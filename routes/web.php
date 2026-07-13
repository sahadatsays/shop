<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');
Route::view('/products/ranger-field-jacket', 'products.show')->name('product.show');
