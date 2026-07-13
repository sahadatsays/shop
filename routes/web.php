<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');
Route::view('/shop', 'shop')->name('shop');
Route::view('/categories', 'categories')->name('categories');
Route::view('/search', 'search')->name('search');
Route::view('/cart', 'cart')->name('cart');
Route::view('/products/ranger-field-jacket', 'products.show')->name('product.show');
