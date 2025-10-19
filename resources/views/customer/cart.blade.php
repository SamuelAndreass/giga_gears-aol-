<h2>Keranjang Saya</h2>

@if ($cart && $cart->items->count())
    <table>
        <thead>
            <tr>
                <th>Produk</th>
                <th>Qty</th>
                <th>Harga</th>
                <th>Subtotal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($cart->items as $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td>{{ $item->qty }}</td>
                    <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($item->qty * $item->price, 0, ',', '.') }}</td>
                    <td>
                        <form method="POST" action="{{ route('cart.remove', $item->id) }}">
                            @csrf @method('DELETE')
                            <button type="submit">Hapus</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h3>Total: Rp {{ number_format($cart->total_price, 0, ',', '.') }}</h3>

    <form method="POST" action="{{ route('cart.checkout') }}">
        @csrf
        <button type="submit">Checkout</button>
    </form>
@else
    <p>Keranjang kamu kosong.</p>
@endif
