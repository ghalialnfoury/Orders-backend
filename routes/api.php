<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Auth\AuthController;

Route::prefix('auth')->group(function () {

    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('auth:api')->group(function () {

        Route::get('profile', [AuthController::class, 'profile']);
        Route::post('logout', [AuthController::class, 'logout']);
    });
});

/*
|--------------------------------------------------------------------------
| PUBLIC
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Customer\RestaurantController as PublicRestaurantController;
use App\Http\Controllers\Customer\ProductController as PublicProductController;

Route::get('restaurants', [PublicRestaurantController::class, 'index']);
Route::get('restaurants/{id}/products', [PublicProductController::class, 'index']);


/*
|--------------------------------------------------------------------------
| CUSTOMER
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Order\OrderController as CustomerOrderController;
use App\Http\Controllers\RatingController;

Route::middleware(['auth:api', 'role:customer'])
    ->prefix('customer')
    ->group(function () {

        Route::post('orders', [CustomerOrderController::class, 'store']);
        Route::get('orders', [CustomerOrderController::class, 'myOrders']);
        Route::get('orders/{id}', [CustomerOrderController::class, 'show']);

        Route::post('orders/{id}/rate', [RatingController::class, 'store']);
        Route::post(
            'orders/{id}/payment-method',
            [CustomerOrderController::class, 'setPaymentMethod']
        );

        Route::post(
            'orders/{id}/pay',
            [CustomerOrderController::class, 'pay']
        );
    });


/*
|--------------------------------------------------------------------------
| RESTAURANT
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Restaurant\DashboardController as RestaurantDashboardController;
use App\Http\Controllers\Restaurant\CategoryController;
use App\Http\Controllers\Restaurant\ProductController as RestaurantProductController;
use App\Http\Controllers\Restaurant\OrderController as RestaurantOrderController;
use App\Http\Controllers\Restaurant\DriverController as RestaurantDriverController;

Route::middleware(['auth:api', 'role:restaurant'])
    ->prefix('restaurant')
    ->group(function () {

        Route::get('dashboard', [RestaurantDashboardController::class, 'index']);




        Route::apiResource('products', RestaurantProductController::class);

        Route::get('orders', [RestaurantOrderController::class, 'index']);

        Route::patch(
            'orders/{id}/status',
            [RestaurantOrderController::class, 'updateStatus']
        );

        Route::patch(
            'orders/{id}/assign-driver',
            [RestaurantOrderController::class, 'assignDriver']
        );

        Route::get('drivers', [RestaurantDriverController::class, 'index']);

        Route::get('categories', [CategoryController::class, 'index']);
    });


/*
|--------------------------------------------------------------------------
| DRIVER
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Driver\OrderController as DriverOrderController;
use App\Http\Controllers\Driver\DashboardController as DriverDashboardController;
use App\Http\Controllers\Restaurant\DriverController;

Route::middleware('auth:api')
    ->prefix('driver')
    ->group(function () {

        Route::post('apply', [DriverController::class, 'apply']);

        Route::middleware('role:driver')->group(function () {

            Route::get('dashboard', [DriverDashboardController::class, 'index']);

            Route::get('orders', [DriverOrderController::class, 'index']);

            Route::patch(
                'orders/{id}/status',
                [DriverOrderController::class, 'updateStatus']
            );

            Route::post(
                'orders/{id}/deliver',
                [DriverOrderController::class, 'deliver']
            );
        });
    });


/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminDriverController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminRestaurantController;

Route::prefix('admin')
    ->middleware(['auth:api', 'role:admin'])
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        */

        Route::get('dashboard', [AdminDashboardController::class, 'index']);


        /*
        |--------------------------------------------------------------------------
        | Users
        |--------------------------------------------------------------------------
        */

        Route::apiResource('users', AdminUserController::class)
            ->except(['show']);

        Route::patch(
            'users/{id}/status',
            [AdminUserController::class, 'updateStatus']
        );

        Route::patch(
            'users/{id}/approve-driver',
            [AdminUserController::class, 'approveDriver']
        );


        /*
        |--------------------------------------------------------------------------
        | Driver Requests
        |--------------------------------------------------------------------------
        */

        Route::prefix('driver-requests')->group(function () {

            Route::get('/', [AdminDriverController::class, 'index']);

            Route::patch(
                '{id}/approve',
                [AdminDriverController::class, 'approve']
            );
        });


        /*
        |--------------------------------------------------------------------------
        | Restaurants
        |--------------------------------------------------------------------------
        */

        Route::prefix('restaurants')->group(function () {

            Route::get('/', [AdminRestaurantController::class, 'index']);

            Route::patch(
                '{id}/approve',
                [AdminRestaurantController::class, 'approve']
            );
        });


        /*
        |--------------------------------------------------------------------------
        | Orders
        |--------------------------------------------------------------------------
        */

        Route::prefix('orders')->group(function () {

            Route::get('/', [AdminOrderController::class, 'index']);
            Route::get('{id}', [AdminOrderController::class, 'show']);

            Route::patch(
                '{id}/status',
                [AdminOrderController::class, 'changeStatus']
            );

            Route::patch(
                '{id}/cancel',
                [AdminOrderController::class, 'cancel']
            );

            Route::patch(
                '{id}/assign-driver',
                [AdminOrderController::class, 'assignDriver']
            );
        });
    });
