<?php

namespace App\Http\Controllers;

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
use Illuminate\Support\Facades\Validator;
use App\Models\StoreReview;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


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
            ->whereMonth('o.order_date', now()->month)
            ->whereYear('o.order_date', now()->year)
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
                MONTH(o.order_date) as month,
                YEAR(o.order_date) as year,
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

        /** @var \App\Models\User $user */
        $user = Auth::user();

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
        ];
        if($request->hasFile('logo')){
            $data['logo'] = $request->file('logo')->store('store_logos', 'public');
        }
        if($request->hasFile('banner')){
            $data['banner'] = $request->file('banner')->store('store_banners', 'public');
        }
        if(!$user->is_seller) {
            $user->update(['role' => 'seller', 'is_seller' => true]);
            SellerStore::create($data);
            return redirect()->route('seller.dashboard')->with('message', 'Selamat! Toko and berhasil dibuat.');
        }

        $sellerstore = $user->sellerStore;
        if($sellerstore){
            $sellerstore->update($data);
            return redirect()->route('seller.dashboard')->with('message', 'Berhasil memperbarui informasi toko.');
        }

        return back()->withErrors(['store' => 'Tidak dapat membuat atau memperbarui toko. Silakan coba lagi.']);
    }

    public function viewRecentOrder(Request $request)
    {
        $seller = auth()->user()->sellerStore;
        $image = $seller->store_logo;

        $validStatuses = ['pending','processing','shipped','delivered','completed','cancelled','refunded'];
        $status = $request->query('status');

        $query = OrderItem::with(['product','order','order.user'])
            ->whereHas('product', function($q) use ($seller) {
                $q->where('seller_store_id', $seller->id);
            });

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


    public function viewAnalyticsReview(){
        $seller = auth()->user()->sellerStore;
        $total_revenue = OrderItem::whereHas('product', fn($q) => $q->where('seller_store_id', $seller->id))
        ->whereHas('order', fn($q) => $q->where('status', 'completed'))
        ->select(DB::raw('COALESCE(SUM(price * qty), 0) as total'))
        ->value('total');
        $total_order = Order::whereHas('items.product', fn($q)=> $q->where('seller_store_id', $seller->id))
        ->whereNot('status','cancelled')->count();
        $product_sold = OrderItem::whereHas('product', fn($q)=> $q->where('seller_store_id', $seller->id))->sum('qty')->whereHas('order', fn($q)=> $q->where('status', 'completed'));
        $total_customers = Order::whereHas('items.product', fn($q) =>
        $q->where('seller_store_id', $seller->id))
        ->whereIn('status', ['completed', 'delivered']) // filter order valid
        ->distinct()
        ->count('user_id');
        $best_selliing_prod = OrderItem::whereHas('product' , fn($q)=>$q->where('seller_store_id', $seller->id))->select('product_id', DB::raw('SUM(qty) as sold_items'))->groupBy('product_id')->with(['product:id,name'])->take(5)->get();
        $topCustomers = Order::select('user_id', DB::raw('SUM(total_amount) as total_spent'), DB::raw('COUNT(*) as total_orders'))->where('store_id', $seller->id)->groupBy('user_id')->orderByDesc('total_spent')->take(6)->with('user:id,name')->get();

        return view('seller.seller-analytics', compact('total_revenue_this_month', 'total_order' ,'product_sold', 'total_customer', 'best_selliing_prod', 'topCustomers'));
    }

    public function feedback(){
        $seller = auth()->user()->sellerStore;
        $product_feedbacks = ProductReview::whereHas('product', fn($q)=> $q->where('seller_store_id', $seller->id))
        ->with(['product', 'user'])->latest()->paginate(10);
        $store_feedbacks = StoreReview::where('seller_store_id', $seller->id)->with(['user'])->latest()->paginate(10);
        return view('seller.seller-inbox', compact('product_feedbacks', 'store_feedbacks'));
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
        // RETURN JSON ON VALIDATION ERRORS
        try {
            $data = $request->validate([
                'courier' => 'required|string|max:255',
                'tracking_number' => 'nullable|string|max:120',
                'shipping_cost' => 'required|numeric',
                'eta_end' => 'nullable|date',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->validator->errors()], 422);
        }

        try {
            $order = \App\Models\Order::findOrFail($id);

            // optional: ensure seller owns order
            // if ($order->seller_store_id !== auth()->user()->sellerStore->id) {
            //     return response()->json(['success'=>false,'message'=>'Forbidden'],403);
            // }

            DB::transaction(function() use ($order, $data) {
                // sesuaikan nama kolom di DB-mu
                $order->courier = $data['courier'];
                $order->tracking_number = $data['tracking_number'] ?? null;
                $order->shipping_cost = $data['shipping_cost'];
                if (!empty($data['eta_end'])) {
                    $order->estimated_arrival = $data['eta_end'];
                }
                $order->shipping_date = now();
                $order->status = 'shipped';
                $order->save();
            });

            return response()->json(['success' => true, 'order_id' => $order->id]);
        } catch (\Throwable $e) {
            Log::error('ship order failed', [
                'order_id' => $id,
                'payload' => $request->all(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate order',
                'error' => $e->getMessage()
            ], 500);
        }
    }


}
