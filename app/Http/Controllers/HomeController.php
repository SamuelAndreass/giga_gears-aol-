<?php
namespace App\Http\Controllers;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\Category;
class HomeController extends Controller
{
   public function index()
    {
        $categories = Category::all(); // ambil dari database
        $products = Product::take(6)->get(); // ambil beberapa produk

        // Hitung jumlah cart dari session
        $cart = session()->get('cart', []);
        $cartCount = array_sum(array_column($cart, 'quantity'));

        return view('customer.home', compact('categories', 'products', 'cartCount'));
    }
}