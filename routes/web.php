<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\CompareController;
use App\Http\Controllers\CustomerAuthController;
use App\Http\Controllers\CustomerNotificationController;
use App\Http\Controllers\CustomerOrderController;
use App\Http\Controllers\CustomerPasswordResetController;
use App\Http\Controllers\CustomerProfileController;
use App\Http\Controllers\CustomerRegistrationController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\SubscribeNewsletterController;
use App\Http\Controllers\TrackOrderController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/collections/{collection:slug}', [CollectionController::class, 'show'])->name('collections.show');
Route::get('/shop', [ShopController::class, 'index'])->name('shop');
Route::view('/categories', 'categories')->name('categories');
Route::get('/search', [SearchController::class, 'index'])->name('search');
Route::get('/search/suggest', [SearchController::class, 'suggest'])->name('search.suggest');
Route::post('/newsletter', SubscribeNewsletterController::class)
    ->middleware('throttle:6,1')
    ->name('newsletter.subscribe');

Route::get('/products/{product:slug?}', [ProductController::class, 'show'])->name('product.show');

Route::get('/cart', [CartController::class, 'index'])->name('cart');
Route::post('/cart/items', [CartController::class, 'store'])->name('cart.items.store');
Route::patch('/cart/items/{cartItem}', [CartController::class, 'update'])->name('cart.items.update');
Route::delete('/cart/items/{cartItem}', [CartController::class, 'destroy'])->name('cart.items.destroy');
Route::post('/cart/save', [CartController::class, 'save'])->name('cart.save');
Route::post('/cart/validate', [CartController::class, 'validateCart'])->name('cart.validate');

Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/checkout/confirmation/{order:order_number}', [CheckoutController::class, 'confirmation'])->name('checkout.confirmation');

Route::middleware('customer.guest')->group(function (): void {
    Route::view('/login', 'login')->name('login');
    Route::post('/login', [CustomerAuthController::class, 'login'])
        ->middleware('throttle:customer-login')
        ->name('login.store');

    Route::view('/register', 'register')->name('register');
    Route::post('/register', [CustomerRegistrationController::class, 'store'])
        ->middleware('throttle:customer-register')
        ->name('register.store');

    Route::view('/forgot-password', 'forgot-password')->name('password.forgot');
    Route::post('/forgot-password', [CustomerPasswordResetController::class, 'sendLink'])
        ->middleware('throttle:customer-password-reset')
        ->name('password.email');

    Route::get('/reset-password/{token}', [CustomerPasswordResetController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [CustomerPasswordResetController::class, 'reset'])
        ->middleware('throttle:customer-password-reset')
        ->name('password.update');

    Route::get('/auth/{provider}', [SocialAuthController::class, 'redirect'])->name('auth.social.redirect');
    Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])->name('auth.social.callback');
});

Route::post('/logout', [CustomerAuthController::class, 'logout'])
    ->middleware('customer.auth')
    ->name('logout');

Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist');
Route::post('/wishlist/items', [WishlistController::class, 'store'])->name('wishlist.items.store');
Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
Route::delete('/wishlist/items/{wishlistItem}', [WishlistController::class, 'destroy'])->name('wishlist.items.destroy');
Route::post('/wishlist/items/{wishlistItem}/move-to-cart', [WishlistController::class, 'moveToCart'])->name('wishlist.items.move-to-cart');
Route::post('/wishlist/move-all-to-cart', [WishlistController::class, 'moveAllToCart'])->name('wishlist.move-all-to-cart');
Route::delete('/wishlist', [WishlistController::class, 'clear'])->name('wishlist.clear');
Route::get('/compare', [CompareController::class, 'index'])->name('compare');
Route::post('/compare/items', [CompareController::class, 'store'])->name('compare.items.store');
Route::post('/compare/toggle', [CompareController::class, 'toggle'])->name('compare.toggle');
Route::delete('/compare/items/{compareItem}', [CompareController::class, 'destroy'])->name('compare.items.destroy');
Route::delete('/compare', [CompareController::class, 'clear'])->name('compare.clear');

Route::middleware('customer.auth')->group(function (): void {
    Route::get('/account/notifications', [CustomerNotificationController::class, 'index'])->name('account.notifications');
    Route::patch('/account/notifications/{notification}/read', [CustomerNotificationController::class, 'markRead'])->name('account.notifications.read');
    Route::post('/account/notifications/mark-all-read', [CustomerNotificationController::class, 'markAllRead'])->name('account.notifications.mark-all-read');
    Route::get('/account/orders', [CustomerOrderController::class, 'index'])->name('account.orders');
    Route::get('/account/orders/{order:order_number}', [CustomerOrderController::class, 'show'])
        ->middleware('order.tracking')
        ->name('account.orders.show');

    Route::get('/account', fn () => view('account'))->name('account');
    Route::get('/account/settings', [CustomerProfileController::class, 'show'])->name('account.settings');
    Route::match(['put', 'patch', 'post'], '/account/settings', [CustomerProfileController::class, 'update'])->name('account.settings.update');
    Route::get('/profile', [CustomerProfileController::class, 'show'])->name('profile');
    Route::match(['put', 'patch', 'post'], '/profile', [CustomerProfileController::class, 'update'])->name('profile.update');
    Route::view('/account/addresses', 'account-addresses')->name('account.addresses');
    Route::view('/account/reviews', 'account-reviews')->name('account.reviews');
});

Route::get('/track-order', [TrackOrderController::class, 'create'])->name('track-order.create');
Route::post('/track-order', [TrackOrderController::class, 'store'])->name('track-order.store');
Route::get('/track-order/{order:order_number}', [TrackOrderController::class, 'show'])
    ->middleware('order.tracking')
    ->name('track-order.show');

Route::redirect('/track', '/track-order')->name('track');
Route::view('/support', 'support')->name('support');
Route::view('/about', 'about')->name('about');
Route::view('/contact', 'contact')->name('contact');
