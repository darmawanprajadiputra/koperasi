<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PredictionController;
use App\Http\Controllers\ProductController;

Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : redirect()->route('login');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
    Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Storage & Product Management
    Route::get('/gudang', [ProductController::class, 'index'])->name('storage');
    Route::get('/product/create', [ProductController::class, 'create'])->name('product.create');
    Route::post('/product', [ProductController::class, 'store'])->name('product.store');
    Route::get('/product/{product}/edit', [ProductController::class, 'edit'])->name('product.edit');
    Route::put('/product/{product}', [ProductController::class, 'update'])->name('product.update');
    Route::delete('/product/{product}', [ProductController::class, 'destroy'])->name('product.destroy');
    Route::patch('/product/{product}/toggle', [ProductController::class, 'toggle'])->name('product.toggle');

    // Shop
    Route::get('/shop', [ShopController::class, 'index'])->name('shop');

    // Checkout
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

    // Orders — Web (menampilkan blade view)
    Route::get('/order', [OrderController::class, 'index'])->name('order');
    Route::get('/order/{order_id}', [OrderController::class, 'show'])->name('order.show'); // ← hanya 1 route, panggil show()

    // Prediction
    Route::get('/prediction', [PredictionController::class, 'index'])->name('prediction');
    Route::post('/prediction/predict', [PredictionController::class, 'predict'])->name('prediction.predict');

    Route::prefix('api')->group(function () {
        Route::get('/get_products', [ShopController::class, 'getProducts'])->name('api.products');
        Route::post('/add-to-cart', [ShopController::class, 'addToCart'])->name('api.add-to-cart');
        Route::get('/get_orders', [OrderController::class, 'getOrders'])->name('api.orders');

        Route::get('/orders/{order_id}', [OrderController::class, 'getOrderDetail'])->name('api.order.detail');
        Route::patch('/orders/{order_id}/status', [OrderController::class, 'updateStatus'])->name('api.order.update-status');

        Route::post('/predict', [PredictionController::class, 'predict'])->name('api.predict');
        Route::post('/analyze-product', [PredictionController::class, 'analyzeProduct'])->name('api.analyze-product');
        Route::post('/save-prediction', [PredictionController::class, 'savePrediction'])->name('api.save-prediction');
        Route::get('/prediction-history', [PredictionController::class, 'getHistory'])->name('api.prediction-history');
    });
});