<?php

use App\Http\Controllers\Api\Account\Agency\AgencyController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Swagger\TestController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('app')->group(function () {
    Route::prefix('organization')->group(function () {
        // Незащищенные маршруты (публичные)
        Route::post('/delete/{user}', [AuthController::class, 'deleteAccountTest']);
        Route::post('/user/{user}/find', [AgencyController::class, 'findUser']);
        
        // Регистрация и авторизация
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/register/confirm-info', [AuthController::class, 'confirmInfo']);
        Route::post('/register/confirm-phone', [AuthController::class, 'confirmPhone']);
        
        // Авторизация
        Route::post('/auth', [AuthController::class, 'authInit']);
        Route::post('/auth/confirm', [AuthController::class, 'authConfirm']);
        
        // Публичные API
        Route::post('/organizations/{city}', [AgencyController::class, 'organizationsCity']);
        Route::post('/cities/search', [AgencyController::class, 'citySearch']);
        Route::post('/edges/search', [AgencyController::class, 'edgeSearch']);
        
        Route::get('/cemeteries', [AgencyController::class, 'getCemeteries']);
        Route::get('/cemeteries/{id}', [AgencyController::class, 'getCemetery']);

        Route::get('/categories/main', [AgencyController::class, 'getMainCategories']);
        Route::get('/categories/{categoryId}/subcategories', [AgencyController::class, 'getSubcategories']);

        // ЗАЩИЩЕННЫЕ маршруты (должны быть ВЫШЕ конкретных маршрутов)
        Route::middleware('jwt.auth')->group(function () {
            Route::group(['prefix' => 'account'], function() {
                Route::group(['prefix' => 'agency'], function() {
                    Route::post('/change-organization', [AgencyController::class, 'changeOrganization']);
                    Route::get('/organizations', [AgencyController::class, 'getUserOrganizations']);
                    Route::get('/current-organization', [AgencyController::class, 'getCurrentOrganization']);

                    Route::get('/wallets', [AgencyController::class, 'userWallets']);
                    
                    Route::group(['prefix' => 'wallet'], function() {
                        Route::delete('/{wallet}/delete', [AgencyController::class, 'deleteWallet']);
                        Route::post('/balance/update', [AgencyController::class, 'walletUpdateBalance']);
                    });

                    Route::group(['prefix' => 'aplications'], function() {
                        Route::get('/buy', [AgencyController::class, 'getApplicationsForBuy']);
                        Route::post('/purchase', [AgencyController::class, 'payApplication']);
                    });

                    Route::group(['prefix' => 'priority'], function() {
                        Route::post('/buy', [AgencyController::class, 'buyPriority']);
                    });

                    Route::post('/send-code', [AgencyController::class, 'sendCode']);
                    Route::post('/accept-code', [AgencyController::class, 'acceptCode']);

                    Route::group(['prefix' => 'organization'], function() {
                        Route::post('/create', [AgencyController::class, 'createOrganization']);
                        Route::post('/update', [AgencyController::class, 'updateOrganization']);
                    });

                    Route::group(['prefix' => 'organization-provider'], function() {
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

                    Route::prefix('reviews')->group(function() {
                        Route::get('/organization/{id}', [AgencyController::class, 'getOrganizationReviews']);
                        Route::delete('/{id}', [AgencyController::class, 'deleteReview']);
                        Route::patch('/{id}/approve', [AgencyController::class, 'approveReview']);
                        Route::put('/{id}/content', [AgencyController::class, 'updateReviewContent']);
                        Route::post('/{id}/response', [AgencyController::class, 'addOrganizationReviewResponse']);
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

        // Эти маршруты ДОЛЖНЫ БЫТЬ ВНЕ middleware, иначе будут конфликты
        Route::post('/delete/{user}', [AuthController::class, 'deleteAccountTest']);
        Route::post('/user/{user}/find', [AgencyController::class, 'findUser']);
    });
});