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

Route::group(['prefix' => 'merchant'], function (){
    Route::post('/register', [\App\Http\Controllers\Merchant\AuthController::class, 'register']);
    Route::post('/login', [\App\Http\Controllers\Merchant\AuthController::class, 'login']);

    Route::group(['middleware' => ['jwt.verify']], function () {

        Route::group(['prefix' => 'product'], function () {
            Route::match(['post', 'get'],'/', [\App\Http\Controllers\Merchant\ProductController::class, 'index']);
            Route::match(['post', 'get'],'/{id}', [\App\Http\Controllers\Merchant\ProductController::class, 'findByID']);
        });

        Route::group(['prefix' => 'driver'], function () {
            Route::match(['post', 'get'],'/', [\App\Http\Controllers\Merchant\DriverController::class, 'index']);
            Route::match(['post', 'get'],'/{id}', [\App\Http\Controllers\Merchant\DriverController::class, 'findByID']);
        });
    });
});
