<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Shipping; // model shipping
use App\Models\ShippingOrder;
use Exception;
class CheckoutController extends Controller
{
    /**
     * Perform checkout: create order + order_items atomically, decrement stock.
     * Expects: shipping_address, payment_method, optional idempotency_key.
     */
    

    public function showCheckoutPage(Request $request)
    {
        $user = $request->user();

        $cart = Cart::with('items.product')
                    ->where('user_id', $user->id)
                    ->where('status', 'active')
                    ->first();

        if (! $cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Cart kosong.');
        }

        // hitung subtotal
        $subTotal = 0;
        foreach ($cart->items as $ci) {
            $subTotal += $ci->subtotal;
        }
        
         // Default pilih shipping pertama
        $shippings = Shipping::where('status', 'active')->get();
        $defaultShipping = $shippings->first();
        $shippingFee = $defaultShipping ? $defaultShipping->base_rate : 0;

        $defaultTotal = $subTotal + $shippingFee;

        return view('customer.checkout', [
            'cart' => $cart,
            'sub_total' => $subTotal,
            'shipping_fee' => $shippingFee,
            'total_payment' => $defaultTotal,
            'shippings' => $shippings,
        ]);
    }

// di CheckoutController.php
      /**
     * AJAX: return shipping fee numeric for given shipping_id.
     */
    public function getShippingFee(Request $request)
    {
        $request->validate([
            'shipping_id' => 'required|integer|exists:shippings,id',
        ]);

        $shipping = Shipping::find($request->shipping_id);

        // pastikan base_rate disimpan sebagai integer (Rp) di DB
        $fee = (float) ($shipping->base_rate ?? 0);

        return response()->json([
            'shipping_id' => $shipping->id,
            'shipping_fee' => $fee, // numeric
        ], 200);
    }

    /**
     * Process checkout (form POST).
     * Accepts: shipping_address, payment_method, shipping_id, idempotency_key (optional)
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'shipping_address' => 'required|string|max:2000',
            'payment_method' => 'required|string',
            'shipping_id' => 'required|integer|exists:shippings,id',
            'idempotency_key' => 'nullable|string',
        ]);

        try {
            $user = $request->user();

            $idempotencyKey = $request->input('idempotency_key') ?? $request->header('X-Idempotency-Key') ?? (string) Str::uuid();

            // idempotency: return if exists
            $existing = Order::where('user_id', $user->id)
                             ->where('idempotency_key', $idempotencyKey)
                             ->first();
            if ($existing) {
                // if web form submit -> redirect to order page
                if (! $request->wantsJson()) {
                    return redirect()->route('orders.show', $existing->id)
                                     ->with('info', 'Order already processed.');
                }
                return response()->json(['order' => $existing], 200);
            }

            $cart = Cart::with('items.product')
                        ->where('user_id', $user->id)
                        ->where('status', 'active')
                        ->first();

            if (! $cart || $cart->items->isEmpty()) {
                if (! $request->wantsJson()) {
                    return redirect()->route('cart.index')->with('error', 'Cart is empty.');
                }
                return response()->json(['message' => 'Cart is empty'], 400);
            }

            // server authoritative: compute subtotal from cart
            $subtotal = 0;
            foreach ($cart->items as $ci) {
                // ensure product exists
                if (! $ci->product) {
                    throw new Exception("Cart item product missing (id: {$ci->product_id})");
                }
                $subtotal += ($ci->product->price * $ci->qty);
            }

            // compute shipping fee by shipping_id
            $shipping = Shipping::find($request->shipping_id);
            $shippingFee = $shipping ? (float) ($shipping->base_rate ?? 0) : 0;

            DB::beginTransaction();

            $order = Order::create([
                'user_id' => $user->id,
                'order_code' => 'ORD-' . strtoupper(Str::random(8)),
                'ordered_at' => now(),
                'shipping_address' => $request->shipping_address,
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending',
                'status' => 'pending',
                'subtotal' => $subtotal,
                'shipping_fee' => $shippingFee,
                'tax' => 0,
                'discount' => 0,
                'total_amount' => ($subtotal + $shippingFee),
                'idempotency_key' => $idempotencyKey,
            ]);

             // estimate arrival using shipping rules (if shipping has min/max delivery days)

            ShippingOrder::create([
                'order_id' => $order->id,
                'shipping_id' => $shipping->id ?? null,
                'type' => $shipping->service_type,
            ]);


            // create order items & decrement stock
            foreach ($cart->items as $ci) {
                // lock product row
                $product = Product::lockForUpdate()->find($ci->product_id);
                if (! $product) {
                    throw new Exception("Product not found (id: {$ci->product_id})");
                }
                if ($product->stock < $ci->qty) {
                    throw new Exception("Insufficient stock for product: {$product->name}");
                }

                $unitPrice = $product->original_price;
                $lineSubtotal = $unitPrice * $ci->qty;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'qty' => $ci->qty,
                    'sku' => $product->sku ?? null,
                    'price' => $unitPrice,
                    'subtotal' => $lineSubtotal,
                ]);

                $product->decrement('stock', $ci->qty);
            }

            // mark cart checked out
            $cart->update(['status' => 'checked_out']);
            $cart->items()->delete();

            DB::commit();

            // success: for web submit, redirect to order page (adjust route)
            if (! $request->wantsJson()) {
                return redirect()->route('orders.show', $order->id)
                                 ->with('success', 'Order placed successfully.');
            }

            return response()->json(['order' => $order], 201);

        } catch (Exception $e) {
            DB::rollBack();
            // log the exception for debugging
            Log::error('Checkout failed: '.$e->getMessage(), [
                'user_id' => $request->user()?->id,
                'payload' => $request->all(),
            ]);

            if (! $request->wantsJson()) {
                return back()->with('error', 'Checkout failed: ' . $e->getMessage());
            }
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }


}
