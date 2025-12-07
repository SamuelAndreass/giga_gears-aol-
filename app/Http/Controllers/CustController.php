<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order; 
// use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Category;

class CustController extends Controller
{
    public function myOrders(Request $request)
    {
       $user = $request->user();
        $search = $request->query('search');
        $status = $request->query('status');
        $perPage = (int) $request->query('per_page', 12);

        // Base query
        $query = Order::with(['items.product'])
                      ->where('user_id', $user->id);

        // Search: by order id or product name
        if ($search) {
            $query->where(function ($q) use ($search) {
                if (is_numeric($search)) {
                    $q->orWhere('id', $search);
                }
                $q->orWhere('id', 'like', "%{$search}%")
                  ->orWhereHas('items.product', function ($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($status) {
            $query->where('status', $status);
        }

        // paginate
        $orders = $query->orderBy('created_at', 'desc')
                        ->paginate($perPage)
                        ->withQueryString();

        $orders->getCollection()->transform(function ($order) {
            // Build a simple products collection from order items
            $products = $order->items->map(function ($item) {
                $product = $item->product;
                return (object) [
                    'id' => $product->id ?? null,
                    'name' => $product->name ?? ($item->product_name ?? 'Unnamed Product'),
                    // adapt this key to where you store the image path
                    'image' => $product->image ?? ($item->product_image ?? null),
                    'qty' => $item->qty ?? 1,
                    'unit_price' => $item->unit_price ?? $item->price ?? null,
                    'subtotal' => $item->subtotal ?? (($item->qty ?? 0) * ($item->unit_price ?? $item->price ?? 0)),
                ];
            });

            $order->products = $products->values()->all();

            // Prefer stored master total (orders.total or orders.total_amount).
            if (isset($order->total) && $order->total !== null) {
                $order->total_price = $order->total;
            } elseif (isset($order->total_amount) && $order->total_amount !== null) {
                $order->total_price = $order->total_amount;
            } else {
                // Fallback: sum of item subtotals (use order_items.subtotal if present)
                $order->total_price = $order->items->sum(function ($i) {
                    return $i->subtotal ?? (($i->qty ?? 0) * ($i->unit_price ?? $i->price ?? 0));
                });
            }

            return $order;
        });

        return view('customer.my_order', compact('orders'));
    }

    public function show(Request $request, $id)
    {
        $userId = $request->user()->id;

        // ambil order milik user, beserta items -> product
        $order = Order::with(['items.product'])
            ->where('id', $id)
            ->where('user_id', $userId)
            ->firstOrFail();

        // Prepare products collection to match Blade expectation:
        // each product must have ->pivot->quantity and ->pivot->price
        $products = $order->items->map(function ($item) {
            $productModel = $item->product;

            // make a lightweight object for product (so we can still call ->image / ->name)
            $displayProduct = (object) [
                'id' => $productModel->id ?? null,
                'name' => $productModel->name ?? ($item->product_name ?? 'Unnamed Product'),
                'image' => $productModel->image ?? ($item->product_image ?? null),
            ];

            // attach pivot-like object used by the blade: pivot->quantity, pivot->price
            $displayProduct->pivot = (object) [
                'quantity' => $item->qty ?? $item->quantity ?? 1,
                'price' => $item->unit_price ?? $item->price ?? $item->price_per_unit ?? 0,
            ];

            return $displayProduct;
        });

        // compute/ensure order-level fields expected by view
        // adjust field names below to match your DB (total, total_amount, subtotal, shipping_fee, etc)
        $order->products = $products; // used in Blade foreach

        // subtotal: prefer stored field on order, else sum of item subtotals (use order_item.subtotal if present)
        if (isset($order->subtotal) && $order->subtotal !== null) {
            $order->subtotal = (float) $order->subtotal;
        } else {
            // try sum item->subtotal, fallback qty*price
            $order->subtotal = $order->items->sum(function ($i) {
                return $i->subtotal ?? (($i->qty ?? 0) * ($i->unit_price ?? $i->price ?? 0));
            });
        }

        // shipping fee — adjust column name if you store differently
        $order->shipping_fee = $order->shipping_fee ?? $order->shipping_cost ?? 0;

        // total price: prefer order.master total column; fallback to subtotal + shipping
        if (isset($order->total) && $order->total !== null) {
            $order->total_price = (float) $order->total;
        } elseif (isset($order->total_amount) && $order->total_amount !== null) {
            $order->total_price = (float) $order->total_amount;
        } else {
            $order->total_price = $order->subtotal + (float) $order->shipping_fee;
            // if you have tax/discount, add/subtract here
        }

        // optional fields used by the blade
        $order->payment_method = $order->payment_method ?? $order->payment_type ?? null;
        $order->shipping_address = $order->shipping_address ?? $order->address ?? null;
        $order->status = $order->status ?? 'Unknown';

        return view('customer.order-detail', compact('order'));
    }

    public function showProducts(Request $request){
        $search = trim((string) $request->query('search', ''));
        $categoryId = $request->query('category');
        $perPage = (int) $request->query('per_page', 12);
        $perPage = $perPage > 0 && $perPage <= 100 ? $perPage : 12;

        // Base query
        $query = Product::with('category')->where('status', 'active');
        // Search
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        // Category filter (if provided)
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        // Order & paginate
        $products = $query->orderBy('created_at', 'desc')
                          ->paginate($perPage)
                          ->withQueryString();

        // load categories for the category select in view
        $categories = Category::orderBy('name')->get();

        return view('customer.products-show', compact('products', 'categories'));
    }
    public function cancel(Request $request, $id)
    {
        $userId = $request->user()->id;

        $order = Order::where('id', $id)
            ->where('user_id', $userId)
            ->whereIn('status', ['Processing', 'Shipped'])
            ->firstOrFail();

        $order->status = 'Cancelled';
        $order->save();

        return back()->with('message', 'Order cancelled successfully.');
    }

    public function showProd($id = 1)
    {
        
        // ambil product utama (throw 404 kalau nggak ada)
        $detailProduct = Product::findOrFail($id);

        // --- proses gambar product utama (sama seperti yang sudah kamu punya) ---
        $raw = $detailProduct->images; // kolom bisa berupa string, json, atau array
        $images = [];

        if (is_array($raw)) {
            $images = $raw;
        } elseif (is_string($raw)) {
            $decoded = json_decode($raw, true);
            $images = (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) ? $decoded : [$raw];
        }

        $imageUrls = collect($images)->map(function ($path) {
            if (!$path) return null;
            if (preg_match('#^https?://#i', $path)) return $path;
            if (Storage::disk('public')->exists($path)) return Storage::url($path);
            if (file_exists(public_path($path))) return asset($path);
            if (file_exists(public_path('images/'.$path))) return asset('images/'.$path);
            return null;
        })->filter()->values()->all();

        $prod_img = $imageUrls[0] ?? asset('images/default-product.png');

        // --- RELATED PRODUCTS: pastikan category_id ada dulu ---
        $relatedProducts = collect(); // default: empty collection

        if (!empty($detailProduct->category_id)) {
            $relatedProducts = Product::where('category_id', $detailProduct->category_id)
                ->where('id', '!=', $detailProduct->id)
                ->take(3)
                ->get();
        }

        // Jika ingin, attachkan image_url pada tiap related product (agar view mudah akses)
        $relatedProducts = $relatedProducts->map(function ($p) {
            // ambil raw images dari related product (bisa gunakan cast di model kalau mau)
            $raw = $p->images;
            $images = [];

            if (is_array($raw)) {
                $images = $raw;
            } elseif (is_string($raw)) {
                $decoded = json_decode($raw, true);
                $images = (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) ? $decoded : [$raw];
            }

            $first = collect($images)->first();
            $imageUrl = null;

            if ($first) {
                if (preg_match('#^https?://#i', $first)) {
                    $imageUrl = $first;
                } elseif (Storage::disk('public')->exists($first)) {
                    $imageUrl = Storage::url($first);
                } elseif (file_exists(public_path($first))) {
                    $imageUrl = asset($first);
                } elseif (file_exists(public_path('images/'.$first))) {
                    $imageUrl = asset('images/'.$first);
                }
            }

            // tambahkan attribute image_url (jika $p adalah model, ini dynamic attribute)
            $p->image_url = $imageUrl ?? asset('images/default-product.png');

            return $p;
        });

        // return ke view
        return view('customer.product-detail', compact('detailProduct', 'prod_img', 'imageUrls', 'relatedProducts'));
        }
}