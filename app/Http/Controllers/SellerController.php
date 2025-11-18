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


class SellerController extends Controller
{
    public function viewMainDashboard(){
        return view('seller.dashboard');
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
        $orders  = Order::whereHas('item.product', function($q) use ($seller){
            $q->where('seller_store', $seller->id);
        })->with(['items.product', 'user'])
        ->latest('order_date')
        ->paginate(10);
        return view('', compact('orders'));
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
        return view('seller.add-new-product', compact('categories'));
    }
    public function addProduct(Request $request){
        $seller = auth()->user()->sellerStore;
        $request->validate([
            'product_name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'brand' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:5000',
            'price' => 'required|numeric|min:0',
            'discount' => [
                        'nullable',
                        'numeric',
                        'min:0',
                        'max:100',
                            function($attribute, $value, $fail) use ($request) {
                                if ($value && $value > 0 && $request->price <= 0) {
                                    $fail('Diskon tidak bisa diterapkan tanpa harga produk.');
                                }
                            }
                        ],
            'qty' => 'required|integer|min:1',
            'color' => 'nullable|string|max:50',
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);
        $imagePath = null;
        if($request->hasFile('image')){
            $imagePath = $request->file('image')->store('products', 'public');
        }
        Product::create([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'seller_store_id'  => $seller->id,
            'brand' => $request->brand,
            'price' => $request->price,
            'qty' => $request->qty,
            'description' => $request->description,
            'discount' => $request->discount,
            'color'  => $request->color,
            'images'  => $imagePath,
        ]);
        return back()->with('message', 'Berhasil menambahkan product');
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
