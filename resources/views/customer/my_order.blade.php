@extends('layouts.main')
@section('title', 'My Orders')

@php
    // Data Dummy Riwayat Pesanan
    $orders = [
        [
            'id' => '#ORD-3045', 
            'date' => '25/09/2025', 
            'product' => 'Logitech G Pro X Headset', 
            'total' => 104, 
            'status' => 'Completed',
            'img_file' => 'product_logitech.png'
        ],
        [
            'id' => '#ORD-3046', 
            'date' => '22/09/2025', 
            'product' => 'MSI Gaming Laptop i7 RTX 4060', 
            'total' => 1309, 
            'status' => 'Shipped',
            'img_file' => 'product_msi.png'
        ],
        [
            'id' => '#ORD-3047', 
            'date' => '20/09/2025', 
            'product' => 'Adobe Creative Cloud License', 
            'total' => 29, 
            'status' => 'Processing',
            'img_file' => 'icon-software.png' 
        ],
        [
            'id' => '#ORD-3048', 
            'date' => '15/09/2025', 
            'product' => 'Samsung Galaxy Tab S9', 
            'total' => 709, 
            'status' => 'Cancelled',
            'img_file' => 'product_samsung.png'
        ],
    ];
    
    // Data Summary (dihitung)
    $total_orders = count($orders);
    $completed = 2; // Disesuaikan untuk demo visual
    $in_progress = 1;
    $cancelled = 1;
@endphp

