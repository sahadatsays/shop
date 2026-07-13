<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');
Route::view('/shop', 'shop')->name('shop');
Route::view('/products/ranger-field-jacket', 'products.show')->name('product.show');
