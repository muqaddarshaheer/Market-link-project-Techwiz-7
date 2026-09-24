<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CustomerDashboardController;
use App\Http\Controllers\FarmerController;
use App\Http\Controllers\FarmerDashboardController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MarketController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| MarketLink Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::post('/contact', [HomeController::class, 'contactSubmit'])->name('contact.submit');
Route::get('/sitemap', [HomeController::class, 'sitemap'])->name('sitemap');

Route::get('/markets', [MarketController::class, 'index'])->name('markets.index');
Route::get('/markets/{market}', [MarketController::class, 'show'])->name('markets.show');

Route::get('/farmers', [FarmerController::class, 'index'])->name('farmers.index');
Route::get('/farmers/{farmer}', [FarmerController::class, 'show'])->name('farmers.show');

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/autocomplete', [ProductController::class, 'autocomplete'])->name('products.autocomplete');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

Route::post('/chatbot/ask', [HomeController::class, 'chatbotAsk'])->name('chatbot.ask');
Route::get('/chatbot/suggestions', [HomeController::class, 'chatbotSuggestions'])->name('chatbot.suggestions');

// Auth
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:6,1');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Authenticated shared
Route::middleware('auth')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/poll', [NotificationController::class, 'poll'])->name('notifications.poll');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
});

// Customer
Route::middleware(['auth', 'role:customer'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');
    Route::get('/orders', [CustomerDashboardController::class, 'orders'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{order}/edit', [OrderController::class, 'edit'])->name('orders.edit');
    Route::put('/orders/{order}', [OrderController::class, 'update'])->name('orders.update');
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('/orders/{order}/reorder', [OrderController::class, 'reorder'])->name('orders.reorder');
    Route::get('/orders/{order}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice');
    Route::get('/orders/{order}/review', [ReviewController::class, 'create'])->name('reviews.create');
    Route::post('/orders/{order}/review', [ReviewController::class, 'store'])->name('reviews.store');

    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
    Route::put('/cart/{item}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{item}', [CartController::class, 'destroy'])->name('cart.destroy');
    Route::delete('/cart', [CartController::class, 'clear'])->name('cart.clear');
    Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout');
    Route::post('/checkout', [OrderController::class, 'store'])->name('checkout.store');

    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/favorites/toggle', [FavoriteController::class, 'toggle'])->name('favorites.toggle');

    Route::get('/profile', [CustomerDashboardController::class, 'profile'])->name('profile');
    Route::put('/profile', [CustomerDashboardController::class, 'updateProfile'])->name('profile.update');
    Route::put('/password', [CustomerDashboardController::class, 'updatePassword'])->name('password.update');
});

// Also allow cart routes via shorter aliases for convenience
Route::middleware(['auth', 'role:customer'])->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
});

Route::post('/reviews/{review}/helpful', [ReviewController::class, 'helpful'])
    ->middleware('auth')
    ->name('reviews.helpful');

