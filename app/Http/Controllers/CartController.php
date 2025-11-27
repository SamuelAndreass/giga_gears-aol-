<?php
namespace App\Http\Controllers;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
   //
    public function index(){
        $cart = Cart::with('items.product')
        ->where('user_id', auth()->id())
        ->where('status', 'active')
        ->first();

        return view('customer.cart', compact('cart'));
    }

    public function add(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);

        $cart = Cart::firstOrCreate(
            ['user_id' => auth()->id(), 'status' => 'active'],
            ['total_price' => 0]
        );

        $item = $cart->items()->where('product_id', $productId)->first();
        if ($item) {
            $item->increment('qty', $request->input('qty', 1));
        } else {
            $cart->items()->create([
                'product_id' => $productId,
                'qty' => $request->input('qty', 1),
                'price' => $product->price,
                'subtotal' => $product->price * $request->input('qty', 1),
            ]);
        }

        $cart->update([
            'total_price' => $cart->items->sum(fn($i) => $i->qty * $i->price)
        ]);

        return redirect()->back()->with('message', 'Produk berhasil ditambahkan ke keranjang.');
    }

    public function update(Request $request, $itemId)
    {
        $item = CartItem::findOrFail($itemId);

        $request->validate([
            'qty' => 'required|integer|min:1',
        ]);

        $item->update(['qty' => $request->qty]);
        $cart = $item->cart;
        $cart->update([
            'total_price' => $cart->items->sum(fn($i) => $i->qty * $i->price)
        ]);

        return back()->with('message', 'Jumlah item berhasil diperbarui.');
    }


    public function remove($itemId)
    {
        $item = CartItem::findOrFail($itemId);
        $cart = $item->cart;

        $item->delete();

        $cart->update([
            'total_price' => $cart->items->sum(fn($i) => $i->qty * $i->price)
        ]);

        return back()->with('message', 'Item berhasil dihapus dari keranjang.');
    }

    public function checkout()
    {
        DB::transaction(function () {
            $cart = Cart::with('items.product')
                ->where('user_id', auth()->id())
                ->where('status', 'active')
                ->firstOrFail();

            $order = Order::create([
                'user_id' => auth()->id(),
                'order_date' => now(),
                'status' => 'pending',
                'total_amount' => $cart->items->sum(fn($i) => $i->qty * $i->price),
            ]);

            // Pindahkan semua item ke order_items
            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'qty' => $item->qty,
                    'price' => $item->price,
                ]);

                // Kurangi stok produk
                $item->product->decrement('stock', $item->qty);
            }

            $cart->update(['status' => 'checked_out']);
            $cart->items()->delete();
        });

        return redirect()->route('orders.index')->with('message', 'Checkout berhasil!');
    }
}
