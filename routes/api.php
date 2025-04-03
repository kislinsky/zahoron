<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

Route::prefix('v1')->group(function () {
    // Регистрация
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/register/confirm-info', [AuthController::class, 'confirmInfo']);
    Route::post('/register/confirm-phone', [AuthController::class, 'confirmPhone']);
    
    // Авторизация
    Route::post('/auth', [AuthController::class, 'authInit']);
    Route::post('/auth/confirm', [AuthController::class, 'authConfirm']);
    
    // Защищенные маршруты
    Route::middleware('auth:api')->group(function () {
        // Здесь будут маршруты для работы с данными
    });
});
