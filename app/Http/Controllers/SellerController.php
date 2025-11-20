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


class SellerController extends Controller
{
    public function viewMainDashboard(){
        $user = Auth::user()->sellerStore;
        $id = $user->id;
        $total_order = DB::table('Order_Items as oi')
            ->join('Product as p', 'oi.ProductCode', '=', 'p.Code')
            ->where('p.StoreID', $storeId)
            ->distinct('oi.OrdersID')
            ->count('oi.OrdersID');
        
        $monthly_revenue = $monthlyRevenue = DB::table('Order_Items as oi')
            ->join('Product as p', 'oi.ProductCode', '=', 'p.Code')
            ->join('Orders as o', 'oi.OrdersID', '=', 'o.ID')
            ->where('p.StoreID', $storeId)
            ->whereMonth('o.OrderDate', $now->month)
            ->whereYear('o.OrderDate', $now->year)
            ->selectRaw('COALESCE(SUM(oi.Price * oi.Qty), 0) as revenue')
            ->value('revenue');;

        $activeProducts = DB::table('Product')
            ->where('StoreID', $storeId)
            ->where('QtyInStock', '>', 0)
            ->count();
        
        $storeRating = DB::table('Store_Rating')
            ->where('StoreID', $storeId)
            ->avg('Rating');
        $storeRating = $storeRating ? number_format($storeRating, 1) : 0;
        $sales = DB::table('Order_Items as oi')
            ->join('Product as p', 'oi.ProductCode', '=', 'p.Code')
            ->join('Orders as o', 'oi.OrdersID', '=', 'o.ID')
            ->where('p.StoreID', $storeId)
            ->selectRaw('
                MONTH(o.OrderDate) as month,
                YEAR(o.OrderDate) as year,
                SUM(oi.Price * oi.Qty) as revenue
            ')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // convert to format chart.js
        $labels = [];
        $data = [];
        foreach ($sales as $row) {
            $labels[] = date("M Y", strtotime("$row->year-$row->month-01")); // contoh: Jan 2025
            $data[] = $row->revenue;
        }
        
        return view('seller.dashboard', compact('total_order', 'monthly_revenue', 'activeProducts', 'storeRating', 'labels', 'data'));
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

    public function viewReecentOrder(){
        $seller = auth()->user()->sellerStore;
        $orders  = Order::whereHas('items.product', function($q) use ($seller){
            $q->where('seller_store', $seller->id);
        })->with(['items.product', 'user'])
        ->latest('order_date')
        ->paginate(10);
        return view('seller.recent-order', compact('orders'));
    }

    public function generateTracking(Order $order, $courier){
        // contoh sederhana: gabungkan ID order dengan kode kurir dan timestamp acak
        return strtoupper($courier) . '-' . $order->id . '-' . strtoupper(Str::random(6));
    }

    public function estimateArrival($courier){
        $courier_data = Shipping::where('id', $courier)->first();
        $minDays = ;
        $maxDays = 7;
        return now()->addDays(rand($minDays, $maxDays));
    }

    public function ship(Request $request, Order $order){
        $this->authorize('ship', $order); // akan pakai OrderPolicy@ship

        $order->shipping_date = now();
        $order->tracking_number = $this->generateTracking($order, $request->courier);
        $order->courier = $request->courier;
        $order->estimated_arrival = $this->estimateArrival($request->courier);
        $order->status = 'shipped';
        $order->save();
        // notify customer, create shipment log, dll.
        return back()->with('success', 'Order marked as shipped.');
    }


    public function updateStatus(Request $request, $id){
        $request->validate(['status'=> 'required|string']);
        $order = Order::findOrFail($id);
        $order->update(['status'=> $request->status]);
        return back()->with('message', 'Berhasil merubah status');
    }

    public function viewProd(){
        $seller = auth()->user()->sellerStore;
        $product = Product::where('seller_store_id', '=', $seller->id)
        ->paginate(10);
        return view('', compact('product'));
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
            'description'  => 'nullable|string|max:5000',
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
            'qty' => 'required|integer|min:1',
            'color' => 'nullable|string|max:50',
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'video' => 'nullable|mimes:mp4,mov,avi,wmv|max:200000',
            'weight' => 'nullable|integer|min:0',
            'diameter' => 'nullable|numeric|min:0',
            'variants' => 'nullable|array',
            'variants.*.color' => 'nullable|string|max:100',
            'variants.*.size'  => 'nullable|string|max:50',
            'variants.*.price' => 'nullable|numeric|min:0',
            'variants.*.stock' => 'nullable|integer|min:0',
        ];

        $validator = Validator::make($request->all(), $rules);


        $validator->after(function ($v) use ($request) {
            $variants = $request->input('variants', []);
            $seen = [];

            foreach ($variants as $i => $row) {
                $color = isset($row['color']) ? trim($row['color']) : '';
                $size  = isset($row['size'])  ? trim($row['size'])  : '';
                $price = $row['price'] ?? null;
                $stock = $row['stock'] ?? null;

                // skip completely empty rows
                if ($color === '' && $size === '' && ($price === null || $price === '')) {
                    continue;
                }

                // if row used, color & size required
                if ($color === '' || $size === '') {
                    $v->errors()->add("variants.$i.color", "Kolom warna dan ukuran harus diisi jika baris variant dipakai.");
                }

                // check duplicate combination color+size
                $key = strtolower($color) . '||' . strtolower($size);
                if (!empty($color) && !empty($size)) {
                    if (isset($seen[$key])) {
                        $v->errors()->add("variants.$i.size", "Kombinasi warna & ukuran duplikat dengan baris #{$seen[$key]}.");
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

        $imagePath = null;
        $videoPath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        if ($request->hasFile('video')) {
            $videoPath = $request->file('video')->store('videos', 'public');
        }


        $sku = $request->input('PSKU');
        if (!$sku) {

            $sku = 'GG-S' . ($seller->id ?? '0') . '-' . time() . '-' . strtoupper(Str::random(4));
        } else {
            $orig = $sku;
            $i = 1;
            while (Product::where('sku', $sku)->exists()) {
                $sku = $orig . '-' . $i++;
            }
        }

        // normalize variants: buang baris kosong dan cast tipe
        $variantsRaw = $request->input('variants', []);
        $normalizedVariants = [];
        foreach ($variantsRaw as $row) {
            $color = isset($row['color']) ? trim($row['color']) : '';
            $size  = isset($row['size'])  ? trim($row['size'])  : '';
            $priceVar = $row['price'] ?? null;
            $stockVar = $row['stock'] ?? null;

            if ($color === '' && $size === '' && ($priceVar === null || $priceVar === '')) {
                continue;
            }

            $normalizedVariants[] = [
                'color' => $color,
                'size'  => $size,
                'price' => $priceVar !== null && $priceVar !== '' ? floatval($priceVar) : null,
                'stock' => $stockVar !== null && $stockVar !== '' ? intval($stockVar) : null,
            ];
        }

        // create product
        $product = Product::create([
            'name' => $request->input('product_name'),
            'category_id' => $request->input('category_id'),
            'seller_store_id' => $seller->id,
            'brand' => $request->input('brand'),
            'price' => $request->input('price'),
            'qty' => $request->input('qty'),
            'description' => $request->input('description'),
            'discount' => $request->input('discount') ?? 0,
            'color' => $request->input('color'),
            'images' => $imagePath,
            'sku' => $sku,
            'video' => $videoPath,
            'weight' => $request->input('weight'),
            'diameter' => $request->input('diameter'),
            'variants' => $normalizedVariants,
        ]);

        return redirect()->back()->with('message', 'Berhasil menambahkan product');
    }

    public function deleteProd(Product $product){
         $seller = auth()->user()->sellerStore;
         abort_unless($product->seller_store_id == $seller->id, 403, 'Anda tidak memiliki akses untuk mengubah product ini!');
         $product->delete();
         return back()->with('message', 'Berhasil menghapus product.');
    }

    public function updateStock(Request $request, Product $product){
        $seller = auth()->user()->sellerStore;
        abort_unless($product->seller_store_id == $seller->id, 403, 'Anda tidak memiliki akses untuk mengubah product ini!');
        $validated = $request->validate(['stock' => 'required|integer|min:0']);
        $product->update(['stock'=> $validated['stock']]);
        return back()->with('message', 'Stock berhasil diperbaharui');
    }

    public function viewReviewProduct(){
        $seller = auth()->user()->sellerStore;
        $review = ProductReview::whereHas('product', fn($q)=> $q->where('seller_store_id', $user->id))
        ->with(['product', 'user'])->latest()->paginate(10);
        return view('', compact('review'));
    }

    public function viewAnalyticsReview(){
        $seller = auth()->user()->sellerStore;
        $total_revenue_this_month = OrderItem::whereHas('product', fn($q) => $q->where('seller_store_id', $seller->id))
        ->whereHas('order', fn($q)=>$q->where('status', 'paid')->whereMonth('order_date', now()->month()))
        ->sum(DB::raw('price * qty'));
        $total_order = Order::whereHas('items.product', fn($q)=> $q->where('seller_store_id', $seller->id))
        ->where('status', 'paid')->count();
        $product_sold = OrderItem::whereHas('product', fn($q)=> $q->where('seller_store_id', $seller->id))->sum('qty');
        $total_customers = User::whereHas('orders.items.product', fn($q)=>$q->where('seller_store_id', $seller->id))->distinct()->count();
        $best_selliing_prod = OrderItem::whereHas('product' , fn($q)=>$q->where('seller_store_id', $seller->id))->select('product_id', DB::raw('SUM(qty) as sold_items'))->groupBy('product_id')->with(['product:id,name,images'])->take(5)->get();
        return view('', compact('total_revenue_this_month', 'total_order' ,'product_sold', 'total_customer', 'best_selliing_prod'));
    }
}
