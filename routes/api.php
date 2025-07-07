<?php

use App\Http\Controllers\Api\Account\Agency\AgencyController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;





Route::prefix('v1')->group(function () {




    // Защищенные маршруты
    Route::middleware('auth:api')->group(function () {
        // Здесь будут маршруты для работы с данными
    });


    // Регистрация
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/register/confirm-info', [AuthController::class, 'confirmInfo']);
    Route::post('/register/confirm-phone', [AuthController::class, 'confirmPhone']);
    
    // Авторизация
    Route::post('/auth', [AuthController::class, 'authInit']);
    Route::post('/auth/confirm', [AuthController::class, 'authConfirm']);
    


    Route::middleware('jwt.auth')->group(function () {
        Route::group(['prefix'=>'account'], function() {
            Route::group(['prefix'=>'agency'], function() {

                Route::group(['prefix'=>'organization'], function() {
                    Route::post('/create', [AgencyController::class, 'createOrganization']);
                    Route::post('/update', [AgencyController::class, 'updateOrganization']);
                });

                Route::group(['prefix'=>'organization-provider'], function() {
                    Route::post('/create-requests-cost', [AgencyController::class, 'addRequestsCostProductSuppliers']);
                    Route::delete('/delete-requests-cost/{request}', [AgencyController::class, 'deleteRequestCostProductProvider']);
                    Route::post('/offer/add', [AgencyController::class, 'createProviderOffer']);    
                    Route::delete('/offer/{id}/delete', [AgencyController::class, 'deleteProviderOffer']);
 

                });


                Route::post('/settings/update', [AgencyController::class, 'settingsUserUpdate']);

                Route::prefix('products')->group(function () {
                    Route::get('/', [AgencyController::class, 'products']);
                    Route::post('/', [AgencyController::class, 'addProduct']);
                    Route::post('/{product}/update', [AgencyController::class, 'updateProduct']);
                    Route::delete('/{product}', [AgencyController::class, 'deleteProduct']);
                    
                });
            });
        });
    });



});
