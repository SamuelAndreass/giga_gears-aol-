<?php

use App\Http\Controllers\ProfileController;
use App\Models\SellerProfile;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SellerController;
use Illuminate\Auth\Middleware\Authenticate;
use App\Http\Controllers\CartController;            
Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

//profile settings
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::middleware(['auth', 'verified'])->group(function(){
    Route::get('/customer/dashboard', function(){
        return view('customer.home');
    })->name('customer.dashboard');
});

Route::middleware(['auth'])->group(function(){
 Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{productId}', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/update/{itemId}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove/{itemId}', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');

});

Route::middleware(['auth', 'verified','ensure.seller'])->group(function(){

});


//admin dashboard
Route::middleware(['auth:web', 'ensure.admin'])->group(function(){
    Route::get('/admin/dashboard', function(){
        return view('admin.admin-dashboard');
    })->name('admin.dashboard');
});


require __DIR__.'/auth.php';
