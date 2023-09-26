<?php

use App\Http\Controllers\AdministrationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ManagementController;
use App\Http\Controllers\OpdbController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

Auth::routes(['verify' => true]);
// Rutas públicas accesibles para todos los usuarios (sin autenticación)
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

// Rutas protegidas que requieren autenticación
Route::middleware('auth:sanctum')->group(function () {
    // Rutas accesibles para todos los roles
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('user', [AuthController::class, 'user']);


    // Rutas accesibles solo para el rol "user"
    Route::middleware('role:user')->group(function () {
        //Ruta para configurar la foto de perfil
        Route::put('settingsProfilePhoto', [AuthController::class, 'settingsProfilePhoto']);
        //Ruta para configurar la informacion del usuario
        Route::put('settingsPersonalDetails', [AuthController::class, 'settingsPersonalDetails']);
        //Ruta para configurar la contraseña
        Route::put('settingsPasswordUpdate', [AuthController::class, 'settingsPasswordUpdate']);
        //Ruta para generar el codigo de seis digitos para la autenticacion en dos pasos
        Route::post('generateTwoFactorCode', [AuthController::class, 'generateTwoFactorCode']);
        //Ruta para ingresar el codigo y completar la autenticacion en dos pasos
        Route::post('enableTwoFactorAuthentication/', [AuthController::class, 'enableTwoFactorAuthentication']);
        //Ruta para la verificacion del email
        Route::prefix('web')->group(function () {
            Route::post('verifyEmail', [AuthController::class, 'verifyEmail'])->name('verification.verify');
        });
        //Base de datos para uso del rol asistente
        Route::prefix('opdbs')->group(function () {
            //Ruta para mostrar los documentos para el rol asistente
            Route::get('/', [OpdbController::class, 'upload']);
            //Ruta para generar los documentos para el rol asistente
            Route::post('/download/{id}', [OpdbController::class, 'download']);
        });
        //Metodos de pago
        Route::prefix('payment-methods')->group(function () {
            // Ruta para mostrar el metodo de pago actual del usuario
            Route::get('/', [PaymentMethodController::class, 'index']);
            // Ruta para añadir un metodo de pago
            Route::post('/', [PaymentMethodController::class, 'create']);
        });
        //Suscripciones
        Route::prefix('subscription')->group(function () {
            // Ruta para mostrar el plan actual del usuario
            Route::get('/', [SubscriptionController::class, 'show']);
            // Ruta para suscribirse a un nuevo plan
            Route::post('/subscribe/{plan}', [SubscriptionController::class, 'subscribe']);
            // Ruta para cambiar de plan
            Route::put('/change-plan/{plan}', [SubscriptionController::class, 'changePlan']);
            // Ruta para cancelar la suscripción
            Route::delete('/unsubscribe', [SubscriptionController::class, 'unsubscribe']);
        });
    });
    // Rutas accesibles solo para el rol "manager"
    Route::middleware('role:manager')->group(function () {
        Route::get('opsInfo', [ManagementController::class, 'opsInfo']);
    });
    // Rutas accesibles solo para el rol "admin"
    Route::middleware('role:admin')->group(function () {
        Route::get('agentInfo', [AdministrationController::class, 'agentInfo']);
        Route::post('createCampaign', [AdministrationController::class, 'createCampaign']);
        Route::put('updateCampaign', [AdministrationController::class, 'updateCampaign']);
        Route::delete('deleteCampaign', [AdministrationController::class, 'deleteCampaign']);
    });
});
