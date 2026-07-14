<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');
Route::view('/shop', 'shop')->name('shop');
Route::view('/categories', 'categories')->name('categories');
Route::view('/search', 'search')->name('search');
Route::view('/cart', 'cart')->name('cart');
Route::view('/checkout', 'checkout')->name('checkout');
Route::view('/wishlist', 'wishlist')->name('wishlist');
Route::view('/compare', 'compare')->name('compare');
Route::view('/account', 'account')->name('account');
Route::view('/account/orders', 'account-orders')->name('account.orders');
Route::view('/track', 'track')->name('track');
Route::view('/products/ranger-field-jacket', 'products.show')->name('product.show');
