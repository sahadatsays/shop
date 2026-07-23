<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CustomerAuthController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');
Route::view('/shop', 'shop')->name('shop');
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

Route::view('/wishlist', 'wishlist')->name('wishlist');
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
