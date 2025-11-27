<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\Seller\SettingsController;
use App\Http\Controllers\Auth\SocialAuthController;
Route::redirect('/',  '/login');


Route::get('login/google', [SocialAuthController::class, 'redirectToGoogle'])->name('login.google');
Route::get('login/google/callback', [SocialAuthController::class, 'handleGoogleCallback']);

//Route::get('/products', [ProductController::class, 'index'])->name('products.index');
//Route::get('/products/featured', [ProductController::class, 'featured'])->name('products.featured');
//Route::get('/products/search', [ProductController::class, 'search'])->name('products.search');
//Route::get('/products/category/{categoryId}', [ProductController::class, 'byCategory'])->name('products.category');
//Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');
//Route::post('/products/{id}/review', [ProductController::class, 'addReview'])->name('products.review');
// Route::get('/api/products/search', [ProductController::class, 'apiSearch'])->name('api.products.search');

// customer (basic user)
Route::middleware(['auth', 'ensure.active'])->group(function(){
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{productId}', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/update/{itemId}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove/{itemId}', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
    Route::get('/become-seller', [SellerController::class, 'store'])->name('become.seller');
});


// seller
Route::middleware(['auth','ensure.seller', 'ensure.active'])->prefix('seller')->group(function(){
    Route::get('/dashboard', [SellerController::class, 'viewMainDashboard'])->name('seller.index');
    Route::get('/product', [SellerController::class, 'viewProd'])->name('seller.products');
    Route::get('/analytics', [SellerController::class, 'viewAnalyticsReview'])->name('seller.analytics');
    Route::get('/inbox', [SellerController::class, 'feedback'])->name('seller.inbox');
    Route::delete('/remove/product/{id}', [SellerController::class, 'deleteProd']);
    Route::post('/add/product', [SellerController::class,'addProduct'])->name('seller.add.product');
    Route::get('/add/product', [SellerController::class,'viewAddProductForm'])->name('seller.view.add.product');
    Route::post('/update/product/{id}', [SellerController::class, '']);
    Route::post('/update/status', [SellerController::class, 'updateStatus']);
    Route::get('/recent-order', [SellerController::class,'viewReecentOrder'])->name('seller.orders');
    Route::get('/recent-order', [SellerController::class,'search'])->name('seller.recent.order');
});

Route::middleware(['auth','ensure.seller', 'ensure.active'])->prefix('seller')->name('seller.')->group(function () {
        Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::post('settings/owner', [SettingsController::class, 'updateOwner'])->name('settings.owner.update');
        Route::post('settings/store', [SettingsController::class, 'updateStore'])->name('settings.store.update');
        Route::post('settings/password', [SettingsController::class, 'updatePassword'])->name('settings.password.update');
    });


// admin
Route::middleware(['auth:web', 'ensure.admin'])->prefix('admin')->group(function(){
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/transactions', [AdminController::class, 'transactions'])->name('admin.transactions.index');
    Route::get('/shipping', [AdminController::class, 'shippingIndex'])->name('admin.shipping.index');
    Route::post('/add/shipping', [AdminController::class, 'addShipping'])->name('admin.shipping.store');
    Route::put('/admin/shipping/{shipping}', [AdminController::class, 'update'])->name('admin.shipping.update');
    Route::get('/products', [AdminController::class, 'productIndex'])->name('admin.products.index');
    Route::patch('/products/{product}/toggle-status', [AdminController::class, 'toggleStatus'])->name('admin.products.toggle-status');
    Route::get('/admin/products/{product}/json', [AdminController::class, 'json'])->name('admin.products.json');
    Route::patch('/admin/customers/{user}/edit', [AdminController::class, 'update'])->name('admin.customers.update');
    Route::get('/customers', [AdminController::class, 'index'])->name('admin.customers.index');
    Route::patch('/customers/{user}/status', [AdminController::class, 'updateStatus'])->name('admin.customers.update-status');
    Route::get('/sellers', [AdminController::class, 'index'])->name('admin.sellers.index');
    Route::get('/sellers/{seller}/json', [AdminController::class, 'json'])->name('admin.sellers.json');
    Route::patch('/sellers/{seller}/status', [AdminController::class, 'updateStatus'])->name('admin.sellers.update-status');
});


require __DIR__.'/auth.php';

    //Route::post('/orders/{order}/ship', [OrderController::class, 'ship'])
    //->middleware('auth', 'can:ship,order');