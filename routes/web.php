<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ProductController;

Route::get('/', function () { return redirect()->route('products.index'); });
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/featured', [ProductController::class, 'featured'])->name('products.featured');
Route::get('/products/search', [ProductController::class, 'search'])->name('products.search');
Route::get('/products/category/{categoryId}', [ProductController::class, 'byCategory'])->name('products.category');
Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');
Route::post('/products/{id}/review', [ProductController::class, 'addReview'])->name('products.review');
Route::get('/api/products/search', [ProductController::class, 'apiSearch'])->name('api.products.search');

// customer (basic user)
Route::middleware(['auth'])->group(function(){
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
Route::middleware(['auth','ensure.seller'])->group(function(){
    Route::get('/seller', [SellerController::class, 'viewMainDashboard'])->name('seller.index');
    Route::get('/seller/product', [SellerController::class, 'viewProd']);
    Route::get('/seller/analytics', [SellerController::class, 'viewAnalyticsReview']);
    Route::get('/seller/inbox', [SellerController::class, 'viewReviewProduct']);
    Route::delete('/seller/remove/product/{id}', [SellerController::class, 'deleteProd']);
    Route::post('/seller/add/product', [SellerController::class,'addProduct'])->name('seller.add.product');
    Route::get('/seller/add/product', [SellerController::class,'viewAddProductForm'])->name('seller.view.add.product');
    Route::post('/seller/update/product/{id}', [SellerController::class, '']);
    Route::post('/seller/update/status', [SellerController::class, 'updateStatus']);
    Route::get('/seller/recent-order', [SellerController::class,'viewReecentOrder']);
    Route::get('seller/recent-order', [SellerController::class,'search'])->name('seller.recent.order');

});


// admin
Route::middleware(['auth:web', 'ensure.admin'])->group(function(){
    Route::get('/admin/dashboard', []);
});


require __DIR__.'/auth.php';

    //Route::post('/orders/{order}/ship', [OrderController::class, 'ship'])
    //->middleware('auth', 'can:ship,order');