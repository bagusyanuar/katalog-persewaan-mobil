<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return response()->json([
        'app_name' => 'katalog_persewaan_mobil',
        'app_version' => 'v1.0'
    ], 200);
});

Route::group(['prefix' => 'merchant'], function () {
    Route::post('/register', [\App\Http\Controllers\Merchant\AuthController::class, 'register']);
    Route::post('/login', [\App\Http\Controllers\Merchant\AuthController::class, 'login']);

    Route::group(['middleware' => ['jwt.verify']], function () {
        Route::group(['prefix' => 'product'], function () {
            Route::match(['post', 'get'], '/', [\App\Http\Controllers\Merchant\ProductController::class, 'index']);
            Route::match(['post', 'get'], '/{id}', [\App\Http\Controllers\Merchant\ProductController::class, 'findByID']);
            Route::delete('/{id}/delete', [\App\Http\Controllers\Merchant\ProductController::class, 'destroy']);
        });

        Route::group(['prefix' => 'driver'], function () {
            Route::match(['post', 'get'], '/', [\App\Http\Controllers\Merchant\DriverController::class, 'index']);
            Route::match(['post', 'get'], '/{id}', [\App\Http\Controllers\Merchant\DriverController::class, 'findByID']);
            Route::delete('/{id}/delete', [\App\Http\Controllers\Merchant\DriverController::class, 'destroy']);
        });

        Route::group(['prefix' => 'order'], function () {
            Route::match(['post', 'get'], '/', [\App\Http\Controllers\Merchant\OrderController::class, 'index']);
            Route::match(['post', 'get'], '/{id}', [\App\Http\Controllers\Merchant\OrderController::class, 'findByID']);
        });

        Route::group(['prefix' => 'profile'], function () {
            Route::match(['post', 'get'], '/', [\App\Http\Controllers\Merchant\ProfileController::class, 'index']);
        });
    });
});

Route::group(['prefix' => 'customer'], function () {
    Route::post('/register', [\App\Http\Controllers\Customer\AuthController::class, 'register']);
    Route::post('/login', [\App\Http\Controllers\Customer\AuthController::class, 'login']);
    Route::group(['prefix' => 'merchant'], function () {
        Route::get('/', [\App\Http\Controllers\Customer\MerchantController::class, 'index']);
        Route::get('/{id}', [\App\Http\Controllers\Customer\MerchantController::class, 'findByID']);
        Route::get('/{id}/product', [\App\Http\Controllers\Customer\MerchantController::class, 'productByMerchant']);
        Route::get('/{id}/driver', [\App\Http\Controllers\Customer\MerchantController::class, 'driverByMerchant']);
    });

    Route::group(['prefix' => 'product'], function () {
        Route::get('/{id}', [\App\Http\Controllers\Customer\ProductController::class, 'findByID']);
    });
    Route::group(['middleware' => ['jwt.verify']], function () {
        Route::group(['prefix' => 'cart'], function () {
            Route::match(['post', 'get'], '/', [\App\Http\Controllers\Customer\CartController::class, 'index']);
            Route::post( '/checkout', [\App\Http\Controllers\Customer\CartController::class, 'checkout']);
        });

        Route::group(['prefix' => 'rent'], function () {
            Route::get( '/', [\App\Http\Controllers\Customer\RentController::class, 'index']);
            Route::match(['post', 'get'], '/{id}', [\App\Http\Controllers\Customer\RentController::class, 'getDataByID']);
        });

        Route::group(['prefix' => 'profile'], function () {
            Route::match(['post', 'get'], '/', [\App\Http\Controllers\Customer\ProfileController::class, 'index']);
        });
    });
});


