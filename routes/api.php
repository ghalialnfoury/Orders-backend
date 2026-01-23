<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Restaurant\RestaurantController;
use App\Http\Controllers\Restaurant\ProductController;
use App\Http\Controllers\Order\OrderController;
use App\Http\Controllers\Restaurant\OrderController as RestaurantOrderController;
use App\Http\Controllers\Driver\OrderController as DriverOrderController;
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('auth:api')->group(function () {
        Route::get('profile', [AuthController::class, 'profile']);
        Route::post('logout', [AuthController::class, 'logout']);
    });
});
Route::middleware(['auth:api'])->group(function () {

    Route::middleware('role:restaurant')->group(function () {
        // Routes المطاعم لاحقًا
    });

    Route::middleware('role:admin')->group(function () {
        // Routes الأدمن لاحقًا
    });

    Route::middleware('role:customer')->group(function () {
        // Routes الطلبات لاحقًا
    });

});
Route::middleware(['auth:api', 'role:restaurant'])->prefix('restaurant')->group(function () {
    Route::post('/', [RestaurantController::class, 'store']);
    Route::get('/', [RestaurantController::class, 'show']);
});
use App\Http\Controllers\Restaurant\CategoryController;

Route::middleware(['auth:api', 'role:restaurant'])->prefix('restaurant')->group(function () {
    Route::get('categories', [CategoryController::class, 'index']);
    Route::post('categories', [CategoryController::class, 'store']);
});

Route::middleware(['auth:api', 'role:restaurant'])->prefix('restaurant')->group(function () {
    Route::get('products', [ProductController::class, 'index']);
    Route::post('products', [ProductController::class, 'store']);
    Route::put('products/{id}', [ProductController::class, 'update']);
    Route::delete('products/{id}', [ProductController::class, 'destroy']);
});



Route::middleware(['auth:api', 'role:customer'])->prefix('orders')->group(function () {
    Route::post('/', [OrderController::class, 'store']);
    Route::get('/my', [OrderController::class, 'myOrders']);
});


Route::middleware(['auth:api', 'role:restaurant'])
    ->prefix('restaurant')
    ->group(function () {
        Route::get('orders', [RestaurantOrderController::class, 'index']);
        Route::patch('orders/{id}/status', [RestaurantOrderController::class, 'updateStatus']);
            Route::patch(
    'orders/{id}/assign-driver',
    [RestaurantOrderController::class, 'assignDriver']
);
    });



Route::middleware(['auth:api', 'role:driver'])
    ->prefix('driver')
    ->group(function () {
        Route::get('orders', [DriverOrderController::class, 'index']);
        Route::patch('orders/{id}/status', [DriverOrderController::class, 'updateStatus']);
    });

Route::middleware('auth:api')->group(function () {
    Route::patch('orders/{id}/payment-method', [OrderController::class, 'setPaymentMethod']);
    Route::post('orders/{id}/pay', [OrderController::class, 'pay']);
});


