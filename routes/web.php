<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\CustomerDashboardController;
use App\Http\Controllers\FarmerController;
use App\Http\Controllers\FarmerDashboardController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MarketController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::post('/contact', [HomeController::class, 'sendContact'])->middleware('throttle:8,1')->name('contact.send');
Route::get('/sitemap', [HomeController::class, 'sitemap'])->name('sitemap');

Route::get('/markets', [MarketController::class, 'index'])->name('markets.index');
Route::get('/markets/{market}', [MarketController::class, 'show'])->name('markets.show');
Route::get('/farmers', [FarmerController::class, 'index'])->name('farmers.index');
Route::get('/farmers/{farmer}', [FarmerController::class, 'show'])->name('farmers.show');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
Route::get('/search', [ProductController::class, 'search'])->name('search');
Route::get('/search/suggest', [ProductController::class, 'suggest'])->name('search.suggest');
Route::get('/privacy', [HomeController::class, 'privacy'])->name('privacy');
Route::get('/terms', [HomeController::class, 'terms'])->name('terms');
Route::post('/chatbot', [ChatbotController::class, 'ask'])->middleware('throttle:30,1')->name('chatbot.ask');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register/customer', [AuthController::class, 'registerCustomer'])->name('register.customer');
    Route::post('/register/farmer', [AuthController::class, 'registerFarmer'])->name('register.farmer');
    Route::get('/password/reset', [AuthController::class, 'showForgot'])->name('password.request');
    Route::post('/password/email', [AuthController::class, 'sendReset'])->middleware('throttle:5,1')->name('password.email');
    Route::get('/password/reset/{token}', [AuthController::class, 'showReset'])->name('password.reset');
    Route::post('/password/reset', [AuthController::class, 'reset'])->name('password.update');
});

Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware(['auth', 'role:customer'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');
    Route::get('/favorites', [CustomerDashboardController::class, 'favorites'])->name('favorites');
    Route::post('/favorites', [FavoriteController::class, 'toggle'])->name('favorites.toggle');
    Route::get('/notifications', [CustomerDashboardController::class, 'notifications'])->name('notifications');
    Route::get('/notifications/poll', [CustomerDashboardController::class, 'poll'])->name('notifications.poll');
    Route::post('/notifications/{notification}/read', [CustomerDashboardController::class, 'read'])->name('notifications.read');
    Route::post('/notifications/read-all', [CustomerDashboardController::class, 'readAll'])->name('notifications.read-all');
    Route::get('/reviews', [CustomerDashboardController::class, 'reviews'])->name('reviews');
    Route::get('/profile', [CustomerDashboardController::class, 'profile'])->name('profile');
    Route::put('/profile', [CustomerDashboardController::class, 'updateProfile'])->name('profile.update');
    Route::put('/password', [CustomerDashboardController::class, 'updatePassword'])->name('password.update');
    Route::get('/orders', [OrderController::class, 'history'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::put('/orders/{order}', [OrderController::class, 'update'])->name('orders.update');
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('/orders/{order}/reorder', [OrderController::class, 'reorder'])->name('orders.reorder');
    Route::get('/orders/{order}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice');
    Route::post('/orders/{order}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::post('/reviews/{review}/helpful', [ReviewController::class, 'helpful'])->name('reviews.helpful');
});

Route::post('/guest/quick/{product}', [OrderController::class, 'guestQuick'])->middleware('throttle:12,1')->name('guest.quick');
Route::get('/guest/cart', [CartController::class, 'guestIndex'])->name('guest.cart');
Route::post('/guest/cart/{product}', [CartController::class, 'guestAdd'])->name('guest.cart.add');
Route::post('/guest/cart/{product}/remove', [CartController::class, 'guestRemove'])->name('guest.cart.remove');
Route::get('/guest/checkout', [OrderController::class, 'guestCreate'])->name('guest.checkout');
Route::post('/guest/checkout', [OrderController::class, 'guestStore'])->middleware('throttle:8,1')->name('guest.checkout.store');

Route::middleware(['auth', 'role:customer'])->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::put('/cart/{product}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{product}', [CartController::class, 'remove'])->name('cart.remove');
    Route::get('/checkout', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/checkout', [OrderController::class, 'store'])->name('orders.store');
});

Route::middleware(['auth', 'role:farmer'])->prefix('farmer')->name('farmer.')->group(function () {
    Route::get('/dashboard', [FarmerDashboardController::class, 'index'])->name('dashboard');
    Route::get('/products', [FarmerDashboardController::class, 'products'])->name('products.index');
    Route::post('/products', [FarmerDashboardController::class, 'storeProduct'])->name('products.store');
    Route::put('/products/{product}', [FarmerDashboardController::class, 'updateProduct'])->name('products.update');
    Route::delete('/products/{product}', [FarmerDashboardController::class, 'destroyProduct'])->name('products.destroy');
    Route::post('/products/template', [FarmerDashboardController::class, 'saveTemplate'])->name('products.template.save');
    Route::post('/products/template/apply', [FarmerDashboardController::class, 'applyTemplate'])->name('products.template.apply');
    Route::get('/orders', [FarmerDashboardController::class, 'ordersIndex'])->name('orders.index');
    Route::post('/orders/{order}/accept', [FarmerDashboardController::class, 'accept'])->name('orders.accept');
    Route::post('/orders/{order}/decline', [FarmerDashboardController::class, 'decline'])->name('orders.decline');
    Route::post('/orders/{order}/ready', [FarmerDashboardController::class, 'ready'])->name('orders.ready');
    Route::post('/orders/{order}/complete', [FarmerDashboardController::class, 'complete'])->name('orders.complete');
    Route::get('/slots', [FarmerDashboardController::class, 'slots'])->name('slots.index');
    Route::put('/slots', [FarmerDashboardController::class, 'updateSlots'])->name('slots.update');
    Route::get('/insights', [FarmerDashboardController::class, 'insights'])->name('insights');
    Route::get('/reviews', [FarmerDashboardController::class, 'reviews'])->name('reviews');
    Route::post('/reviews/{review}/reply', [FarmerDashboardController::class, 'reply'])->name('reviews.reply');
    Route::get('/profile', [FarmerDashboardController::class, 'profileEdit'])->name('profile');
    Route::put('/profile', [FarmerDashboardController::class, 'profileUpdate'])->name('profile.update');
    Route::get('/notifications/poll', [CustomerDashboardController::class, 'poll'])->name('notifications.poll');
    Route::get('/notifications', [CustomerDashboardController::class, 'notifications'])->name('notifications');
    Route::post('/notifications/read-all', [CustomerDashboardController::class, 'readAll'])->name('notifications.read-all');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/users', [AdminDashboardController::class, 'users'])->name('users.index');
    Route::get('/users/{user}', [AdminDashboardController::class, 'showUser'])->name('users.show');
    Route::post('/users/{user}/toggle', [AdminDashboardController::class, 'toggleUser'])->name('users.toggle');
    Route::post('/users/{user}/status', [AdminDashboardController::class, 'setUserStatus'])->name('users.status');
    Route::post('/users/{user}/pin', [AdminDashboardController::class, 'setUserPin'])->name('users.pin');
    Route::post('/pin', [AdminDashboardController::class, 'setOwnPin'])->name('pin.update');
    Route::post('/orders/{order}/payment', [AdminDashboardController::class, 'setPayment'])->name('orders.payment');
    Route::get('/farmers', [AdminDashboardController::class, 'farmers'])->name('farmers.index');
    Route::get('/farmers/{farmer}', [AdminDashboardController::class, 'showFarmer'])->name('farmers.show');
    Route::get('/orders', [AdminDashboardController::class, 'orders'])->name('orders.index');
    Route::get('/orders/{order}', [AdminDashboardController::class, 'showOrder'])->name('orders.show');
    Route::post('/farmers/{farmer}/decide', [AdminDashboardController::class, 'decideFarmer'])->name('farmers.decide');
    Route::put('/farmers/{farmer}', [AdminDashboardController::class, 'updateFarmer'])->name('farmers.update');
    Route::post('/farmers/{farmer}/suspend', [AdminDashboardController::class, 'suspendFarmer'])->name('farmers.suspend');
    Route::get('/markets', [AdminDashboardController::class, 'markets'])->name('markets.index');
    Route::post('/markets', [AdminDashboardController::class, 'storeMarket'])->name('markets.store');
    Route::put('/markets/{market}', [AdminDashboardController::class, 'updateMarket'])->name('markets.update');
    Route::delete('/markets/{market}', [AdminDashboardController::class, 'destroyMarket'])->name('markets.destroy');
    Route::get('/categories', [AdminDashboardController::class, 'categories'])->name('categories.index');
    Route::post('/categories', [AdminDashboardController::class, 'storeCategory'])->name('categories.store');
    Route::put('/categories/{category}', [AdminDashboardController::class, 'updateCategory'])->name('categories.update');
    Route::delete('/categories/{category}', [AdminDashboardController::class, 'destroyCategory'])->name('categories.destroy');
    Route::get('/products', [AdminDashboardController::class, 'products'])->name('products.index');
    Route::put('/products/{product}', [AdminDashboardController::class, 'updateProduct'])->name('products.update');
    Route::delete('/products/{product}', [AdminDashboardController::class, 'destroyProduct'])->name('products.destroy');
    Route::get('/reviews', [AdminDashboardController::class, 'reviews'])->name('reviews.index');
    Route::post('/reviews/{review}', [AdminDashboardController::class, 'moderateReview'])->name('reviews.moderate');
    Route::delete('/reviews/{review}', [AdminDashboardController::class, 'destroyReview'])->name('reviews.destroy');
    Route::get('/reports', [AdminDashboardController::class, 'reports'])->name('reports.index');
    Route::get('/reports/export', [AdminDashboardController::class, 'exportReports'])->name('reports.export');
    Route::get('/announcements', [AdminDashboardController::class, 'announcements'])->name('announcements.index');
    Route::post('/announcements', [AdminDashboardController::class, 'storeAnnouncement'])->name('announcements.store');
    Route::put('/announcements/{announcement}', [AdminDashboardController::class, 'updateAnnouncement'])->name('announcements.update');
    Route::delete('/announcements/{announcement}', [AdminDashboardController::class, 'destroyAnnouncement'])->name('announcements.destroy');
    Route::get('/faqs', [AdminDashboardController::class, 'faqs'])->name('faqs.index');
    Route::get('/chatbot-faqs', [AdminDashboardController::class, 'faqs'])->name('chatbot-faqs.index');
    Route::post('/faqs', [AdminDashboardController::class, 'storeFaq'])->name('faqs.store');
    Route::delete('/faqs/{faq}', [AdminDashboardController::class, 'destroyFaq'])->name('faqs.destroy');
    Route::get('/settings', [AdminDashboardController::class, 'settings'])->name('settings');
    Route::put('/settings', [AdminDashboardController::class, 'updateSettings'])->name('settings.update');
    Route::get('/notifications/poll', [CustomerDashboardController::class, 'poll'])->name('notifications.poll');
});
