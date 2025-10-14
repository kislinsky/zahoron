<?php

use App\Http\Controllers\Api\Account\Agency\AgencyController;
use App\Http\Controllers\Api\Account\Agency\AuthAgencyController;
use App\Http\Controllers\Api\Account\Cashier\AuthCashierController;
use App\Http\Controllers\Api\Account\Cashier\CashierController;
use App\Http\Controllers\Swagger\TestController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::prefix('app')->group(function () {



    Route::prefix('organization')->group(function () {


        Route::post('/send-code', [AgencyController::class, 'sendCode']);
        Route::post('/accept-code', [AgencyController::class, 'acceptCode']);
        
        // Незащищенные маршруты (публичные)
        Route::post('/delete/{user}', [AuthAgencyController::class, 'deleteAccountTest']);
        Route::post('/user/{user}/find', [AgencyController::class, 'findUser']);
        
        // Регистрация и авторизация
        Route::post('/register', [AuthAgencyController::class, 'register']);
        Route::post('/register/confirm-info', [AuthAgencyController::class, 'confirmInfo']);
        Route::post('/register/confirm-phone', [AuthAgencyController::class, 'confirmPhone']);
        
        // Авторизация
        Route::post('/auth', [AuthAgencyController::class, 'authInit']);
        Route::post('/auth/confirm', [AuthAgencyController::class, 'authConfirm']);
        
        // Публичные API
        Route::post('/organizations/{city}', [AgencyController::class, 'organizationsCity']);
        Route::post('/cities/search', [AgencyController::class, 'citySearch']);
        Route::post('/edges/search', [AgencyController::class, 'edgeSearch']);
        
        Route::get('/cemeteries', [AgencyController::class, 'getCemeteries']);
        Route::get('/cemeteries/{id}', [AgencyController::class, 'getCemetery']);

        Route::get('/categories/main', [AgencyController::class, 'getMainCategories']);
        Route::get('/categories/{categoryId}/subcategories', [AgencyController::class, 'getSubcategories']);

        // ЗАЩИЩЕННЫЕ маршруты (должны быть ВЫШЕ конкретных маршрутов)
        Route::middleware('jwt.role.organization')->group(function () {
            Route::group(['prefix' => 'account'], function() {
                Route::group(['prefix' => 'agency'], function() {


                     Route::group(['prefix'=>'users'], function() {
                        Route::get('/', [AgencyController::class, 'users'])->name('account.agency.users');
                        Route::post('/store', [AgencyController::class, 'storeUser'])->name('account.agency.users.store');
                        Route::get('/{user}', [AgencyController::class, 'editUser'])->name('account.agency.users.edit');
                        Route::put('/{user}', [AgencyController::class, 'updateUser'])->name('account.agency.users.update');
                        Route::delete('/{user}', [AgencyController::class, 'destroyUser'])->name('account.agency.users.destroy');
                    });

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
        Route::post('/delete/{user}', [AuthAgencyController::class, 'deleteAccountTest']);
        Route::post('/user/{user}/find', [AgencyController::class, 'findUser']);
    });


    Route::prefix('cashier')->group(function () {


        Route::post('/validate-token', [AuthCashierController::class, 'checkJwtToken']);
        // Проверка JWT токена из заголовка
        Route::get('/validate-token-header', [AuthCashierController::class, 'checkJwtTokenFromHeader']);
        
        // Авторизация
        Route::post('/auth/init', [AuthCashierController::class, 'authInit']);
        Route::post('/auth/confirm', [AuthCashierController::class, 'authConfirm']);
        Route::post('/auth/confirm-call', [AuthCashierController::class, 'authConfirmCall']);

        Route::get('/cemeteries/{id}', [CashierController::class, 'getCemetery']);
        Route::get('/mortuaries/{id}', [CashierController::class, 'getMortuary']);

        Route::middleware('jwt.role.cashier')->group(function () {
            Route::group(['prefix' => 'account'], function() {
                Route::group(['prefix' => 'cashier'], function() {

                    Route::get('/cemeteries', [CashierController::class, 'getCemeteries']);
                    Route::get('/morgues', [CashierController::class, 'getMorgues']);
                    Route::get('/call-stats', [CashierController::class, 'getCallStats']);
                    Route::get('/orders', [CashierController::class, 'orderProducts']);
                    
                });
            });
        });
      
        
    });
});