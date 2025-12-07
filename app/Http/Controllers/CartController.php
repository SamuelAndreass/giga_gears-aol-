<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;

class CartController extends Controller
{
    // show cart page
    public function index(Request $request)
    {
        $cart = Cart::with('items.product')
            ->where('user_id', $request->user()->id)
            ->where('status', 'active')
            ->first();

        // support json (AJAX) or blade view
        if ($request->wantsJson()) {
            return response()->json(['cart' => $cart]);
        }

        return view('customer.cart', compact('cart'));
    }

    // add product to cart
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'qty' => 'nullable|integer|min:1',
        ]);

        $user = $request->user();
        $qty = (int) ($request->input('qty', 1));
        $product = Product::findOrFail($request->product_id);

        // create or get active cart
        $cart = Cart::firstOrCreate(
            ['user_id' => $user->id, 'status' => 'active'],
            ['total' => 0]
        );

        $item = $cart->items()->where('product_id', $product->id)->first();

        if ($item) {
            $item->update([
                'qty' => $item->qty + $qty,
                'price' => $product->original_price,
                'subtotal' => ($item->qty + $qty) * $product->original_price,
            ]);
        } else {
            $cart->items()->create([
                'product_id' => $product->id,
                'qty' => $qty,
                'sku' => $product->sku ?? null,
                'price' => $product->original_price,
                'subtotal' => $product->original_price * $qty,
            ]);
        }

        // recalc total
        $cart->refresh();
        $cart->update([
            'total' => $cart->items->sum(fn($i) => ($i->qty ?? 0) * ($i->price ?? 0))
        ]);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Added to cart', 'cart' => $cart->load('items.product')], 200);
        }

        return redirect()->back()->with('message', 'Product added to cart.');
    }

    // update item qty
    public function update(Request $request, $itemId)
    {
        $request->validate([
            'qty' => 'required|integer|min:1',
        ]);

        $item = CartItem::findOrFail($itemId);
        // ensure owner
        if ($item->cart->user_id !== $request->user()->id) {
            abort(403);
        }

        $item->update([
            'qty' => $request->qty,
            'subtotal' => $request->qty * $item->price,
        ]);

        $cart = $item->cart;
        $cart->update([
            'total' => $cart->items->sum(fn($i) => ($i->qty ?? 0) * ($i->price ?? 0))
        ]);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Cart updated', 'cart' => $cart->load('items.product')]);
        }

        return back()->with('message', 'Cart updated.');
    }

    // remove item
    public function remove(Request $request, $itemId)
    {
        $item = CartItem::findOrFail($itemId);
        if ($item->cart->user_id !== $request->user()->id) abort(403);

        $cart = $item->cart;
        $item->delete();

        $cart->update([
            'total' => $cart->items->sum(fn($i) => ($i->qty ?? 0) * ($i->price ?? 0))
        ]);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Item removed', 'cart' => $cart->load('items.product')]);
        }

        return back()->with('message', 'Item removed.');
    }

    public function buyNowRedirect(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'qty' => 'nullable|integer|min:1',
        ]);

        $user = $request->user();
        $qty = (int) ($request->qty ?? 1);

        // Ambil product
        $product = Product::findOrFail($request->product_id);

        // Ambil atau buat cart aktif untuk user
        $cart = Cart::firstOrCreate([
            'user_id' => $user->id,
            'status' => 'active',
        ]);

        // Pilihan: kosongkan cart lama (agar checkout hanya untuk produk ini)
        // Jika kamu mau agar buy-now hanya menambah tanpa hapus, skip this step.
        $cart->items()->delete();

        // Tambah item baru (simpan price & subtotal snapshot)
        $cart->items()->create([
            'product_id' => $product->id,
            'qty' => $qty,
            'price' => $product->original_price, // atau original_price sesuai schema
            'subtotal' => $product->original_price * $qty,
        ]);

        // Redirect ke halaman checkout yang sudah ada (showCheckoutPage)
        return redirect()->route('checkout.index');
    }
}
