<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Api;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderTrackingController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Storefront
|--------------------------------------------------------------------------
*/

Route::get('/', HomeController::class)->name('home');

Route::controller(ShopController::class)->group(function () {
    Route::get('/shop', 'index')->name('shop.index');
    Route::get('/deals', 'deals')->name('shop.deals');
    Route::get('/category/{category:slug}', 'category')->name('shop.category');
    Route::get('/brand/{brand:slug}', 'brand')->name('shop.brand');
    Route::get('/search/suggest', 'suggest')->name('search.suggest');
    Route::get('/offers/{offer}', 'offer')->name('shop.offer');
    Route::get('/brands', 'brands')->name('shop.brands');
});

Route::get('/product/{product:slug}', [ProductController::class, 'show'])->name('products.show');

Route::get('/track-order', OrderTrackingController::class)->middleware('throttle:order-tracking')->name('orders.track');

Route::controller(ServiceController::class)->prefix('services')->name('services.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/request', 'create')->name('request');
    Route::post('/request', 'store')->middleware('throttle:service-requests')->name('request.store');
    Route::get('/request/thanks', 'thanks')->name('thanks');
    Route::get('/portfolio', 'portfolio')->name('portfolio');
    Route::get('/{service:slug}', 'show')->name('show');
});

Route::controller(CartController::class)->prefix('cart')->name('cart.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::post('/{product}', 'store')->name('store');
    Route::patch('/{product}', 'update')->name('update');
    Route::delete('/{product}', 'destroy')->name('destroy');
});

/*
|--------------------------------------------------------------------------
| JSON API (uses the store's normal session login and CSRF protection)
|--------------------------------------------------------------------------
*/

Route::prefix('api')->name('api.')->group(function () {
    Route::controller(Api\ProductReviewController::class)->prefix('products/{product}')->name('products.')->group(function () {
        Route::get('/reviews', 'index')->name('reviews.index');
        Route::get('/rating', 'rating')->name('rating');
        Route::post('/reviews', 'store')->middleware(['auth', 'throttle:reviews'])->name('reviews.store');
    });
});

// Footer information pages: /about-us, /faq, /warranty, ...
Route::get('/{page}', PageController::class)->whereIn('page', array_keys(PageController::PAGES))->name('pages.show');

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:login');
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/{product}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

    Route::controller(CheckoutController::class)->prefix('checkout')->name('checkout.')->group(function () {
        Route::get('/', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/success/{order}', 'success')->name('success');
    });

    Route::controller(AccountController::class)->prefix('account')->name('account.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/orders', 'orders')->name('orders');
        Route::get('/orders/{order}', 'showOrder')->name('orders.show');
        Route::post('/orders/{order}/cancel', 'cancelOrder')->name('orders.cancel');
        Route::get('/service-requests', 'serviceRequests')->name('services');
        Route::get('/profile', 'profile')->name('profile');
        Route::put('/profile', 'updateProfile')->name('profile.update');
        Route::put('/password', 'updatePassword')->name('password.update');
    });
});

/*
|--------------------------------------------------------------------------
| Admin panel
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', Admin\DashboardController::class)->name('dashboard');
    Route::get('/analytics', [Admin\AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('/analytics/live', [Admin\AnalyticsController::class, 'live'])->name('analytics.live');

    Route::resource('products', Admin\ProductController::class)->except('show');
    Route::patch('/product-images/{image}/primary', [Admin\ProductImageController::class, 'makePrimary'])->name('product-images.primary');
    Route::delete('/product-images/{image}', [Admin\ProductImageController::class, 'destroy'])->name('product-images.destroy');

    Route::resource('categories', Admin\CategoryController::class)->except('show');
    Route::resource('brands', Admin\BrandController::class)->except('show');
    Route::resource('banners', Admin\BannerController::class)->except('show');

    Route::get('/orders', [Admin\OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [Admin\OrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/status', [Admin\OrderController::class, 'updateStatus'])->name('orders.status');
    Route::patch('/orders/{order}/payment', [Admin\OrderController::class, 'updatePayment'])->name('orders.payment');

    Route::resource('services', Admin\ServiceController::class)->except('show');
    Route::post('/portfolio', [Admin\PortfolioController::class, 'update'])->name('portfolio.update');
    Route::delete('/portfolio', [Admin\PortfolioController::class, 'destroy'])->name('portfolio.destroy');
    Route::resource('service-categories', Admin\ServiceCategoryController::class)->except(['index', 'show']);
    Route::resource('service-inquiries', Admin\ServiceInquiryController::class)->only(['index', 'show', 'update', 'destroy']);

    Route::get('/reviews', [Admin\ProductReviewController::class, 'index'])->name('reviews.index');
    Route::delete('/reviews/{review}', [Admin\ProductReviewController::class, 'destroy'])->name('reviews.destroy');

    Route::get('/customers', [Admin\CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/{user}', [Admin\CustomerController::class, 'show'])->name('customers.show');
});
