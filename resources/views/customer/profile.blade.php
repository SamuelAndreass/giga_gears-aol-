@extends('layouts.main')
@section('title', 'Profile & Settings')

{{-- ================================================= --}}
{{-- 1. HEADER SECTION --}}
{{-- ================================================= --}}
@section('header')
    {{-- Memastikan gaya header dimuat --}}
    <style>
        .header-wrapper {
            width: 100%; height: 90px; padding-top: 20px; background: #FFFFFF; border-bottom: 1px solid #eee;
        }
        .main-navbar {
            width: 1280px; max-width: 90%; margin: 0 auto; display: flex; justify-content: space-between; align-items: center;
        }
        .profile-list-item {
            padding: 20px 15px; border: 1px solid #D5D5D5; border-radius: 5px; margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center; background: #FFFFFF; text-decoration: none;
        }
        .section-title {
            font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: 22px; line-height: 27px; color: #000000; margin-bottom: 11px;
        }
    </style>
    
    <div class="header-wrapper">
        
        <div class="page-container main-navbar">
            <img src="{{ asset('images/logo GigaGears.png') }}" alt="GIGAGEARS Logo" width="197" height="24">
            
            <div class="d-flex" style="gap: 71px;">
                <div class="d-flex gap-5">
                    <a href="/" style="color: #000000; font-size:25px; text-decoration: none;">Home</a>
                    <a href="/products" style="color: #000000; font-size:25px; text-decoration: none;">Products</a>
                    <a href="/about-us" style="color: #000000; font-size:25px; text-decoration: none;">About Us</a>
                    <a href="/my-order" style="color: #000000; font-size:25px; text-decoration: none;">My Order</a>
                </div>
            </div>

            <a href="/profile" class="d-flex align-items-center justify-content-center" style="border: 1px solid #000000; border-radius: 5px; padding: 10px; width: 135px; height: 52px; text-decoration: none; color: #000;">
                <div class="d-flex align-items-center gap-2" style="gap: 9px;">
                    <span style="color: #067CC2;">Profil</span>
                    <img src="{{ asset('images/logo foto profile.png') }}" alt="Profile" style="width: 32px; height: 32px; border-radius: 50%; border: 1px solid #067CC2;">
                </div>
            </a>
        </div>
    </div>
@endsection


