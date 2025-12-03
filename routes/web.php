<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\Auth\SocialAuthController;


Route::middleware('guest')->group(function () {
    Route::get('/login', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'store']);
    Route::get('login/google', [SocialAuthController::class, 'redirectToGoogle'])->name('login.google');
    Route::get('login/google/callback', [SocialAuthController::class, 'handleGoogleCallback'])->name('login.google.callback');
    Route::get('/register', [\App\Http\Controllers\Auth\RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [\App\Http\Controllers\Auth\RegisteredUserController::class, 'store']);
});

// redirect based on role after login
Route::get('/', function () {
    if (! auth()->check()) {
        return redirect()->route('login');
    }
    $user = auth()->user();
    return match ($user->role) {
        'admin' => redirect()->route('admin.dashboard'),
        'seller' => redirect()->route('seller.index'),
        default => redirect()->route('dashboard'),
    };
});



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

    Route::get('/orders/{id}/ship-data', [SellerController::class, 'shipData'])->name('seller.orders.shipData');
    Route::post('/orders/{id}/ship', [SellerController::class, 'ship'])->name('seller.orders.ship');
    Route::get('/dashboard', [SellerController::class, 'viewMainDashboard'])->name('seller.index');
    Route::get('/product', [SellerController::class, 'viewProd'])->name('seller.products');
    Route::get('/analytics', [SellerController::class, 'viewAnalyticsReview'])->name('seller.analytics');
    Route::get('/inbox', [SellerController::class, 'feedback'])->name('seller.inbox');
    Route::delete('/remove/product/{id}', [SellerController::class, 'deleteProd']);
    Route::post('/add/product', [SellerController::class,'addProduct'])->name('seller.add.product');
    Route::get('/add/product', [SellerController::class,'viewAddProductForm'])->name('seller.view.add.product');
    Route::post('/update/product/{id}', [SellerController::class, '']);
    Route::post('/update/status', [SellerController::class, 'updateStatus']);
    Route::get('/recent-order', [SellerController::class,'viewRecentOrder'])->name('seller.orders');
    Route::get('/recent-order/search', [SellerController::class,'viewRecentOrder'])->name('search.seller.orders');
    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('settings/owner', [SettingsController::class, 'updateOwner'])->name('settings.owner.update');
    Route::put('settings/store', [SettingsController::class, 'updateStore'])->name('settings.store.update');
    Route::post('settings/password', [SettingsController::class, 'updatePassword'])->name('settings.password.update');
    Route::get('/seller/orders-over-time/data', [SellerController::class, 'data'])->name('seller.orders-over-time.data');
});



// admin
Route::middleware(['auth', 'ensure.admin'])->prefix('admin')->group(function(){
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/transactions', [AdminController::class, 'dataTransaction'])->name('admin.transactions.index');
    Route::get('/shipping', [AdminController::class, 'shippingIndex'])->name('admin.shipping.index');
    Route::post('/add/shipping/json', [AdminController::class, 'addShipping'])->name('admin.shipping.store');
    Route::put('/shipping/{shipping}', [AdminController::class, 'editShipping'])->name('admin.shipping.update');
    Route::get('/products', [AdminController::class, 'productIndex'])->name('admin.products.index');
    Route::patch('/products/{product}/toggle-status', [AdminController::class, 'toggleStatus'])->name('admin.products.toggle-status');
    Route::get('/products/{product}/json', [AdminController::class, 'productJson'])->name('admin.products.json');
    Route::patch('/customers/{user}/edit', [AdminController::class, 'updateUser'])->name('admin.customers.update');
    Route::get('/customers/{user}/json', [AdminController::class, 'userJson'])->name('admin.customers.json');
    Route::get('/customers', [AdminController::class, 'viewUser'])->name('admin.customers.index');
    Route::patch('/customers/{user}/status', [AdminController::class, 'updateStatus'])->name('admin.customers.update-status');
    Route::get('/sellers', [AdminController::class, 'sellerIndex'])->name('admin.sellers.index');
    Route::get('/sellers/{seller}/json', [AdminController::class, 'sellerJson'])->name('admin.sellers.json');
    Route::get('/admin/sellers/{seller}/products', [AdminController::class, 'sellerProductsJson']);
    Route::patch('/sellers/{seller}/status', [AdminController::class, 'updateStatusSeller'])->name('admin.sellers.update-status');
});


require __DIR__.'/auth.php';

    //Route::post('/orders/{order}/ship', [OrderController::class, 'ship'])
    //->middleware('auth', 'can:ship,order');