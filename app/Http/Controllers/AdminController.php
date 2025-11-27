<?php

namespace App\Http\Controllers;

use App\Models\CustomerProfile;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\SellerStore;
use Illuminate\Support\Facades\DB;


class AdminController extends Controller
{
    //
    public function index(){
        $t_customer = User::where('role', 'customer')->count();
        $t_seller = SellerStore::count();
        $t_transaction = Order::where('status', 'completed')->sum('total_amount');
        $recent_order = OrderItem::all()->take(5);

        $customer_growth = User::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as total')
        )
        ->where('role', 'customer')
        ->whereYear('created_at', date('Y'))
        ->groupBy('month')
        ->orderBy('month')
        ->pluck('total', 'month');

        $months = [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
        ];

        $customerData = [];
        foreach ($months as $index => $m) {
            $customerData[] = $customer_growth[$index + 1] ?? 0;
        }

        $revenues = DB::table('orders')
        ->join('products', 'orders.product_id', '=', 'products.id')
        ->join('categories', 'products.category_id', '=', 'categories.id')
        ->select('categories.id as category_id', 'categories.name as category_name', DB::raw('SUM(orders.total_amount) as total_revenue'))
        ->groupBy('categories.id', 'categories.name')
        ->orderByDesc('total_revenue')
        ->get();

        // Total revenue keseluruhan (float)
        $total = (float) $revenues->sum('total_revenue');

        // Arrays untuk chart
        $labels = $revenues->pluck('category_name')->toArray();
        $values = $revenues->pluck('total_revenue')->map(fn($v) => (float) $v)->toArray();

        // Persentase tiap kategori (2 desimal)
        $percentages = collect($values)->map(fn($v) => $total > 0 ? round(($v / $total) * 100, 2) : 0)->toArray();


        return view('admin.dashboard', compact('t_customer', 't_seller', 't_transaction', 't_pending', 'recent_order', 'labels', 'values', 'percentages', 'revenues', 'total', 'customerData', 'months'));
    }

    public function viewUser(){
        $user = CustomerProfile::with('users')->paginate(10);
        return view('admin.customer-view', compact('user'));
    }

    public function dataTransaction(){
        // filter opsional (search, status, date range)
        $q = OrderItem::query();

        //eager load ke relasi terkait
        $q->with([
            'order:id,user_id,order_number,total_amount,payment_method,status,order_date',
            'order.user:id,name,email',
            'product:id,name,price,seller_store_id',
            'product.sellerStore:id,name' // pastikan relasi bernama sellerStore
        ]);

        // Pilih kolom minimal dari order_items agar lebih efisien
        $q->select('id', 'order_id', 'product_id', 'qty', 'price');
        $items = $q->latest('created_at')->paginate(25)->withQueryString();

        return view('admin.data-transaction', compact('items'));
    }

    public function shippingPage(){
        $shippings = Shipping::paginate(5);
        return view('admin.shipping', compact('shippings'));
    }

    public function addShipping(){
        $validated = $request->validate([
            'name'               => 'required|string|max:255',
            'service_type'       => 'required|string|max:50',
            'custom_service'     => 'nullable|string|max:255',
            'base_rate'          => 'required|numeric|min:0',
            'per_kg'             => 'required|numeric|min:0',
            'min_delivery_days'  => 'required|integer|min:1|max:60',
            'max_delivery_days'  => 'required|integer|gte:min_delivery_days|max:60',
            'coverage'           => 'required|string|max:255',
        ]);

        // Jika service_type = custom, custom_service wajib
        if ($validated['service_type'] === 'custom' && empty($validated['custom_service'])) {
            return back()->withErrors(['custom_service' => 'Custom service name is required.'])->withInput();
        }

        Shipping::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Shipping method added successfully!',
        ]);
    }

    public function editShipping(Request $request, Shipping $shipping){
        $validated = $request->validate([
            'name'               => 'required|string|max:255',
            'service_type'       => 'required|string|max:50',
            'custom_service'     => 'nullable|string|max:255',
            'base_rate'          => 'required|numeric|min:0',
            'per_kg'             => 'required|numeric|min:0',
            'min_delivery_days'  => 'required|integer|min:1|max:60',
            'max_delivery_days'  => 'required|integer|gte:min_delivery_days|max:60',
        ]);

        // Jika service = custom maka custom_service wajib
        if ($validated['service_type'] === 'custom' && empty($validated['custom_service'])) {
            return response()->json([
                'success' => false,
                'message' => 'Custom service name is required.',
            ], 422);
        }

        $shipping->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Shipping updated successfully.',
        ]);
    }

    public function userUpdate(Request $request, $id){
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'phone' => 'required|regex:/^(?:\+62|62|0)8[1-9][0-9]{6,10}$/|unique:customer_profiles',
        ]);

        $user = auth()->user();
        $user->update(['name' => $request->name, 'phone' => $request->phone]);
        $user->customerProfile->update(['phone' => $request->phone]);

        return back()->with('success', 'Berhasil merubah data.');
    }

    public function deleteUser($id){
        User::where('id',$id)->delete();
        return back()->with('success', 'Berhasil melakukan penghapusan.');
    }


    public function sellerView(){
        $seller = SellerStore::with('user')->paginate(10);
        return view('admin.seller-view', compact('seller'));
    }

    public function updateSeller(){
    }

    
    public function productIndex(Request $request){
        $query = Product::with(['sellerStore', 'category'])
            ->orderBy('created_at', 'desc');

        // Optional: Search
        if ($search = $request->search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $products = $query->paginate(12);

        return view('admin.products.index', compact('products'));
    }

    public function toggleStatus(Product $product){
        $product->status = $product->status === 'Active' ? 'Inactive' : 'Active';
        $product->save();

        return back()->with('success', 'Product status updated.');
    }

    public function json(Product $product){
        $product->load(['sellerStore', 'category']);

        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'sku' => $product->sku ?? ('PRD-'.$product->id),
            'price' => $product->price,
            'stock' => $product->stock,
            'description' => $product->description,
            'status' => $product->status,
            'image' => $product->image_url ?? null,
            'category' => $product->category->name ?? '-',
            'seller' => $product->sellerStore->name ?? 'Unknown Seller',
            'rating' => number_format($product->rating ?? 0, 1),
        ]);
    }

    public function approveSeller($id){
        $seller = SellerStore::find($id);
        $seller::approve(auth()->user()->id);
        return back()->with('message', 'Berhasil melakukan approve seller');
    }

    public function SuspendSeller($id){
        $seller = SellerStore::find($id);
        $seller::suspend(auth()->user()->id);
        return back()->with('message', 'Berhasil melakukan approve seller');
    }

    public function totalStore(){}

    public function sellerDetail($id){
        $seller = SellerStore::find($id);
        $product_list = Product::where('seller_store_id', $id)
        ->withCount([
            'orderItems as total_sold' => function($q){
                $q->select(DB::raw('SUM(qty)'));
            }
        ])->paginate(3);

        $order_history = Order::whereHas('items.product', fn($q)=>$q->where('seller_store_id','=', $id))
        ->with(['user:id,name', 'items.product:id,name,price,seller_store_id'])->paginate(3)
        ->map(function($order){
            $amount = $order->items->sum(function($item){
                return $item->price * $item->qty;
            });
            return [
                'order_code' => $order->order_code,
                'date' => $order->created_at->format('M d, y'),
                'customer' => $order->user->name,
                'product' => $order->items->pluck('product.name')->join(', '),
                'amount' => number_format($amount, 2),
                'status' => ucfirst($order->status),
            ];
        });

        return view('admin.seller-detail-view');
    }

}

