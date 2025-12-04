@extends('layouts.main')
@section('title', 'Checkout & Payment')

@php
    // Data Produk yang dibeli (Contoh: Logitech G Pro X Headset)
    $product = [
        'name' => 'Logitech G Pro X Headset', 
        'description' => 'The Logitech G Pro X Gaming.....',
        'price' => '$129', 
        'price_after' => '$99', 
        'color' => 'Black',
        'qty' => 1,
        'img_file' => 'product_logitech.png' // FIX: Kunci gambar ditambahkan
    ];
    // Data Pembayaran (dihitung)
    $sub_total = 99;
    $shipping_fee = 5;
    $total_payment = $sub_total + $shipping_fee;
@endphp

{{-- ================================================= --}}
{{-- 1. HEADER SECTION (@section('header')) --}}
{{-- (Salin dan tempelkan kode Header yang stabil dari home.blade.php di sini) --}}
{{-- ================================================= --}}
@section('header')
    <style>
        /* STYLE KHUSUS UNTUK HEADER (Sama seperti Product Detail) */
        .header-wrapper {
            width: 100%;
            height: 90px; 
            padding-top: 20px; 
            background: #FFFFFF;
            border-bottom: 1px solid #eee;
        }
        .main-navbar {
            width: 1280px;
            max-width: 90%; 
            margin: 0 auto; 
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        /* Style untuk Tombol Checkout di Navbar */
        .nav-links a.active {
            color: #067CC2 !important;
        }
        /* Styling Input Field yang Rapi */
        .checkout-input-group {
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 15px;
        }
    </style>
    
    <div class="header-wrapper">
        <div class="page-container main-navbar">
            <img src="{{ asset('images/logo GigaGears.png') }}" alt="GIGAGEARS Logo" width="197" height="24">
            
            <div class="d-flex" style="gap: 71px;">
                <div class="d-flex gap-5">
                    <a href="/" style="color: #000000; text-decoration: none;">Home</a>
                    <a href="/products" class="active" style="color: #000000; text-decoration: none;">Products</a>
                    <a href="/about-us" style="color: #000000; text-decoration: none;">About Us</a>
                    <a href="/my-order" style="color: #000000; text-decoration: none;">My Order</a>
                </div>
            </div>

            <a href="/profile" class="d-flex align-items-center justify-content-center" style="border: 1px solid #000000; border-radius: 5px; padding: 10px; width: 135px; height: 52px; text-decoration: none; color: #000;">
                <div class="d-flex align-items-center" style="gap: 9px;">
                    <span>Profil</span>
                    <img src="{{ asset('images/logo foto profile.png') }}" alt="Profile" style="width: 32px; height: 32px; border-radius: 50%;">
                </div>
            </a>
        </div>
    </div>
@endsection


{{-- ================================================= --}}
{{-- 2. KONTEN UTAMA (@section('content')) --}}
{{-- ================================================= --}}
@section('content')

    <form action="/process-checkout" method="POST">
        @csrf
        
        {{-- Title Page --}}
        <h1 style="font-family: 'Chakra Petch', sans-serif; font-weight: 700; font-size: 46px;">Checkout & Payment</h1>
        <p class="text-muted mb-4">Secure Your Gears</p>

        <div class="row">
            {{-- KOLOM KIRI: ALAMAT & PEMBAYARAN --}}
            <div class="col-md-7">
                
                {{-- Frame Produk Beli --}}
                <div class="card p-3 mb-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <img src="{{ asset('images/' . $product['img_file']) }}" alt="{{ $product['name'] }}" style="width: 80px; height: 80px; margin-right: 15px;">
                            <div>
                                <h5 class="fw-bold mb-0">{{ $product['name'] }}</h5>
                                <small class="text-muted">{{ $product['description'] }}</small>
                            </div>
                        </div>
                        
                        <div class="text-end">
                            <div class="text-decoration-line-through text-danger">{{ $product['price'] }}</div>
                            <h4 class="fw-bold">{{ $product['price_after'] }}</h4>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end align-items-center mt-3">
                        <small class="text-muted me-2">Color: {{ $product['color'] }}</small>
                        <div class="d-flex align-items-center border rounded">
                            <button type="button" class="btn btn-sm btn-light">-</button>
                            <span class="mx-2">{{ $product['qty'] }}</span>
                            <button type="button" class="btn btn-sm btn-light">+</button>
                        </div>
                    </div>
                </div>

                {{-- A. Customer Information (Shipping Address) --}}
                <h3 class="fw-bold mb-3">Customer Information</h3>
                <h5 class="fw-bold mb-3">A. Shipping Address</h5>
                
                {{-- Form Input Alamat --}}
                <div class="row">
                    <div class="col-md-6">
                        <input type="text" name="full_name" class="form-control checkout-input-group" placeholder="Full Name" required>
                    </div>
                    <div class="col-md-6">
                        <input type="email" name="user_email" class="form-control checkout-input-group" placeholder="Username/email" required>
                    </div>
                    <div class="col-md-6">
                        <input type="tel" name="phone_number" class="form-control checkout-input-group" placeholder="Phone Number" required>
                    </div>
                    <div class="col-md-6">
                        <input type="text" name="address" class="form-control checkout-input-group" placeholder="Address" required>
                    </div>
                    <div class="col-12">
                        <textarea name="description" class="form-control checkout-input-group" rows="2" placeholder="Description (Optional)"></textarea>
                    </div>
                </div>

                {{-- B. Delivery Method --}}
                <h5 class="fw-bold mb-3 mt-4">B. Delivery Method</h5>
                <div class="list-group mb-4">
                    <label class="list-group-item d-flex justify-content-between align-items-center">
                        <input class="form-check-input me-1" type="radio" name="shipping_method" value="standard" checked>
                        Standard Shipping (3–5 days) – ${{ $shipping_fee }}
                        <span class="text-primary fw-bold">&gt;</span>
                    </label>
                    <label class="list-group-item d-flex justify-content-between align-items-center">
                        <input class="form-check-input me-1" type="radio" name="shipping_method" value="express">
                        Express Shipping (1–2 days) – $10
                        <span class="text-primary fw-bold">&gt;</span>
                    </label>
                </div>

                {{-- Payment Method --}}
                <h5 class="fw-bold mb-3 mt-4">Payment Method</h5>
                <div class="list-group mb-4">
                    <label class="list-group-item d-flex justify-content-between align-items-center">
                        <input class="form-check-input me-1" type="radio" name="payment_method" value="card" checked>
                        Credit / Debit Card (Visa, MasterCard)
                        <span class="text-primary fw-bold">&gt;</span>
                    </label>
                    <label class="list-group-item d-flex justify-content-between align-items-center">
                        <input class="form-check-input me-1" type="radio" name="payment_method" value="bank_transfer">
                        Bank Transfer
                        <span class="text-primary fw-bold">&gt;</span>
                    </label>
                    <label class="list-group-item d-flex justify-content-between align-items-center">
                        <input class="form-check-input me-1" type="radio" name="payment_method" value="e_wallet">
                        E-Wallet (PayPal, Gopay, OVO, Dana)
                        <span class="text-primary fw-bold">&gt;</span>
                    </label>
                    <label class="list-group-item d-flex justify-content-between align-items-center">
                        <input class="form-check-input me-1" type="radio" name="payment_method" value="crypto">
                        Crypto (BTC, ETH, USDT)
                        <span class="text-primary fw-bold">&gt;</span>
                    </label>
                </div>

                {{-- Credit Card Form --}}
                <h5 class="fw-bold mb-3 mt-4">Credit Card Form</h5>
                <input type="text" name="card_number" class="form-control checkout-input-group" placeholder="Card Number" required>
                <input type="text" name="card_holder" class="form-control checkout-input-group" placeholder="Cardholder Name" required>
                <div class="row">
                    <div class="col-md-6">
                        <input type="text" name="expiry_date" class="form-control checkout-input-group" placeholder="Expiry Date" required>
                    </div>
                    <div class="col-md-6">
                        <input type="text" name="cvv" class="form-control checkout-input-group" placeholder="CVV" required>
                    </div>
                </div>

            </div>

            {{-- KOLOM KANAN: RINGKASAN PEMBAYARAN --}}
            <div class="col-md-5">
                <div class="card p-4 sticky-top" style="top: 20px;">
                    <h3 class="fw-bold mb-4">Order Summary</h3>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Sub Total</span>
                        <span class="fw-bold">${{ $sub_total }}</span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-4">
                        <span>Shipping Fee</span>
                        <span class="fw-bold">${{ $shipping_fee }}</span>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="fw-bold mb-0">Total Payment</h4>
                        <h3 class="fw-bold mb-0 text-primary">${{ $total_payment }}</h3>
                    </div>
                    
                    <button type="submit" class="btn btn-dark btn-lg">Pay Now</button>
                </div>
            </div>
        </div>
    </form>
    
@endsection


{{-- ================================================= --}}
{{-- 3. FOOTER SECTION (@section('footer')) --}}
{{-- (Salin dan tempelkan kode Footer yang stabil dari home.blade.php di sini) --}}
{{-- ================================================= --}}
@section('footer')
    <style>
        .main-footer {
            background-image: linear-gradient(87.6deg, #FFFFFF 8.86%, rgba(78, 218, 254, 0.67) 32.51%, rgba(6, 124, 194, 0.93) 95.43%);
            padding: 50px 0 20px 0;
            position: relative;
            z-index: 10;
            top:25px;
        }
        .footer-title {
            font-family: 'Chakra Petch', sans-serif;
            font-weight: 700;
            font-size: 22px;
            line-height: 29px;
            color: #000000;
            margin-bottom: 12px;
        }
        .copyright {
            width: 100%;
            text-align: center;
            padding-bottom: 20px;
            font-family: 'Chakra Petch', sans-serif;
            font-weight: 700;
            font-size: 22px;
            line-height: 29px;
            color: #000000;
        }
    </style>
    
    <footer class="main-footer">
        <div class="page-container d-flex justify-content-between" style="gap: 197px; position: relative; z-index: 10;">
            {{-- Frame 58: Logo dan Slogan --}}
            <div class="d-flex flex-column" style="width: 343px; gap: 27px;">
                <img src="{{ asset('images/logo GigaGears.png') }}" alt="GIGAGEARS Logo" width="263" height="32">
                <p style="font-family: 'Chakra Petch', sans-serif; font-style: italic; font-weight: 500; font-size: 22px; line-height: 29px; color: #000000;">Empowering your digital lifestyle with the best tech and software.</p>
            </div>

            {{-- Frame 57: Quick Links, Support, Social Media --}}
            <div class="d-flex" style="width: 740px; justify-content: space-between; gap: 155px;">
                <div class="d-flex flex-column" style="width: 121px; gap: 12px;">
                    <div class="footer-title">Quick Links</div>
                    <div class="d-flex flex-column">
                        <a href="/">Home</a><a href="/products">Products</a><a href="#">Categories</a><a href="/about-us">About Us</a><a href="#">Contact</a>
                    </div>
                </div>
                <div class="d-flex flex-column" style="width: 220px; gap: 12px;">
                    <div class="footer-title">Customer Support</div>
                    <div class="d-flex flex-column">
                        <a href="#">Help Center</a><a href="#">FAQs</a><a href="#">Shipping & Delivery</a><a href="#">Return & Refund Policy</a>
                    </div>
                </div>
                <div class="d-flex flex-column" style="width: 220px; gap: 12px;">
                    <div class="footer-title">Social Media</div>
                    <div class="d-flex flex-column">
                        <a href="#">Facebook</a><a href="#">Instagram</a><a href="#">X [Twitter]</a><a href="#">LinkedIn</a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="copyright page-container">
            © 2025 GigaGears. All Rights Reserved.
        </div>
    </footer>
@endsection