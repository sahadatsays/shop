<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CustomerAuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');
Route::get('/shop', [ShopController::class, 'index'])->name('shop');
Route::view('/categories', 'categories')->name('categories');
Route::view('/search', 'search')->name('search');

Route::get('/products/{product:slug?}', [ProductController::class, 'show'])->name('product.show');

Route::get('/cart', [CartController::class, 'index'])->name('cart');
Route::post('/cart/items', [CartController::class, 'store'])->name('cart.items.store');
Route::patch('/cart/items/{cartItem}', [CartController::class, 'update'])->name('cart.items.update');
Route::delete('/cart/items/{cartItem}', [CartController::class, 'destroy'])->name('cart.items.destroy');
Route::post('/cart/save', [CartController::class, 'save'])->name('cart.save');
Route::post('/cart/validate', [CartController::class, 'validateCart'])->name('cart.validate');

Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');

Route::post('/login', [CustomerAuthController::class, 'login'])->name('login.store');
Route::post('/logout', [CustomerAuthController::class, 'logout'])->name('logout');

Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist');
Route::post('/wishlist/items', [WishlistController::class, 'store'])->name('wishlist.items.store');
Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
Route::delete('/wishlist/items/{wishlistItem}', [WishlistController::class, 'destroy'])->name('wishlist.items.destroy');
Route::post('/wishlist/items/{wishlistItem}/move-to-cart', [WishlistController::class, 'moveToCart'])->name('wishlist.items.move-to-cart');
Route::post('/wishlist/move-all-to-cart', [WishlistController::class, 'moveAllToCart'])->name('wishlist.move-all-to-cart');
Route::delete('/wishlist', [WishlistController::class, 'clear'])->name('wishlist.clear');
Route::view('/compare', 'compare')->name('compare');
Route::view('/account', 'account')->name('account');
Route::view('/account/orders', 'account-orders')->name('account.orders');
Route::view('/account/settings', 'account-settings')->name('account.settings');
Route::view('/account/addresses', 'account-addresses')->name('account.addresses');
Route::view('/account/reviews', 'account-reviews')->name('account.reviews');
Route::view('/account/notifications', 'account-notifications')->name('account.notifications');
Route::view('/track', 'track')->name('track');
Route::view('/login', 'login')->name('login');
Route::view('/register', 'register')->name('register');
Route::view('/forgot-password', 'forgot-password')->name('password.forgot');
Route::view('/support', 'support')->name('support');
Route::view('/about', 'about')->name('about');
Route::view('/contact', 'contact')->name('contact');
