<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SellerStore;
use App\models\Order;
use App\Models\User;
use App\Models\ProductReview;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Category;
use App\Models\Shipping;
use App\Models\ShippingOrder;
use Illuminate\Support\Facades\Validator;
use App\Models\StoreReview;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class SellerController extends Controller
{
    public function viewMainDashboard(){
        $user = Auth::user()->sellerStore;
        $storeId = $user->id;
        $total_order = DB::table('Order_Items as oi')
            ->join('Products as p', 'oi.product_id', '=', 'p.id')
            ->where('p.seller_store_id', $storeId)
            ->distinct('oi.id')
            ->count('oi.id');
        
        $monthlyRevenue = DB::table('Order_Items as oi')
            ->join('Products as p', 'oi.product_id', '=', 'p.id')
            ->join('Orders as o', 'oi.order_id', '=', 'o.id')
            ->where('p.seller_store_id', $storeId)
            ->whereMonth('o.ordered_at', now()->month)
            ->whereYear('o.ordered_at', now()->year)
            ->selectRaw('COALESCE(SUM(oi.subtotal), 0) as revenue')
            ->value('revenue');

        $activeProducts = DB::table('Products')
            ->where('seller_store_id', $storeId)
            ->where('stock', '>', 0)
            ->count();
        
        $storeRating = DB::table('store_reviews')
            ->where('seller_store_id', $storeId)
            ->avg('rating');

        $storeRating = $storeRating ? number_format($storeRating, 1) : 0;
        $sales = DB::table('Order_Items as oi')
            ->join('Products as p', 'oi.product_id', '=', 'p.id')
            ->join('Orders as o', 'oi.order_id', '=', 'o.id')
            ->where('p.seller_store_id', $storeId)
            ->selectRaw('
                MONTH(o.ordered_at) as month,
                YEAR(o.ordered_at) as year,
                SUM(oi.subtotal) as revenue
            ')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // ====== FORCE LABEL 1–12 ======
        $labels = [];
        $data = [];

        // buat label bulan Januari–Desember
        for ($i = 1; $i <= 12; $i++) {
            $labels[] = date("M", strtotime("2024-$i-01")); // kalo mau tahun dinamis bisa diganti
            $data[$i] = 0; // default revenue = 0
        }

        // isi revenue dari hasil query
        foreach ($sales as $row) {
            $data[$row->month] = $row->revenue;
        }

        // rapikan data (Chart.js butuh array indexed 0-n)
        $data = array_values($data);
        
        $storelogo = SellerStore::where('user_id', Auth::id())->value('store_logo');

        return view('seller.seller-dashboard', compact('total_order', 'storelogo', 'monthlyRevenue', 'activeProducts', 'storeRating', 'labels', 'data'));
    }

    public function store(Request $request){
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $sellerStore = $user->sellerStore;
        $request->validate([
            'store_name' => 'required|string|max:255|unique:seller_profiles,store_name',
            'store_phone' => 'required|string|max:20',
            'store_address' => 'required|string|max:255',   
            'logo' => 'required|image|mimes:jpg,jpeg,png|max:1024',
            'banner' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'province' => 'required|string|max:50',
            'city' => 'required|string|max:50',
            'address'=> 'required|string|max:50',
            'store_description' => 'nullable|string|max:2000',
            'open_time' => 'nullable|date_format:H:i',
            'close_time' => 'nullable|date_format:H:i',
        ]);

        $data  = [
            'user_id' => $user->id,
            'store_name' => $request->input('store_name'),
            'store_phone' => $request->input('store_phone') ?? $request->input('phone'),
            'store_address' => $request->input('store_address') ?? $request->input('address'),
            'province' => $request->input('province'),
            'city' => $request->input('city'),
            'description' => $request->input('store_description'),
            'open_time' => $request->input('open_time'),
            'close_time' => $request->input('close_time'),
            'status' => 'active',
        ];
        if($request->hasFile('logo')){
            $data['logo'] = $request->file('logo')->store('store_logos', 'public');
        }
        if($request->hasFile('banner')){
            $data['banner'] = $request->file('banner')->store('store_banners', 'public');
        }

        if (! $sellerStore) {
                if (! $user->is_seller) {
                    $user->update([
                        'role'      => 'seller',
                        'is_seller' => true,
                    ]);
                SellerStore::create($data);

                return redirect()
                ->route('seller.dashboard')
                ->with('message', 'Selamat! Toko Anda berhasil dibuat.');
            }
        }

            $sellerStore->update($data);

        return redirect()
            ->route('seller.dashboard')
            ->with('message', 'Toko Anda berhasil diaktifkan / diperbarui.');

    }

    public function viewRecentOrder(Request $request)
    {
        $seller = auth()->user()->sellerStore;
        $image = $seller->store_logo;

        $validStatuses = ['pending','processing','shipped','delivered','completed','cancelled','refunded'];
        $status = $request->query('status');

        $query = OrderItem::select('order_items.*')
        ->join('products', 'order_items.product_id', '=', 'products.id')
        ->join('orders', 'order_items.order_id', '=', 'orders.id')
        ->join('shipping_orders', 'orders.id', '=', 'shipping_orders.order_id')
        ->where('products.seller_store_id', $seller->id)
        ->with(['product', 'order.shiporder', 'order.user']);


        // filter by order.status jika diberikan dan valid
        if (! empty($status) && in_array(strtolower($status), $validStatuses, true)) {
            $lower = strtolower($status);
            $query->whereHas('order', function($q) use ($lower) {
                $q->where('status', $lower);
            });
        }

        $orders = $query->orderBy('created_at', 'desc')
                        ->paginate(20)
                        ->withQueryString(); // penting supaya ?status=... bertahan saat pagination

        return view('seller.recent-order', compact('orders', 'image'));
    }

    public function prodJson($id){
         $order = Order::with(['items','shippingCourier']) // sesuaikan relasi
            ->findOrFail($id);

        // contoh bentuk data yang ingin kita kirim
        $data = [
            'id' => $order->id,
            'status' => $order->status,
            'total_amount' => $order->total_amount, // angka
            'currency' => 'IDR',
            'courier' => $order->courier_name, // string atau null
            'tracking_number' => $order->tracking_number,
            'shipping_cost' => $order->shipping_cost,
            'shipping_date' => $order->shipping_date ? $order->shipping_date->toDateString() : null,
            'eta_text' => $order->eta_text ?? null,
            'available_couriers' => [
                ['name' => 'JNE'],
                ['name' => 'J&T'],
                ['name' => 'SiCepat'],
                // atau load dari DB/config
            ],
            'items' => $order->items->map(function($it){
                return [
                    'sku' => $it->sku,
                    'name' => $it->name,
                    'qty' => $it->quantity,
                    'price' => $it->price,
                ];
            }),
        ];

        return response()->json($data);
    }

    public function update(Request $request, $id){
        $order = Order::findOrFail($id);

        $validated = $request->validate([
            'status' => ['nullable','string'],
            'courier' => ['nullable','string'],
            'tracking_number' => ['nullable','string'],
            'shipping_cost' => ['nullable','numeric'],
            'shipping_date' => ['nullable','date'],
            'eta_text' => ['nullable','string'],
        ]);

        $order->status = $validated['status'] ?? $order->status;
        $order->courier_name = $validated['courier'] ?? $order->courier_name;
        $order->tracking_number = $validated['tracking_number'] ?? $order->tracking_number;
        $order->shipping_cost = $validated['shipping_cost'] ?? $order->shipping_cost;
        $order->shipping_date = $validated['shipping_date'] ?? $order->shipping_date;
        $order->eta_text = $validated['eta_text'] ?? $order->eta_text;

        $order->save();

        return response()->json(['success' => true, 'message' => 'Order updated', 'order' => $order]);
    }

    public function generateTracking(Order $order, $courier){
        // contoh sederhana: gabungkan ID order dengan kode kurir dan timestamp acak
        return strtoupper($courier) . '-' . $order->id . '-' . strtoupper(Str::random(6));
    }

    public function updateStatus(Request $request, $id){
        $request->validate(['status'=> 'required|string']);
        $order = Order::findOrFail($id);
        $order->update(['status'=> $request->status]);
        return back()->with('message', 'Berhasil merubah status');
    }

    public function viewProd(Request $request){
        $seller = auth()->user()->sellerStore;

        $storelogo = $seller->store_logo;
        $query = Product::where('seller_store_id', $seller->id);

        // Jika ada query ?search=...
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'LIKE', "%{$search}%");
            // Sesuaikan kolom jika perlu: name, description, sku, dll.
        }

        $product = $query->paginate(10);

        return view('seller.product', compact('product', 'storelogo'));
    }

    public function viewAddProductForm(){
        $categories = Category::all();
        $shippings = Shipping::all();
        return view('seller.add-new-product', compact('categories', 'shippings'));
    }

    public function addProduct(Request $request){
    $seller = auth()->user()->sellerStore;

    $rules = [
        'product_name' => 'required|string|max:255',
        'category_id'  => 'required|exists:categories,id',
        'brand'        => 'required|string|max:100',
        'description'  => 'required|string|max:5000',
        'price'        => 'required|numeric|min:0',
        'PSKU'         => 'nullable|string|max:100',
        'discount'     => [
            'nullable',
            'numeric',
            'min:0',
            'max:100',
            function($attribute, $value, $fail) use ($request) {
                if ($value && $value > 0 && ($request->price === null || $request->price <= 0)) {
                    $fail('Diskon tidak bisa diterapkan tanpa harga produk.');
                }
            }
        ],
        'qty' => 'required|integer|min:0|max:10000',
        'color' => 'nullable|string|max:50',
        'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        'video' => 'nullable|mimes:mp4,mov,avi,wmv|max:200000',
        'weight' => 'nullable|integer|min:0',
        'diameter' => 'nullable|numeric|min:0',

        // variants boleh pakai struktur name+value (UI sebelumnya) atau name+color
        'variants' => 'nullable|array',
        'variants.*.name'  => 'nullable|string|max:100',
        'variants.*.value' => 'nullable|string|max:100',
        'variants.*.color' => 'nullable|string|max:100',
        'variants.*.price' => 'nullable|numeric|min:0',
        'variants.*.stock' => 'nullable|integer|min:0',
    ];

    $validator = Validator::make($request->all(), $rules);

    // additional checks (duplicates, required-combination, numeric)
    $validator->after(function ($v) use ($request) {
        $variants = $request->input('variants', []);
        $seen = [];

        foreach ($variants as $i => $row) {
            $name  = trim($row['name'] ?? '');
            $value = trim($row['value'] ?? '');
            $color = trim($row['color'] ?? '');
            $price = $row['price'] ?? null;
            $stock = $row['stock'] ?? null;

            // skip completely empty rows
            if ($name === '' && $value === '' && $color === '' && ($price === null || $price === '')) {
                continue;
            }

            // require name and at least one of (value or color)
            if ($name === '' || ($value === '' && $color === '')) {
                $v->errors()->add("variants.$i.name", "Nama variant dan value/warna harus diisi jika baris variant dipakai.");
            }

            // prepare duplicate key (normalize to lower-case)
            $keyPart = $value !== '' ? $value : $color;
            $key = strtolower($name) . '||' . strtolower($keyPart);

            if (!empty($name) && !empty($keyPart)) {
                if (isset($seen[$key])) {
                    $v->errors()->add("variants.$i.name", "Kombinasi variant duplikat dengan baris #{$seen[$key]}.");
                } else {
                    $seen[$key] = $i + 1;
                }
            }

            // price numeric check (defensive)
            if (!is_null($price) && $price !== '' && !is_numeric($price)) {
                $v->errors()->add("variants.$i.price", "Harga variant harus berupa angka.");
            }
        }
    });

    if ($validator->fails()) {
        return back()->withErrors($validator)->withInput();
    }

    // handle files
    $imagePath = null;
    $videoPath = null;

    if ($request->hasFile('image')) {
        $imagePath = $request->file('image')->store('products', 'public'); // returns path like products/xxx.jpg
    }

    if ($request->hasFile('video')) {
        $videoPath = $request->file('video')->store('videos', 'public');
    }

    // SKU generation + uniqueness check (check both sku and SKU columns to be safe)
    $sku = $request->input('PSKU');
    if (!$sku) {
        $sku = 'GG-S' . ($seller->id ?? '0') . '-' . time() . '-' . strtoupper(\Illuminate\Support\Str::random(4));
    } else {
        $orig = $sku;
        $i = 1;
        while (Product::where('sku', $sku)->orWhere('SKU', $sku)->exists()) {
            $sku = $orig . '-' . $i++;
        }
    }

    // normalize variants: produce consistent array of {name, value, price, stock}
    $variantsRaw = $request->input('variants', []);
    $normalizedVariants = [];
    foreach ($variantsRaw as $row) {
        $name  = isset($row['name']) ? trim($row['name']) : '';
        $value = isset($row['value']) ? trim($row['value']) : '';
        $color = isset($row['color']) ? trim($row['color']) : '';
        $priceVar = $row['price'] ?? null;
        $stockVar = $row['stock'] ?? null;

        // skip empty rows
        if ($name === '' && $value === '' && $color === '' && ($priceVar === null || $priceVar === '')) {
            continue;
        }

        // prefer 'value' field; if not present use 'color' as value
        $finalValue = $value !== '' ? $value : $color;

        $normalizedVariants[] = [
            'name'  => $name,
            'value' => $finalValue,
            'price' => ($priceVar !== null && $priceVar !== '') ? (float) $priceVar : null,
            'stock' => ($stockVar !== null && $stockVar !== '') ? (int) $stockVar : null,
        ];
    }

    // prepare images: store as JSON array (so consistent with seeder)
    $imagesForDb = null;
    if ($imagePath) {
        $imagesForDb = json_encode([$imagePath]);
    }

    // create product (ensure Product model has 'variants' in $fillable or use casts)
    $product = Product::create([
        'name' => $request->input('product_name'),
        'category_id' => $request->input('category_id'),
        'seller_store_id' => $seller->id,
        'brand' => $request->input('brand'),
        'original_price' => $request->input('price'),
        'stock' => $request->input('qty'),
        'description' => $request->input('description'),
        'discount_price' => 0,
        'discount_percentage' => $request->input('discount') ?? 0,
        'color' => $request->input('color'),
        'images' => $imagesForDb,
        // use proper SKU column name (migration used "SKU" but many apps use lowercase 'sku')
        // Laravel/MySQL usually case-insensitive, but keep it consistent
        'SKU'  => $sku,
        'video' => $videoPath,
        'weight' => $request->input('weight'),
        'diameter' => $request->input('diameter'),
        // save variants as JSON (safe): if your model has casts, you may pass array directly
        'variants' => !empty($normalizedVariants) ? json_encode($normalizedVariants) : null,
    ]);

    // OPTIONAL DEBUG (uncomment when needed)
    // dd('MODEL ATTRS', $product->getAttributes(), 'DB ROW', \DB::table('products')->where('id', $product->id)->first());

        return back()->with('success', 'Produk berhasil ditambahkan.');
    }


    public function deleteProd(Product $product){
         $seller = auth()->user()->sellerStore;
         abort_unless($product->seller_store_id == $seller->id, 403, 'Anda tidak memiliki akses untuk mengubah product ini!');
         $product->delete();
         return back()->with('success', 'Berhasil menghapus product.');
    }

    public function updateStock(Request $request, Product $product){
        $seller = auth()->user()->sellerStore;
        abort_unless($product->seller_store_id == $seller->id, 403, 'Anda tidak memiliki akses untuk mengubah product ini!');
        $validated = $request->validate(['stock' => 'required|integer|min:0']);
        $product->update(['stock'=> $validated['stock']]);
        return back()->with('success', 'Stock berhasil diperbaharui');
    }


    public function viewAnalyticsReview(Request $request)
    {
        $seller = auth()->user()->sellerStore;
        if (!$seller) {
            abort(403, 'Seller store not found');
        }
        $sellerId = $seller->id;

        // total revenue (sum price * qty) for completed orders that include this seller's products
        $total_revenue = \DB::table('order_items')
            ->selectRaw('COALESCE(SUM(price * qty), 0) as total')
            ->whereExists(function ($q) use ($sellerId) {
                $q->select(\DB::raw(1))
                ->from('products')
                ->whereColumn('products.id', 'order_items.product_id')
                ->where('products.seller_store_id', $sellerId);
            })
            ->whereExists(function ($q) {
                $q->select(\DB::raw(1))
                ->from('orders')
                ->whereColumn('orders.id', 'order_items.order_id')
                ->where('orders.status', 'completed');
            })
            ->value('total');

        // total orders that include this seller's products (exclude cancelled)
        $total_order = \App\Models\Order::whereExists(function ($q) use ($sellerId) {
            $q->select(\DB::raw(1))
            ->from('order_items')
            ->whereColumn('order_items.order_id', 'orders.id')
            ->whereExists(function ($qq) use ($sellerId) {
                $qq->select(\DB::raw(1))
                    ->from('products')
                    ->whereColumn('products.id', 'order_items.product_id')
                    ->where('products.seller_store_id', $sellerId);
            });
        })->where('status', '!=', 'cancelled')->count();

        // product_sold: sum qty from order_items for this seller where order is completed
        $product_sold = \App\Models\OrderItem::whereHas('product', function ($q) use ($sellerId) {
                $q->where('seller_store_id', $sellerId);
            })
            ->whereHas('order', function ($q) {
                $q->where('status', 'completed');
            })
            ->sum('qty');

        // total distinct customers who bought (completed or delivered)
        $total_customers = \App\Models\Order::whereExists(function ($q) use ($sellerId) {
            $q->select(\DB::raw(1))
            ->from('order_items')
            ->whereColumn('order_items.order_id', 'orders.id')
            ->whereExists(function ($qq) use ($sellerId) {
                $qq->select(\DB::raw(1))
                    ->from('products')
                    ->whereColumn('products.id', 'order_items.product_id')
                    ->where('products.seller_store_id', $sellerId);
            });
        })
        ->whereIn('status', ['completed'])
        ->distinct()
        ->count('user_id');

        // best selling products for this seller (top 5)
        $best_selliing_prod = \App\Models\OrderItem::whereHas('product', function ($q) use ($sellerId) {
                $q->where('seller_store_id', $sellerId);
            })
            ->select('product_id', DB::raw('SUM(qty) as sold_items'))
            ->groupBy('product_id')
            ->with(['product:id,name'])
            ->orderByDesc('sold_items')
            ->take(5)
            ->get();

        // top customers (by total spent on this seller's products) - safer using order_items join
        $topCustomers = \DB::table('order_items')
            ->select('orders.user_id', DB::raw('SUM(order_items.price * order_items.qty) as total_spent'), DB::raw('COUNT(DISTINCT orders.id) as total_orders'))
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('products.seller_store_id', $sellerId)
            ->where('orders.status', 'completed')
            ->groupBy('orders.user_id')
            ->orderByDesc('total_spent')
            ->take(6)
            ->get()
            ->map(function($r){
                // load user info
                $user = \App\Models\User::select('id','name')->find($r->user_id);
                return (object) [
                    'user_id' => $r->user_id,
                    'total_spent' => $r->total_spent,
                    'total_orders' => $r->total_orders,
                    'user' => $user
                ];
            });

        $months = [];
        $now = Carbon::now()->startOfMonth();
        for ($i = 0; $i < 12; $i++) {
            $date = $now->copy()->subMonths($i);
            $value = $date->format('Y-m');
            $label = $date->format('M Y');
            $months[$value] = $label;
        }

        // default month
        $month = $request->query('month', now()->format('Y-m'));

        // pass variables — note names must match actual vars
        return view('seller.seller-analytics', compact(
            'total_revenue',
            'total_order',
            'product_sold',
            'total_customers',
            'best_selliing_prod',
            'topCustomers',
            'month',
            'months'
        ));

    }

    public function data(Request $request)
    {
        $seller = auth()->user()->sellerStore;
        if (!$seller) {
            return response()->json(['error' => 'No seller store'], 403);
        }
        $sellerId = $seller->id;

        // month param format: 'YYYY-MM', default now
        $month = $request->query('month', now()->format('Y-m'));
        try {
            $d = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        } catch (\Throwable $e) {
            $d = now()->startOfMonth();
            $month = $d->format('Y-m');
        }

        $start = $d->copy()->startOfMonth();
        $end   = $d->copy()->endOfMonth();

        // build day labels for the month: "01","02",...
        $daysInMonth = (int)$start->daysInMonth;
        $labels = [];
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $labels[] = str_pad($i, 2, '0', STR_PAD_LEFT);
        }

        // Query: distinct orders per day that include this seller's products
        $ordersPerDay = DB::table('orders')
            ->selectRaw('DATE(orders.created_at) as day, COUNT(DISTINCT orders.id) as cnt')
            ->join('order_items','order_items.order_id','orders.id')
            ->join('products','order_items.product_id','products.id')
            ->where('products.seller_store_id', $sellerId)
            ->whereBetween('orders.created_at', [$start->toDateTimeString(), $end->toDateTimeString()])
            ->groupByRaw('DATE(orders.created_at)')
            ->get()
            ->keyBy(function($r){ return (new \Carbon\Carbon($r->day))->format('d'); });

        // build data aligned with labels
        $data = [];
        foreach ($labels as $lab) {
            $data[] = isset($ordersPerDay[$lab]) ? (int)$ordersPerDay[$lab]->cnt : 0;
        }

        // summary: total orders this month (distinct orders involving this seller)
        $totalOrders = array_sum($data);


        $payload = [
            'labels' => $labels,
            'data'   => $data,
            'summary' => [
                'total_orders' => $totalOrders,

            ],
        ];

        return response()->json($payload);
    }

    public function feedback(Request $request){
        // pastikan user punya seller store
        $user = $request->user();
        if (! $user || ! $user->sellerStore) {
            // sesuaikan behaviour (redirect atau 404). Saya kembalikan view kosong.
            return view('seller.seller-inbox', [
                'items' => new LengthAwarePaginator([], 0, 10),
                'total' => 0,
            ]);
        }

        $seller = $user->sellerStore;
        $q = $request->input('q', null);
        $filter = $request->input('filter', 'all'); // expected: all | review | message
        $perPage = 10;

        /**
         * NOTE:
         * - Saya asumsikan ProductReview memiliki relasi `product` dan `user`, serta kolom `message` (isi teks)
         * - Saya asumsikan StoreReview memiliki relasi `user`, serta kolom `message`
         * - Jika nama kolom berbeda (mis. 'comment' / 'content'), ganti 'message' di bawah sesuai schema Anda.
         */

        // Base queries
        $productQuery = ProductReview::whereHas('product', function ($query) use ($seller) {
            $query->where('seller_store_id', $seller->id);
        })->with(['product', 'user']);

        $storeQuery = StoreReview::where('seller_store_id', $seller->id)->with(['user']);

        // Apply search term
        if ($q) {
            $like = "%{$q}%";
            $productQuery->where(function ($qq) use ($like) {
                $qq->where('comment', 'like', $like)
                   ->orWhereHas('user', function ($u) use ($like) {
                       $u->where('name', 'like', $like);
                   })
                   ->orWhereHas('product', function ($p) use ($like) {
                       $p->where('name', 'like', $like);
                   });
            });

            $storeQuery->where(function ($qq) use ($like) {
                $qq->where('comment', 'like', $like)
                   ->orWhereHas('user', function ($u) use ($like) {
                       $u->where('name', 'like', $like);
                   });
            });
        }

        // Apply filter
        // Assumption: filter 'review' means items that have rating (adjust if your model different)
        if ($filter === 'review') {
            // only items that have a rating (if your models don't have rating, change logic)
            $productQuery->whereNotNull('rating');
            $storeQuery->whereNotNull('rating');
        } elseif ($filter === 'comment') {
            // only items that are messages (no rating)
            $productQuery->whereNull('rating');
            $storeQuery->whereNull('rating');
        }

        // Retrieve collections (we'll merge and sort in PHP)
        $productItems = $productQuery->get()->map(function ($item) {
            return (object) [
                'type' => 'product',
                'model' => $item,
                'created_at' => $item->created_at,
            ];
        });

        $storeItems = $storeQuery->get()->map(function ($item) {
            return (object) [
                'type' => 'store',
                'model' => $item,
                'created_at' => $item->created_at,
            ];
        });

        // Merge & sort descending by created_at
        $merged = $productItems->merge($storeItems)->sortByDesc('created_at')->values();

        // Manual paginate the merged collection
        $page = Paginator::resolveCurrentPage('page') ?: 1;
        $total = $merged->count();
        $itemsForCurrentPage = $merged->slice(($page - 1) * $perPage, $perPage)->all();

        $paginated = new LengthAwarePaginator(
            $itemsForCurrentPage,
            $total,
            $perPage,
            $page,
            [
                'path' => Paginator::resolveCurrentPath(),
                'query' => $request->query(), // mempertahankan q & filter di link pagination
            ]
        );

        // Jika request AJAX -> kembalikan partial HTML (rendered view)
        if ($request->ajax()) {
            return view('seller.partials.inbox-list', ['items' => $paginated])->render();
        }

        // Normal full page render
        return view('seller.seller-inbox', [
            'items' => $paginated,
            'total' => $total,
        ]);
    }
    
    
    public function replyFeedback(Request $request, ProductReview $review){
        $seller = auth()->user()->sellerStore;
        abort_unless($review->product->seller_store_id == $seller->id, 403, 'Anda tidak memiliki akses untuk membalas feedback ini!');
        $validated = $request->validate(['reply' => 'required|string|max:2000']);
        // Simpan balasan ke database atau kirim email ke user
        // Contoh: $review->update(['seller_reply' => $validated['reply']]);
        return back()->with('message', 'Balasan berhasil dikirim.');
    }

    public function shipData($id){
    $order = Order::with(['items.product','user'])->findOrFail($id);
    $couriers = Shipping::active()
        ->select('name','service_type','base_rate','per_kg','min_delivery_days','max_delivery_days')
        ->orderBy('name')
        ->get();

    // Hitung weight
    $weight = $order->items->sum(function($it){
        $w = $it->product->weight_kg ?? 0;
        $qty = $it->qty ?? $it->quantity ?? 0;
        return $w * $qty;
    });

    return response()->json([
        'order' => $order,
        'couriers' => $couriers,
        'weight' => $weight,
    ]);
}

    public function ship(Request $request, $id)
    {
        // validasi
        $data = $request->validate([
            'courier' => 'required|string|max:255', // name of courier/service
            'tracking_number' => 'nullable|string|max:120',
            'shipping_cost' => 'required|numeric|min:0',
            'eta_end' => 'nullable|date',
        ]);

        try {
            DB::transaction(function() use ($id, $data, &$shipping) {
                $order = Order::findOrFail($id);

                // find shipping service by name (adjust to your data)
                $service = Shipping::where('name', $data['courier'])->first();

                // create or update a single shipping record per order
                $shipping = ShippingOrder::updateOrCreate(
                    ['order_id' => $order->id],
                    [
                        'shipping_id' => $service->id ?? null,
                        'tracking_number' => $data['tracking_number'] ?? null,
                        'shipping_date' => now()->toDateString(),
                        'estimated_arrival_date' => $data['eta_end'] ?? null,
                        'shipping_cost' => $data['shipping_cost'],
                    ]
                );

                // update order status & optional duplicate fields on order
                $order->update([
                    'status' => 'shipped',
                    'shipping_cost' => $shipping->shipping_cost,
                ]);
            });

            // kembalikan data shipping agar UI bisa update tanpa reload
            return response()->json([
                'success' => true,
                'shipping' => $shipping->fresh()->load('shipping')
            ]);
        } catch (\Throwable $e) {
            Log::error('Ship order failed', [
                'order_id' => $id,
                'payload' => $request->all(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate order',
                'error' => $e->getMessage()
            ], 500);
        }
    }


}