{{-- ================================================= --}}
{{-- 2. KONTEN UTAMA (@section('content')) --}}
{{-- ================================================= --}}
@section('content')

    {{-- FIX: Definisi variabel dipindahkan di sini agar diproses sebelum konten --}}
    @php
        $profile = [
            'username' => 'JohnDoe99',
            'email' => 'johndoe@gmail.com',
            'phone' => '08299248123989',
            'address' => 'Jl. Melati No. 25, Kel. Sukamaju, Kec. Tebet, Jakarta Selatan, DKI Jakarta, 12810',
            'card' => 'Visa **** 1234',
            'paypal' => 'PayPal (johndoe@gmail.com)',
        ];
    @endphp

    {{-- Title & Log Out --}}
    <div class="d-flex justify-content-between align-items-center" style="width: 100%; margin-top: 50px; margin-bottom: 30px;">
        <div>
            <h1 style="font-family: 'Chakra Petch', sans-serif; font-weight: 700; font-size: 55px; line-height: 72px; color: #000000; margin-bottom: 0;">Profile & Settings</h1>
            <p style="font-family: 'Chakra Petch', sans-serif; font-weight: 700; font-size: 26px; line-height: 34px; color: #717171;">Manage your account details, security, and preferences</p>
        </div>
        
        {{-- Log Out Button --}}
        <a href="/sign-in" class="d-flex justify-content-center align-items-center" style="width: 179px; height: 54px; background: #E33629; border: 1px solid #E33629; border-radius: 5px; color: #FFFFFF; font-family: 'Chakra Petch', sans-serif; font-weight: 600; font-size: 24px; text-decoration: none;">
            <svg style="margin-right: 10px;" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M16 9v-4l8 7-8 7v-4h-8v-6h8zm-2 10v3h-12v-22h12v3h-2v-2h-8v18h8v-2h2z"/></svg>
            Log Out
        </a>
    </div>

    {{-- Frame 102: Profile Summary --}}
    <div class="d-flex align-items-center p-4" style="width: 100%; border: 1px solid #D7D7D7; border-radius: 10px; gap: 42px; margin-bottom: 40px;">
        
        {{-- Ellipse 4: Profile Image --}}
        <img src="{{ asset('images/profile-large.png') }}" alt="Profile" style="width: 140px; height: 140px; border-radius: 50%; background: #D9D9D9; flex-shrink: 0;">
        
        {{-- Frame 97: Details --}}
        <div class="d-flex" style="width: 100%; justify-content: space-between; align-items: center; gap: 62px;">
            
            {{-- Username --}}
            <div class="d-flex flex-column">
                <div class="text-muted" style="font-family: 'Chakra Petch', sans-serif; font-weight: 500; font-size: 18px;">Username</div>
                <div class="fw-bold" style="font-family: 'Chakra Petch', sans-serif; font-weight: 600; font-size: 24px;">{{ $profile['username'] }}</div>
            </div>
            
            {{-- Email --}}
            <div class="d-flex flex-column">
                <div class="text-muted" style="font-family: 'Chakra Petch', sans-serif; font-weight: 500; font-size: 18px;">Email</div>
                <div class="fw-bold" style="font-family: 'Chakra Petch', sans-serif; font-weight: 600; font-size: 24px;">{{ $profile['email'] }}</div>
            </div>
            
            {{-- Phone Number --}}
            <div class="d-flex flex-column">
                <div class="text-muted" style="font-family: 'Chakra Petch', sans-serif; font-weight: 500; font-size: 18px;">Phone Number</div>
                <div class="fw-bold" style="font-family: 'Chakra Petch', sans-serif; font-weight: 600; font-size: 24px;">{{ $profile['phone'] }}</div>
            </div>

            {{-- Edit Profile Button --}}
            <a href="/profile/edit" class="d-flex justify-content-center align-items-center" style="width: 179px; height: 54px; border: 1px solid #E33629; border-radius: 5px; color: #E33629; background: none; font-family: 'Chakra Petch', sans-serif; font-weight: 600; font-size: 24px; flex-shrink: 0; text-decoration: none;">
                Edit Profile
            </a>
        </div>
    </div>


    {{-- Default Shipping Address --}}
    <div class="d-flex flex-column" style="gap: 11px;">
        <div class="section-title">Default Shipping Address</div>
        {{-- Address Box --}}
        <a href="/profile/edit" class="profile-list-item" style="color: #1D1D1D; font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: 22px;">
            <span>{{ $profile['address'] }}</span>
            <span style="color: #067CC2;">Change</span>
        </a>
    </div>

    {{-- Payment Method --}}
    <div class="d-flex flex-column" style="gap: 11px; margin-top: 30px;">
        <div class="section-title">Payment Method</div>
        {{-- Visa Card --}}
        <a href="/profile/edit" class="profile-list-item" style="color: #1D1D1D; font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: 22px;">
            <span>{{ $profile['card'] }}</span>
            <span style="color: #067CC2;">Change</span>
        </a>
        {{-- PayPal --}}
        <a href="/profile/edit" class="profile-list-item" style="color: #1D1D1D; font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: 22px;">
            <span>{{ $profile['paypal'] }}</span>
            <span style="color: #067CC2;">Change</span>
        </a>
    </div>

    {{-- Security Settings --}}
    <div class="d-flex flex-column" style="gap: 11px; margin-top: 30px;">
        <div class="section-title">Security Settings</div>
        {{-- Password --}}
        <a href="/profile/edit" class="profile-list-item" style="color: #1D1D1D; font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: 22px;">
            <span>Password (*******)</span>
            <span style="color: #067CC2;">Change</span>
        </a>
        {{-- Two-Factor Authentication --}}
        <a href="/profile/edit" class="profile-list-item" style="color: #1D1D1D; font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: 22px;">
            <span>Two-Factor Authentication</span>
            <div class="d-flex align-items-center" style="gap: 10px;">
                <span style="width: 23px; height: 23px; background: #067CC2; border-radius: 50%;"></span>
                <span style="font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: 22px; color: #067CC2;">Enabled</span>
            </div>
        </a>
    </div>
    
    {{-- Others --}}
    <div class="d-flex flex-column" style="gap: 11px; margin-top: 30px;">
        <div class="section-title">Others</div>
        {{-- Things Preference --}}
        <a href="/profile/edit" class="profile-list-item" style="color: #1D1D1D; font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: 22px;">
            <span>Things Preference</span>
            <span style="color: #067CC2;">&gt;</span>
        </a>
        {{-- Order History --}}
        <a href="/my-order" class="profile-list-item" style="color: #1D1D1D; font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: 22px;">
            <span>Order History</span>
            <span style="color: #067CC2;">&gt;</span>
        </a>
    </div>

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
        .copyright {
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