<?php

use App\Http\Controllers\AuthController;
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

// Rutas públicas accesibles para todos los usuarios (sin autenticación)
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

// Rutas protegidas que requieren autenticación
Route::middleware('auth:sanctum')->group(function () {
    // Rutas accesibles para todos los roles
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('users', [AuthController::class, 'index']);
    Route::post('logout', [AuthController::class, 'logout']);

    // Rutas accesibles solo para el rol "admin"
    Route::middleware('role:admin')->group(function () {
        // ... otras rutas y funciones específicas del admin
    });

    // Rutas accesibles solo para el rol "assistant"
    Route::middleware('role:assistant')->group(function () {
        // ... otras rutas y funciones específicas del assistant
    });

    // Rutas accesibles solo para el rol "user"
    Route::middleware('role:user')->group(function () {
        Route::put('settingsProfilePhotoUrl/{id}', [AuthController::class, 'settingsProfilePhotoUrl']);
        Route::put('settingsPersonalDetails/{id}', [AuthController::class, 'settingsPersonalDetails']);
        Route::put('settingsPasswordUpdate/{id}', [AuthController::class, 'settingsPasswordUpdate']);

        Route::prefix('payment-methods')->group(function () {
            Route::get('/', [PaymentMethodController::class, 'index']);
            Route::post('/', [PaymentMethodController::class, 'store']);
        });
        Route::prefix('subscribe')->group(function () {
            Route::get('/', [SubscriptionController::class, 'index']);
            Route::post('/', [SubscriptionController::class, 'store']);
            Route::get('/cancel', [SubscriptionController::class, 'cancel']);
        });
    });
});
