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
        $t_seller = User::where('role','seller')->where('is_seller', true)->count();
        $t_transaction = User::where('status', 'completed')->sum('total_amount');
        $t_pending = Order::where('status','pending')->count();
        $recent_order = OrderItem::all()->take(5);

        $customer_growth = User::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT (*) as count'),
        )->where('role', 'customer')->whereYear('created_at', date('y'))->groupBy('month')->pluck('total', 'month');
        
        $months = [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
        ];

        $customerData = [];
        foreach($months as $index => $name){
            $customerData[] = $customer_growth[$index+1] ?? 0; 
        }

        $revenubycategory = DB::table('orders')
        ->join('products', 'orders.product_id', '=', 'products.id')
        ->join('categories', 'product.category_id', '=', 'cateogries.id')
        ->select('categories.name as category_name', DB::raw('SUM(orders.total_amount) as total_revenue'))
        ->groupBy('categories.name')
        ->get();

        return view('admin.dashboard', compact('t_customer', 't_seller', 't_transaction', 't_pending', 'recent_order', 'revenubycategory', 'customerData'));
    }

    public function viewUser(){
        $user = CustomerProfile::with('users')->paginate(10);
        return view('admin.customer-view', compact('user'));
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

    public function productVerifPage(){
        $product = Product::paginate(10);
        return view('admin.product-verif', compact('product'));
    }
    public function approveProd($id){
        $product = Product::find($id);
        $product::approve(auth()->user()->id);
        return back()->with('message', 'Berhasil melakukan approve product.');
    }

    public function rejectProd($id){
        $product = Product::find($id);
        $product::reject(auth()->user()->id);
        return back()->with('message', 'Berhasil melakukan rejection terhadap product.');
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

