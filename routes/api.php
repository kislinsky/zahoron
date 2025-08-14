<?php

use App\Http\Controllers\Api\Account\Agency\AgencyController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Swagger\TestController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;






Route::prefix('v1')->group(function () {


    Route::post('/delete/{user}', [AuthController::class, 'deleteAccountTest']);
    Route::post('/user/{user}/find', [AgencyController::class, 'findUser']);

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
    
    Route::post('/organizations/{city}', [AgencyController::class, 'organizationsCity']);
    Route::post('/cities/search', [AgencyController::class, 'citySearch']);



    Route::middleware('jwt.auth')->group(function () {
        Route::group(['prefix'=>'account'], function() {

            Route::group(['prefix'=>'agency'], function() {

                Route::get('/wallets', [AgencyController::class, 'userWallets']);
                Route::group(['prefix'=>'wallet'], function() {
                    Route::delete('/{wallet}/delete', [AgencyController::class, 'deleteWallet']);
                    Route::delete('/balance/update', [AgencyController::class, 'walletUpdateBalance']);
                });

                Route::group(['prefix'=>'aplications'], function() {
                    Route::get('/buy', [AgencyController::class, 'getApplicationsForBuy']);
                    Route::post('/purchase', [AgencyController::class, 'payApplication']);
                });

                Route::group(['prefix'=>'priority'], function() {
                    Route::post('/buy', [AgencyController::class, 'buyPriority']);
                });
                


                Route::post('/send-code', [AgencyController::class, 'sendCode']);
                Route::post('/accept-code', [AgencyController::class, 'acceptCode']);

                

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


                Route::prefix('product-comments')->group(function() {
                    Route::get('/organization/{id}', [AgencyController::class, 'getProductComments']);
                    Route::delete('/{id}', [AgencyController::class, 'deleteProductComment']);
                    Route::patch('/{id}/approve', [AgencyController::class, 'approveProductComment']);
                    Route::put('/{id}/content', [AgencyController::class, 'updateProductCommentContent']);
                    Route::post('/{id}/response', [AgencyController::class, 'addProductCommentResponse']);
                    });
                });


                Route::prefix('reviews')->group(function() {
                    Route::get('/organization/{id}', [AgencyController::class, 'getOrganizationReviews']);
                    Route::delete('/{id}', [AgencyController::class, 'deleteReview']);
                    Route::patch('/{id}/approve', [AgencyController::class, 'approveReview']);
                    Route::put('/{id}/content', [AgencyController::class, 'updateReviewContent']);
                    Route::post('/{id}/response', [AgencyController::class, 'addOrganizationReviewResponse']);
                });


                Route::post('/settings/update', [AgencyController::class, 'settingsUserUpdate']);
                Route::post('/delete/{user}', [AuthController::class, 'deleteAccountTest']);
                Route::post('/user/{user}/find', [AgencyController::class, 'findUser']);


                Route::prefix('products')->group(function () {
                    Route::get('/', [AgencyController::class, 'products']);
                    Route::post('/', [AgencyController::class, 'addProduct']);
                    Route::post('/{product}/update', [AgencyController::class, 'updateProduct']);
                    Route::delete('/{product}', [AgencyController::class, 'deleteProduct']);
                    
                });
            });

        });
    });