// Farmer
Route::middleware(['auth', 'role:farmer'])->prefix('farmer')->name('farmer.')->group(function () {
    Route::get('/dashboard', [FarmerDashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [FarmerDashboardController::class, 'profile'])->name('profile');
    Route::put('/profile', [FarmerDashboardController::class, 'updateProfile'])->name('profile.update');
    Route::put('/password', [FarmerDashboardController::class, 'updatePassword'])->name('password.update');

    Route::middleware('farmer.approved')->group(function () {
        Route::get('/products', [FarmerDashboardController::class, 'products'])->name('products.index');
        Route::get('/products/create', [FarmerDashboardController::class, 'createProduct'])->name('products.create');
        Route::post('/products', [FarmerDashboardController::class, 'storeProduct'])->name('products.store');
        Route::get('/products/{product}/edit', [FarmerDashboardController::class, 'editProduct'])->name('products.edit');
        Route::put('/products/{product}', [FarmerDashboardController::class, 'updateProduct'])->name('products.update');
        Route::delete('/products/{product}', [FarmerDashboardController::class, 'destroyProduct'])->name('products.destroy');
        Route::post('/products/copy-weekly-stock', [FarmerDashboardController::class, 'copyWeeklyStock'])->name('products.copy-stock');

        Route::get('/orders', [FarmerDashboardController::class, 'orders'])->name('orders.index');
        Route::get('/orders/{order}', [FarmerDashboardController::class, 'showOrder'])->name('orders.show');
        Route::post('/orders/{order}/status', [FarmerDashboardController::class, 'updateOrderStatus'])->name('orders.status');

        Route::get('/reviews', [FarmerDashboardController::class, 'reviews'])->name('reviews.index');
        Route::post('/reviews/{review}/reply', [FarmerDashboardController::class, 'replyReview'])->name('reviews.reply');
        Route::get('/insights', [FarmerDashboardController::class, 'insights'])->name('insights');
    });
});

// Admin
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::get('/users', [AdminDashboardController::class, 'users'])->name('users.index');
    Route::post('/users/{user}/status', [AdminDashboardController::class, 'updateUserStatus'])->name('users.status');

    Route::get('/farmers', [AdminDashboardController::class, 'farmers'])->name('farmers.index');
    Route::post('/farmers/{farmer}/approve', [AdminDashboardController::class, 'approveFarmer'])->name('farmers.approve');
    Route::post('/farmers/{farmer}/reject', [AdminDashboardController::class, 'rejectFarmer'])->name('farmers.reject');

    Route::get('/markets', [AdminDashboardController::class, 'markets'])->name('markets.index');
    Route::get('/markets/create', [AdminDashboardController::class, 'createMarket'])->name('markets.create');
    Route::post('/markets', [AdminDashboardController::class, 'storeMarket'])->name('markets.store');
    Route::get('/markets/{market}/edit', [AdminDashboardController::class, 'editMarket'])->name('markets.edit');
    Route::put('/markets/{market}', [AdminDashboardController::class, 'updateMarket'])->name('markets.update');
    Route::delete('/markets/{market}', [AdminDashboardController::class, 'destroyMarket'])->name('markets.destroy');

    Route::get('/categories', [AdminDashboardController::class, 'categories'])->name('categories.index');
    Route::post('/categories', [AdminDashboardController::class, 'storeCategory'])->name('categories.store');
    Route::put('/categories/{category}', [AdminDashboardController::class, 'updateCategory'])->name('categories.update');
    Route::delete('/categories/{category}', [AdminDashboardController::class, 'destroyCategory'])->name('categories.destroy');

    Route::get('/products', [AdminDashboardController::class, 'products'])->name('products.index');
    Route::post('/products/{product}/toggle', [AdminDashboardController::class, 'toggleProduct'])->name('products.toggle');
    Route::delete('/products/{product}', [AdminDashboardController::class, 'destroyProduct'])->name('products.destroy');

    Route::get('/reviews', [AdminDashboardController::class, 'reviews'])->name('reviews.index');
    Route::post('/reviews/{review}/moderate', [AdminDashboardController::class, 'moderateReview'])->name('reviews.moderate');

    Route::get('/reports', [AdminDashboardController::class, 'reports'])->name('reports.index');
    Route::get('/reports/export', [AdminDashboardController::class, 'exportCsv'])->name('reports.export');

    Route::get('/announcements', [AdminDashboardController::class, 'announcements'])->name('announcements.index');
    Route::post('/announcements', [AdminDashboardController::class, 'storeAnnouncement'])->name('announcements.store');
    Route::put('/announcements/{announcement}', [AdminDashboardController::class, 'updateAnnouncement'])->name('announcements.update');
    Route::delete('/announcements/{announcement}', [AdminDashboardController::class, 'destroyAnnouncement'])->name('announcements.destroy');

    Route::get('/faqs', [AdminDashboardController::class, 'faqs'])->name('faqs.index');
    Route::post('/faqs', [AdminDashboardController::class, 'storeFaq'])->name('faqs.store');
    Route::delete('/faqs/{faq}', [AdminDashboardController::class, 'destroyFaq'])->name('faqs.destroy');

    Route::get('/settings', [AdminDashboardController::class, 'settings'])->name('settings.index');
    Route::put('/settings', [AdminDashboardController::class, 'updateSettings'])->name('settings.update');
});