{{-- ================================================= --}}
{{-- 1. HEADER SECTION (@section('header')) --}}
{{-- ================================================= --}}
@section('header')
    <style>
        /* Style Header dan Card Summary (Visual Upgrade) */
        .header-wrapper {
            width: 100%; height: 90px; padding-top: 20px; background: #FFFFFF; border-bottom: 1px solid #eee;
        }
        .main-navbar {
            width: 1280px; max-width: 90%; margin: 0 auto; display: flex; justify-content: space-between; align-items: center;
        }
        .summary-card-inner {
            background: #F4F8FA; /* Latar Belakang abu-abu terang */
            border-radius: 10px;
            padding: 20px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            min-height: 120px;
        }
        .summary-card-inner:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.15);
        }
        .status-completed { color: #28a745; }
        .status-progress { color: #007bff; }
        .status-cancelled { color: #dc3545; }
        .table thead th {
            border-bottom: 2px solid #067CC2 !important; /* Garis bawah biru */
        }
    </style>
    
    <div class="header-wrapper">
        <div class="page-container main-navbar">
            <img src="{{ asset('images/logo GigaGears.png') }}" alt="GIGAGEARS Logo" width="197" height="24">
            
            <div class="d-flex" style="gap: 71px;">
                <div class="d-flex gap-5">
                    <a href="/" style="color: #000000; font-size:25px; text-decoration: none;">Home</a>
                    <a href="/products" style="color: #000000; font-size:25px; text-decoration: none;">Products</a>
                    <a href="/#about-us-section" style="color: #000000; font-size:25px; text-decoration: none;">About Us</a>
                    <a href="/my-order" style="color: #067CC2; font-size:25px; text-decoration: none;">My Order</a>
                </div>
            </div>

            <a href="/profile" class="d-flex align-items-center justify-content-center" style="border: 1px solid #000000; border-radius: 5px; padding: 10px; width: 135px; height: 52px; text-decoration: none; color: #000;">
                <div class="d-flex align-items-center gap-2" style="gap: 9px;">
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

    <h1 style="font-family: 'Chakra Petch', sans-serif; font-weight: 700; font-size: 55px; line-height: 72px; color: #000000;">My Order History</h1>
    <p style="font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: 20px; line-height: 27px; color: #717171; margin-bottom: 30px;">Track your past and current orders with GigaGears.</p>

    {{-- 1. SUMMARY CARDS --}}
    <div class="row mb-5 justify-content-between" style="gap: 20px;">
        
        {{-- Card 1: Total Orders --}}
        <div class="col-md-3">
            <div class="summary-card-inner" style="background: #EAF3FF;">
                <h5 class="fw-normal mb-1 status-progress" style="font-family: 'Montserrat', sans-serif;">TOTAL ORDERS</h5>
                <h2 class="fw-bolder status-progress" style="font-size: 40px; margin-top: 5px;">{{ $total_orders }}</h2>
            </div>
        </div>

        {{-- Card 2: Completed --}}
        <div class="col-md-3">
            <div class="summary-card-inner" style="background: #E6FFEC;">
                <h5 class="fw-normal mb-1 status-completed" style="font-family: 'Montserrat', sans-serif;">COMPLETED</h5>
                <h2 class="fw-bolder status-completed" style="font-size: 40px; margin-top: 5px;">{{ $completed }}</h2>
            </div>
        </div>
        
        {{-- Card 3: In Progress --}}
        <div class="col-md-3">
            <div class="summary-card-inner" style="background: #FFF7E6;">
                <h5 class="fw-normal mb-1 text-warning" style="font-family: 'Montserrat', sans-serif;">IN PROGRESS</h5>
                <h2 class="fw-bolder text-warning" style="font-size: 40px; margin-top: 5px;">{{ $in_progress }}</h2>
            </div>
        </div>
        
        {{-- Card 4: Cancelled --}}
        <div class="col-md-3">
            <div class="summary-card-inner" style="background: #FFEBEB;">
                <h5 class="fw-normal mb-1 text-danger" style="font-family: 'Montserrat', sans-serif;">CANCELLED</h5>
                <h2 class="fw-bolder text-danger" style="font-size: 40px; margin-top: 5px;">{{ $cancelled }}</h2>
            </div>
        </div>
    </div>

    {{-- GRAFIK SIMULASI (FITUR BARU) --}}
    <div class="card p-4 shadow-sm mb-5" style="border-radius: 10px;">
        <h3 class="fw-bold" style="font-family: 'Chakra Petch', sans-serif; font-size: 25px; border-bottom: 1px solid #eee; padding-bottom: 15px;">Order Status Distribution</h3>
        <div class="row pt-3">
            <div class="col-md-6">
                <div style="height: 200px; background: #f8f8f8; border-radius: 5px; display: flex; justify-content: center; align-items: center; color: #717171;">
                    [Placeholder: Grafik Pie Status Pesanan]
                </div>
            </div>
            <div class="col-md-6">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between">Completed <span class="fw-bold text-success">{{ $completed }}</span></li>
                    <li class="list-group-item d-flex justify-content-between">Shipped / Processing <span class="fw-bold text-info">{{ $in_progress }}</span></li>
                    <li class="list-group-item d-flex justify-content-between">Cancelled <span class="fw-bold text-danger">{{ $cancelled }}</span></li>
                    <li class="list-group-item d-flex justify-content-between fw-bold">Total Items Bought <span class="fw-bolder text-primary">12 Items</span></li>
                </ul>
            </div>
        </div>
    </div>
    
    {{-- 2. FILTER DAN SEARCH --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold" style="font-family: 'Chakra Petch', sans-serif; font-size: 30px;">Order History Details</h3>
        
        <div class="d-flex gap-3">
            <input type="text" class="form-control" placeholder="Search Order ID or Product">
            <select class="form-select" style="width: 200px;">
                <option selected>Filter Status (All)</option>
                <option value="1">Completed</option>
                <option value="2">Shipped</option>
                <option value="3">Processing</option>
                <option value="4">Cancelled</option>
            </select>
        </div>
    </div>

    {{-- 3. ORDER LIST TABLE --}}
    <div class="card p-4 shadow-sm" style="border-radius: 10px;">
        
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr style="font-family: 'Chakra Petch', sans-serif; font-weight: 600; font-size: 18px; color: #000000;">
                        <th scope="col" style="width: 15%;">Order ID</th>
                        <th scope="col" style="width: 15%;">Date</th>
                        <th scope="col" style="width: 35%;">Product</th>
                        <th scope="col" style="width: 10%;">Total ($)</th>
                        <th scope="col" style="width: 10%;">Status</th>
                        <th scope="col" style="width: 15%;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                    <tr style="font-family: 'Montserrat', sans-serif; font-weight: 500;">
                        <td class="fw-bold" style="color: #067CC2;">{{ $order['id'] }}</td>
                        <td class="text-secondary">{{ $order['date'] }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="{{ asset('images/' . $order['img_file']) }}" alt="Product" style="width: 50px; height: 50px; margin-right: 15px; object-fit: contain; border: 1px solid #eee; border-radius: 5px;">
                                <span class="fw-bold text-dark">{{ $order['product'] }}</span>
                            </div>
                        </td>
                        <td class="fw-bold text-success">${{ number_format($order['total'], 0, ',', '.') }}</td>
                        <td>
                            @if ($order['status'] == 'Completed')
                                <span class="badge bg-success py-2 px-3">{{ $order['status'] }}</span>
                            @elseif ($order['status'] == 'Shipped')
                                <span class="badge bg-info py-2 px-3">{{ $order['status'] }}</span>
                            @elseif ($order['status'] == 'Processing')
                                <span class="badge bg-warning text-dark py-2 px-3">{{ $order['status'] }}</span>
                            @else
                                <span class="badge bg-danger py-2 px-3">{{ $order['status'] }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="#" class="btn btn-sm fw-bold" style="background: #067CC2; color: white; border-radius: 5px;">Detail</a>
                                @if ($order['status'] == 'Shipped' || $order['status'] == 'Processing')
                                    <button class="btn btn-sm btn-outline-danger" title="Ask for Cancellation">Batal</button>
                                @endif
                                @if ($order['status'] == 'Completed')
                                    <button class="btn btn-sm btn-outline-dark" title="Download Invoice">Invoice</button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        {{-- Pagination (Simulasi) --}}
        <div class="d-flex justify-content-center mt-4">
            <nav>
              <ul class="pagination">
                <li class="page-item disabled"><a class="page-link" href="#">&laquo;</a></li>
                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">3</a></li>
                <li class="page-item"><a class="page-link" href="#">&raquo;</a></li>
              </ul>
            </nav>
        </div>
        
    </div>
    <div style="height: 100px;"></div>

@endsection


{{-- ================================================= --}}
{{-- 3. FOOTER SECTION (@section('footer')) --}}
{{-- ================================================= --}}
@section('footer')
    <style>
        .main-footer {
            background-image: linear-gradient(87.6deg, #FFFFFF 8.86%, rgba(78, 218, 254, 0.67) 32.51%, rgba(6, 124, 194, 0.93) 95.43%);
            padding: 50px 0 20px 0;
            position: relative;
            z-index: 10;
        }
        .footer-title {
            font-family: 'Chakra Petch', sans-serif;
            font-weight: 700;
            font-size: 22px;
            line-height: 29px;
            color: #000000;
            margin-bottom: 12px;
        }
        .copyright.page-container {
            width: 100%;
            text-align: center;
            padding-top: 30px;
            font-family: 'Chakra Petch', sans-serif;
            font-weight: 700;
            font-size: 22px;
            line-height: 29px;
            color: #000000;
        }
    </style>
    
    <footer class="main-footer">
        
        <div class="page-container d-flex justify-content-between" style="gap: 197px;">
            <div class="d-flex flex-column" style="width: 343px; gap: 27px;">
                <img src="{{ asset('images/gigagears-logo-footer.png') }}" alt="GIGAGEARS Logo" width="263" height="32">
                <p style="font-family: 'Chakra Petch', sans-serif; font-style: italic; font-weight: 500; font-size: 22px; line-height: 29px; color: #000000;">Empowering your digital lifestyle with the best tech and software.</p>
            </div>

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