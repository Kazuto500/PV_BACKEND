<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\StripePaymentController;
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
        //Ruta para configurar la foto de perfil
        Route::put('settingsProfilePhotoUrl/{id}', [AuthController::class, 'settingsProfilePhotoUrl']);
        //Ruta para configurar la informacion del usuario
        Route::put('settingsPersonalDetails/{id}', [AuthController::class, 'settingsPersonalDetails']);
        //Ruta para configurar la contraseña
        Route::put('settingsPasswordUpdate/{id}', [AuthController::class, 'settingsPasswordUpdate']);
        Route::prefix('payment-methods')->group(function () {
            // Ruta para mostrar el metodo de pago actual del usuario
            Route::get('/', [PaymentMethodController::class, 'index']);
            // Ruta para añadir un metodo de pago
            Route::post('/', [PaymentMethodController::class, 'create']);
        });
        Route::prefix('subscription')->group(function () {
            // Ruta para mostrar el plan actual del usuario
            Route::get('/', [SubscriptionController::class, 'show'])->name('subscription.show');
            // Ruta para suscribirse a un nuevo plan
            Route::post('/subscribe/{plan}', [SubscriptionController::class, 'subscribe'])->name('subscription.subscribe');
            // Ruta para cambiar de plan
            Route::put('/change-plan/{plan}', [SubscriptionController::class, 'changePlan'])->name('subscription.change-plan');
            // Ruta para cancelar la suscripción
            Route::delete('/unsubscribe', [SubscriptionController::class, 'unsubscribe'])->name('subscription.unsubscribe');
        });
    });
});
