<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart | GigaGears</title>
    
    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@400;500;600;700&family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background: #FFFFFF;
            margin: 0;
            padding: 0;
        }
        
        .page-container {
            width: 1280px;
            max-width: 90%;
            margin: 0 auto;
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Chakra Petch', sans-serif;
        }
        
        .cart-item-image {
            width: 100px;
            height: 100px;
            object-fit: contain;
            border: 1px solid #eee;
            border-radius: 5px;
            padding: 5px;
        }
        
        .btn-custom {
            background: #067CC2;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-custom:hover {
            background: #0568a3;
            color: white;
        }
    </style>
</head>
<body>
    {{-- HEADER (Sama seperti home.blade.php) --}}
    <div style="width:100%; height:90px; padding-top:20px; background:#FFFFFF; border-bottom:1px solid #eee;">
        <div class="page-container" style="display:flex; justify-content:space-between; align-items:center;">
            <img src="/images/logo GigaGears.png" alt="GIGAGEARS Logo" width="197" height="24">
            
            <div style="display:flex; gap:71px;">
                <div style="display:flex; gap:20px;">
                    <a href="/" style="color:#000000; font-size:25px; text-decoration:none;">Home</a>
                    <a href="/products" style="color:#000000; font-size:25px; text-decoration:none;">Products</a>
                    <a href="/#about-us-section" style="color:#000000; font-size:25px; text-decoration:none;">About Us</a>
                    <a href="/my-order" style="color:#000000; font-size:25px; text-decoration:none;">My Order</a>
                </div>
            </div>

            <a href="/profile" style="border:1px solid #000000; border-radius:5px; padding:10px; width:135px; height:52px; text-decoration:none; color:#000; display:flex; align-items:center; justify-content:center;">
                <div style="display:flex; align-items:center; gap:9px;">
                    <span>Profil</span>
                    <img src="/images/logo foto profile.png" alt="Profile" style="width:32px; height:32px; border-radius:50%;">
                </div>
            </a>
        </div>
    </div>

    {{-- CONTENT --}}
    <div class="page-container" style="padding:40px 0;">
        <h1 style="font-family:'Chakra Petch', sans-serif; font-weight:700; font-size:46px; margin-bottom:20px;">🛒 Shopping Cart</h1>
        
        {{-- Success Message --}}
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif
        
        {{-- Error Message --}}
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif
        
        {{-- Empty Cart --}}
        @if(empty($cartItems))
        <div class="text-center py-5">
            <h3 class="text-muted mb-4">Your cart is empty</h3>
            <p class="text-muted mb-4">Add some products to start shopping</p>
            <a href="/" class="btn-custom">Browse Products</a>
        </div>
        @else
        {{-- Cart Items --}}
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th class="text-center">Price</th>
                                    <th class="text-center">Quantity</th>
                                    <th class="text-center">Subtotal</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cartItems as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="/images/{{ $item['image'] }}" class="cart-item-image me-3">
                                            <div>
                                                <h6 class="mb-0">{{ $item['name'] }}</h6>
                                                <small class="text-muted">Item ID: {{ $item['id'] }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">${{ number_format($item['price'], 2) }}</td>
                                    <td class="text-center">
                                        <form action="/cart/update/{{ $item['id'] }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" class="form-control" style="width:80px; display:inline;">
                                            <button type="submit" class="btn btn-sm btn-outline-primary">Update</button>
                                        </form>
                                    </td>
                                    <td class="text-center fw-bold">${{ number_format($item['subtotal'], 2) }}</td>
                                    <td class="text-center">
                                        <form action="/cart/remove/{{ $item['id'] }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-danger btn-sm">Remove</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        
                        <div class="d-flex justify-content-between mt-4">
                            <a href="/" class="btn btn-outline-secondary">← Continue Shopping</a>
                            <a href="/checkout" class="btn btn-success">Proceed to Checkout →</a>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Order Summary --}}
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Items:</span>
                            <span>{{ count($cartItems) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="h5">Total:</span>
                            <span class="h4 text-primary">${{ number_format($total, 2) }}</span>
                        </div>
                        
                        <div class="d-grid">
                            <a href="/checkout" class="btn btn-dark btn-lg">Checkout Now</a>
                        </div>
                        
                        <div class="mt-3">
                            <small class="text-muted">
                                <i>Free shipping on orders over $500</i>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>