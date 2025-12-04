<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
// use App\Models\Order; 
// use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CustController extends Controller
{
    public function myOrders()
    {
        // Data Dummy yang dipindahkan dari my_order.blade.php
        $orders = [
            [ 'id' => '#ORD-3045', 'date' => '25/09/2025', 'product' => 'Logitech G Pro X Headset', 'total' => 104, 'status' => 'Completed', 'img_file' => 'product_logitech.png' ],
            [ 'id' => '#ORD-3046', 'date' => '22/09/2025', 'product' => 'MSI Gaming Laptop i7 RTX 4060', 'total' => 1309, 'status' => 'Shipped', 'img_file' => 'product_msi.png' ],
            [ 'id' => '#ORD-3047', 'date' => '20/09/2025', 'product' => 'Adobe Creative Cloud License', 'total' => 29, 'status' => 'Processing', 'img_file' => 'icon-software.png' ],
            [ 'id' => '#ORD-3048', 'date' => '15/09/2025', 'product' => 'Samsung Galaxy Tab S9', 'total' => 709, 'status' => 'Cancelled', 'img_file' => 'product_samsung.png' ],
        ];
        
        return view('customer.my_order', compact('orders'));
    }

    public function showProd($id = 1)
    {
        
        // ambil product utama (throw 404 kalau nggak ada)
        $detailProduct = Product::findOrFail($id);

        // --- proses gambar product utama (sama seperti yang sudah kamu punya) ---
        $raw = $detailProduct->images; // kolom bisa berupa string, json, atau array
        $images = [];

        if (is_array($raw)) {
            $images = $raw;
        } elseif (is_string($raw)) {
            $decoded = json_decode($raw, true);
            $images = (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) ? $decoded : [$raw];
        }

        $imageUrls = collect($images)->map(function ($path) {
            if (!$path) return null;
            if (preg_match('#^https?://#i', $path)) return $path;
            if (Storage::disk('public')->exists($path)) return Storage::url($path);
            if (file_exists(public_path($path))) return asset($path);
            if (file_exists(public_path('images/'.$path))) return asset('images/'.$path);
            return null;
        })->filter()->values()->all();

        $prod_img = $imageUrls[0] ?? asset('images/default-product.png');

        // --- RELATED PRODUCTS: pastikan category_id ada dulu ---
        $relatedProducts = collect(); // default: empty collection

        if (!empty($detailProduct->category_id)) {
            $relatedProducts = Product::where('category_id', $detailProduct->category_id)
                ->where('id', '!=', $detailProduct->id)
                ->take(3)
                ->get();
        }

        // Jika ingin, attachkan image_url pada tiap related product (agar view mudah akses)
        $relatedProducts = $relatedProducts->map(function ($p) {
            // ambil raw images dari related product (bisa gunakan cast di model kalau mau)
            $raw = $p->images;
            $images = [];

            if (is_array($raw)) {
                $images = $raw;
            } elseif (is_string($raw)) {
                $decoded = json_decode($raw, true);
                $images = (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) ? $decoded : [$raw];
            }

            $first = collect($images)->first();
            $imageUrl = null;

            if ($first) {
                if (preg_match('#^https?://#i', $first)) {
                    $imageUrl = $first;
                } elseif (Storage::disk('public')->exists($first)) {
                    $imageUrl = Storage::url($first);
                } elseif (file_exists(public_path($first))) {
                    $imageUrl = asset($first);
                } elseif (file_exists(public_path('images/'.$first))) {
                    $imageUrl = asset('images/'.$first);
                }
            }

            // tambahkan attribute image_url (jika $p adalah model, ini dynamic attribute)
            $p->image_url = $imageUrl ?? asset('images/default-product.png');

            return $p;
        });

        // return ke view
        return view('customer.product-detail', compact('detailProduct', 'prod_img', 'imageUrls', 'relatedProducts'));
        }
}