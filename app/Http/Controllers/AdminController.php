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
use App\Models\Shipping;

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

    public function viewUser(Request $request){
        $query = User::with('customerProfile')
            ->where('role', 'customer')
            ->orderBy('created_at', 'desc');

        // Search form GET (by ID, name, email, phone)
        if ($search = $request->search) {
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', "%$search%")
                  ->orWhere('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhere('phone', 'like', "%$search%");
            });
        }

        // Optional status filter (dropdown)
        if ($status = $request->status) {
            if ($status !== "all") {
                $query->where('status', $status);
            }
        }

        $customers = $query->paginate(10)->withQueryString();

    }

    public function updateUser(Request $request, User $user){
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => "required|email|unique:users,email,{$user->id}",
            'phone'    => 'nullable|string|max:20',
            'address'  => 'nullable|string',
        ]);

        $user->update([
            'name'  => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        // update CustomerProfile
        $user->customerProfile->update([
            'address' => $request->address,
        ]);

        return back()->with('success', 'Customer updated successfully.');
    }

    public function updateStatus(Request $request, User $user){
        $request->validate([
            'status' => 'required|in:active,suspended,banned',
        ]);

        $user->status = $request->status;
        $user->save();

        return back()->with('success', "Status updated to {$user->status}");
    }

    public function sellerIndex(Request $request){
        $query = SellerStore::with('user') // relasi owner user()
            ->withCount('products') // jumlah produk aktif/semua tergantung relasi
            ->orderBy('created_at','desc');

        if ($q = $request->search) {
            $query->where(function($qq) use ($q) {
                $qq->where('id', 'like', "%{$q}%")
                   ->orWhere('store_name', 'like', "%{$q}%")
                   ->orWhereHas('user', fn($u)=> $u->where('name','like',"%{$q}%")->orWhere('email','like',"%{$q}%"));
            });
        }

        $sellers = $query->paginate(10)->withQueryString();

        return view('admin.seller', compact('sellers'));
    }

    public function updateSellerStatus(Request $request, SellerStore $seller){
        $request->validate([
            'status' => 'required|in:active,suspended,closed'
        ]);

        $seller->status = $request->status;
        $seller->save();

        // optional: dispatch job to hide products if suspended
        return back()->with('success','Seller status updated.');
    }

    public function sellerJson(SellerStore $seller){
        $seller->load(['user', 'products' => function($q){
            $q->select('id','seller_store_id','name','price','stock','status');
        }]);

        // total revenue (sum of order_items.unit_price * quantity for seller's products)
        $totalRevenue = DB::table('order_items')
            ->join('products','order_items.product_id','products.id')
            ->where('products.seller_store_id', $seller->id)
            ->selectRaw('COALESCE(SUM(order_items.unit_price * order_items.quantity),0) as total')
            ->value('total');

        // total orders count for this seller (distinct orders containing seller's products)
        $totalOrders = DB::table('order_items')
            ->join('products','order_items.product_id','products.id')
            ->where('products.seller_store_id', $seller->id)
            ->distinct('order_items.order_id')
            ->count('order_items.order_id');

        return response()->json([
            'id' => $seller->id,
            'store_name' => $seller->store_name,
            'avatar' => $seller->logo_url ?? null,
            'owner_name' => $seller->user->name ?? '-',
            'email' => $seller->user->email ?? '-',
            'phone' => $seller->phone ?? '-',
            'status' => $seller->status,
            'created_at' => $seller->created_at->format('d M Y'),
            'product_count' => $seller->products->count(),
            'total_revenue' => (float) $totalRevenue,
            'total_orders' => (int) $totalOrders,
            'products' => $seller->products->map(function($p){
                return [
                    'id' => $p->id,
                    'sku' => 'PRD-'.$p->id,
                    'name' => $p->name,
                    'price' => (float) $p->price,
                    'stock' => $p->stock,
                    'sold' => $p->sold_count ?? 0, // if you keep sold_count
                    'status' => $p->status,
                    'rating' => (float) ($p->rating ?? 0),
                ];
            }),
        ]);
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

    public function shippingIndex(){
        $shippings = Shipping::paginate(5);
        return view('admin.shipping', compact('shippings'));
    }

    public function addShipping(Request $request){
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

