<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CityController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\NewsController;


use App\Http\Controllers\Account\AgentController;
use App\Http\Controllers\Account\Agency\AgencyController;
use App\Http\Controllers\Account\DecoderController;
use App\Http\Controllers\Account\AccountController;
use App\Http\Controllers\Account\Admin\AdminRitualObjectsController;
use App\Http\Controllers\Account\AdminController;
use App\Http\Controllers\Account\Agency\AgencyOrganizationController;
use App\Http\Controllers\Account\HomeController;



use App\Http\Controllers\BurialController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\CemeteriesController;
use App\Http\Controllers\BasketBurialContoller;
use App\Http\Controllers\OrderBurialController;
use App\Http\Controllers\WordsMemoryController;
use App\Http\Controllers\BasketServiceContoller;
use App\Http\Controllers\OrderProductController;
use App\Http\Controllers\OrderServiceController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\BasketProductController;
use App\Http\Controllers\BeautificationController;
use App\Http\Controllers\CategoryProductController;
use App\Http\Controllers\ColumbariumController;
use App\Http\Controllers\CrematoriumController;
use App\Http\Controllers\DeadController;
use App\Http\Controllers\DistrictController;
use App\Http\Controllers\FuneralController;
use App\Http\Controllers\InfoEditBurialController;
use App\Http\Controllers\LifeStoryBurialController;
use App\Http\Controllers\MemorialController;
use App\Http\Controllers\MortuaryController;
use App\Http\Controllers\ProductPriceListController;
use App\Models\CategoryProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


#убирать die когда нужно использовать artisan 
$city = @explode("/", explode("//", request()->fullUrl())[1])[1];

if (!request()->is('storage/*') && !request()->is('css/*') && !request()->is('js/*')) {
    
if(city_by_slug($city) == null){
    setcookie('city', '', -1, '/');
    setcookie("city", first_city_id(), time()+20*24*60*60,'/');
    header("location: /".first_city_slug());
    die;
} else{
    $c_b_s__ = city_by_slug($city);
    if(!isset($_COOKIE['city'])){
        setcookie("city", first_city_id(), time()+20*24*60*60,'/');
        header("Refresh:0");
        die;
    }
    if($_COOKIE['city'] != $c_b_s__->id){
        setcookie('city', '', -1, '/');
        setcookie("city", $c_b_s__->id, time()+20*24*60*60,'/');
        header("Refresh:0");
        die;

    }
}
}



Route::prefix($city)->group(function () {


    Auth::routes();


    Route::get('/', [MainController::class, 'index'])->name('index');
    Route::get('/speczialist', [MainController::class, 'speczialist'])->name('speczialist');




    Route::get('/', [MainController::class, 'index'])->name('index');
    Route::get('/speczialist', [MainController::class, 'speczialist'])->name('speczialist');
    Route::get('/speczialist', [MainController::class, 'speczialist'])->name('speczialist');

    Route::get('/city/{id}', [CityController::class, 'selectCity'])->name('city.select');


    Route::get('/organization/{slug}', [OrganizationController::class, 'single'])->name('organization.single');

    Route::get('/organizations', [OrganizationController::class, 'catalogOrganization'])->name('organizations');




    Route::prefix('ajax')->group(function () {

        Route::get('category/children/ul', [CategoryProductController::class, 'ajaxCategoryChildrenUl'])->name('category.product.children.ul');
                
        Route::post('/add-cemetery-setings', [AgentController::class, 'addCemetery'])->name('add.cemetery.settings');

        Route::get('/organizations/filters', [OrganizationController::class, 'ajaxFilterCatalog'])->name('organizations.ajax.filters');
        Route::get('/organizations/category-prices', [OrganizationController::class, 'ajaxCategoryPrices'])->name('organizations.ajax.category-prices');
        Route::get('/organizations/title', [OrganizationController::class, 'ajaxTitlePage'])->name('organizations.ajax.title');
        Route::get('/organizations/map', [OrganizationController::class, 'ajaxMapOrganizations'])->name('organizations.ajax.map');

        

        Route::get('/organizations-provider/filters', [OrganizationController::class, 'ajaxFilterCatalogProvider'])->name('organizations.provider.ajax.filters');
        Route::get('/organizations-provider/category-prices', [OrganizationController::class, 'ajaxCategoryPricesProvider'])->name('organizations.provider.ajax.category-prices');
        Route::get('/organizations-provider/title', [OrganizationController::class, 'ajaxTitlePageProvider'])->name('organizations.provider.ajax.title');
        Route::get('/organizations-provider/search', [OrganizationController::class, 'ajaxSearchCatalogProvider'])->name('organizations.provider.ajax.search');
        Route::get('/organizations-provider/rating', [OrganizationController::class, 'ajaxRatingOrganizationProvider'])->name('organizations.provider.ajax.rating');

        
        Route::get('/filters', [ProductController::class, 'filterShow'])->name('marketplace.ajax.filters');
        Route::get('/cat', [ProductController::class, 'ajaxProductCat'])->name('marketplace.ajax.cat');
        Route::get('/cemetery', [ProductController::class, 'ajaxCemeteryCat'])->name('marketplace.ajax.cemetery');
        Route::get('/cat/content', [ProductController::class, 'ajaxCatContent'])->name('marketplace.ajax.cat.content');
        Route::get('/cat/manual', [ProductController::class, 'ajaxCatManual'])->name('marketplace.ajax.cat.manual');
        Route::get('/cat/reviews', [ProductController::class, 'ajaxCatReviews'])->name('marketplace.ajax.cat.reviews');
        Route::get('/marketplace/title', [ProductController::class, 'ajaxTitle'])->name('marketplace.ajax.title');

        


        Route::get('/beautification/cemetery', [CemeteriesController::class, 'ajaxCemetery'])->name('beautification.ajax.cemetery');

        Route::get('/beautification/products', [ProductPriceListController::class, 'ajaxProducts'])->name('beautification.ajax.products');

        Route::get('/memorial/district', [DistrictController::class, 'ajaxDistrict'])->name('memorial.ajax.district');

        Route::get('/dead/mortuary', [MortuaryController::class, 'ajaxMortuary'])->name('dead.ajax.mortuary');

        Route::get('/funeral-service/mortuary', [FuneralController::class, 'ajaxMortuary'])->name('funeral-service.ajax.mortuary');
        Route::get('/funeral-service/cemetery', [FuneralController::class, 'ajaxCemetery'])->name('funeral-service.ajax.cemetery');

        Route::get('/city', [CityController::class, 'ajaxCity'])->name('city.ajax');


        Route::get('/category/children', [OrganizationController::class, 'ajaxProductsChildrenCat'])->name('organization.category.ajax.children');
        Route::get('/category/main', [OrganizationController::class, 'ajaxProductsMainCat'])->name('organization.category.ajax.main');
        Route::get('/categories/children', [OrganizationController::class, 'ajaxProductsMainCatUlChildren'])->name('organization.categories.ajax.children');

    });

    Route::group(['prefix'=>'marketplace'], function() {
        Route::get('/', [ProductController::class, 'redirectToCategoryMarketplace'])->name('marketplace');
        Route::get('/{slug}', [ProductController::class, 'marketPlace'])->name('marketplace.category');
    });



    Route::group(['prefix'=>'product'], function() {
        Route::get('/add/cart', [BasketProductController::class, 'addToCart'])->name('product.add.cart');
        Route::get('/add/cart/details', [BasketProductController::class, 'addToCartDetails'])->name('product.add.cart.details');
        Route::get('/{id}/cart/delete', [BasketProductController::class, 'deleteFromCart'])->name('product.delete');
        Route::get('/{slug}', [ProductController::class, 'singleProduct'])->name('product.single');
        Route::get('/add/review', [ProductController::class, 'addReview'])->name('product.add.review');

        Route::get('/cart/change/count', [BasketProductController::class, 'changeCountCart'])->name('product.cart.count.change');
    });

    Route::get('/mortuaries', [MortuaryController::class, 'index'])->name('mortuaries');


    Route::group(['prefix'=>'mortuary'], function() {
        Route::get('/{id}', [MortuaryController::class, 'single'])->name('mortuary.single');
        Route::get('/review/add', [MortuaryController::class, 'addReview'])->name('mortuary.review.add');

    });

    Route::get('/crematoriums', [CrematoriumController::class, 'index'])->name('crematoriums');

    Route::group(['prefix'=>'crematorium'], function() {
        Route::get('/{id}', [CrematoriumController::class, 'single'])->name('crematorium.single');
        Route::get('/review/add', [CrematoriumController::class, 'addReview'])->name('crematorium.review.add');
    });

    Route::get('/columbariums', [ColumbariumController::class, 'index'])->name('columbariums');

    Route::group(['prefix'=>'columbarium'], function() {
        Route::get('/{id}', [ColumbariumController::class, 'single'])->name('columbarium.single');
        Route::get('/review/add', [ColumbariumController::class, 'addReview'])->name('columbarium.review.add');
    });


    Route::group(['prefix'=>'burial'], function() {
        Route::get('/{slug}', [BurialController::class, 'singleProduct'])->name('burial.single');
        Route::get('/{id}/cart/add', [BasketBurialContoller::class, 'addToCart'])->name('burial.add');
        Route::get('/{id}/cart/delete', [BasketBurialContoller::class, 'deleteFromCart'])->name('burial.delete');
    });



    Route::get('/search', [BurialController::class, 'searchProduct'])->name('search.burial');




    Route::group(['prefix'=>'service'], function() {
        Route::get('/{id}/cart/add', [BasketServiceContoller::class, 'addToCart'])->name('burial.service.add');
        Route::get('/cart/delete', [BasketServiceContoller::class, 'deletefromCart'])->name('burial.service.delete');
        Route::get('/{id}', [ServiceController::class, 'single'])->name('service.single');
    });



    Route::group(['prefix'=>'price-list'], function() {
        Route::get('/', [ProductPriceListController::class, 'priceList'])->name('pricelist');
        Route::get('/{slug}', [ProductPriceListController::class, 'serviceCategory'])->name('service.category');
        Route::get('/product/{slug}', [ProductPriceListController::class, 'singleProduct'])->name('pricelist.single');
    });



    Route::group(['prefix'=>'news'], function() {
        Route::get('/', [NewsController::class, 'index'])->name('news');
        Route::get('/category/{id}', [NewsController::class, 'newsCat'])->name('news.category');
        Route::get('/{slug}', [NewsController::class, 'singleNews'])->name('news.single');
    });




    Route::post('/words-memory/add', [WordsMemoryController::class, 'addWordsMemory'])->name('words-memory.add');



    Route::get('/contacts', [MainController::class, 'contacts'])->name('contacts');

    Route::get('/search-filter', [MainController::class, 'searchProductFilter'])->name('page.search.burial.filter');
    Route::get('/search-filter-who', [BurialController::class, 'searchProductFilter'])->name('search.burial.filter');
    Route::get('/search-request', [BurialController::class, 'searchProductRequest'])->name('page.search.burial.request');
    Route::get('/search-request/add', [BurialController::class, 'searchProductRequestAdd'])->name('search.burial.request');


    Route::get('/our-works', [MainController::class, 'ourWorks'])->name('our.products');


    Route::get('/cemeteries', [CemeteriesController::class, 'index'])->name('cemeteries');





    Route::group(['prefix'=>'cemetery'], function() {
        Route::get('/{id}', [CemeteriesController::class, 'singleCemetery'])->name('cemeteries.single');
        Route::get('/review/add', [CemeteriesController::class, 'addReview'])->name('cemetery.review.add');

    });



    Route::get('/home', [HomeController::class, 'index'])->name('home');


    Route::get('/beautification/send', [BeautificationController::class, 'sendBeautification'])->name('beautification.send');
    Route::get('/memorial/send', [MemorialController::class, 'memorialAdd'])->name('memorial.send');
    Route::get('/dead/send', [DeadController::class, 'deadAdd'])->name('dead.send');
    Route::get('/funeral-service/send', [FuneralController::class, 'funeralServiceAdd'])->name('funeral-service.send');

    Route::get('/review-organization/add', [OrganizationController::class, 'addReview'])->name('review-organization.add');



    Route::group(['prefix'=>'order'], function() {
        Route::post('/burial/add', [OrderBurialController::class, 'orderAdd'])->name('order.burial.add');
        Route::post('/product/add', [OrderProductController::class, 'orderAdd'])->name('order.product.add');
        Route::post('/service/add', [OrderServiceController::class, 'orderAdd'])->name('order.service.add');
        Route::get('/product/add/details', [OrderProductController::class, 'addOrderOne'])->name('order.product.add.details');
        
    });



    Route::group(['prefix'=>'checkout'], function() {
        Route::get('/burial', [BasketBurialContoller::class, 'checkout'])->name('checkout.burial');
        Route::get('/service', [BasketServiceContoller::class, 'checkout'])->name('checkout.service');
        Route::get('/product', [BasketProductController::class, 'cartItems'])->name('product.checkout');    
    });


















    Route::group(['middleware'=>'auth'],function(){

        Route::get('/organization/like/add/{id}', [OrganizationController::class, 'addLikeOrganization'])->name('organization.like.add');
        
        Route::get('/account/products', [AccountController::class, 'products'])->name('account.user.products');
        Route::get('/account/products/{status}/status', [AccountController::class, 'productFilter'])->name('account.user.products.status');
        Route::get('/account/products/{id}/delete', [AccountController::class, 'productDelete'])->name('account.user.products.delete');

        Route::post('/burial-info/image-personal/add', [InfoEditBurialController::class, 'imagePersonalBurialEdit'])->name('burial.image-personal.add');
        Route::post('/burial-info/image-monument/add', [InfoEditBurialController::class, 'imageMonumentBurialEdit'])->name('burial.image-monument.add');


        Route::get('/life-story/{id}/add', [LifeStoryBurialController::class, 'lifeStoryAdd'])->name('life-story.add');
        Route::get('/burial-info/{id}/edit', [InfoEditBurialController::class, 'infoBurialEdit'])->name('info-burial.edit');

        

        Route::get('/account/settings', [AccountController::class, 'userSettings'])->name('account.user.settings');
        Route::get('/account/update', [AccountController::class, 'userSettingsUpdate'])->name('account.user.settings.update');

        Route::get('/account/burial/{id}/delete', [AccountController::class, 'burialDelete'])->name('account.burial.delete');

        Route::get('/account/favorite', [AccountController::class, 'favoriteProduct'])->name('account.burial.favorite');
        Route::get('/favorite/{id}/add', [BurialController::class, 'favoriteAdd'])->name('favorite.add');
        Route::get('/favorite/{id}/delete', [BurialController::class, 'favoriteDelete'])->name('favorite.delete');

        Route::get('/account/services', [AccountController::class, 'serviceIndex'])->name('account.services.index');
        Route::get('/account/services/{status}/status', [AccountController::class, 'serviceFilter'])->name('account.services.filter');

        Route::get('/account/burials', [AccountController::class, 'burialIndex'])->name('account.burial.index');
        Route::get('/account/burials/{status}/status', [AccountController::class, 'burialFilter'])->name('account.burial.filter');

        Route::get('/account/burial-request', [AccountController::class, 'burialRequestIndex'])->name('account.burial-request.index');
        Route::get('/account/burial-request/{status}/status', [AccountController::class, 'burialRequestFilter'])->name('account.burial-request.filter');

    });



    Route::group(['middleware'=>['auth','agent']],function(){

        Route::get('/account/agent/settings', [AgentController::class, 'agentSettings'])->name('account.agent.settings');
        Route::post('/account/agent/settings/update', [AgentController::class, 'agentSettingsUpdate'])->name('account.agent.settings.update');

        Route::post('/account/agent/upload-seal/add', [AgentController::class, 'addUploadSeal'])->name('account.agent.upload-seal.add');
        Route::get('/account/agent/upload-seal/{id}/delete', [AgentController::class, 'deleteUploadSeal'])->name('account.agent.upload-seal.delete');

        Route::get('/account/agent/cemetery/{id}/delete', [AgentController::class, 'agentDeleteCemetery'])->name('account.agent.delete.cemetery');


        Route::get('/account/agent/services', [AgentController::class, 'serviceIndex'])->name('account.agent.services.index');
        Route::post('/account/agent/services/rent', [AgentController::class, 'rentService'])->name('account.agent.services.rent');
        Route::get('/account/agent/services/{status}/status', [AgentController::class, 'serviceFilter'])->name('account.agent.services.filter');
        Route::get('/account/agent/service/{id}/accept', [AgentController::class, 'acceptService'])->name('account.agent.service.accept');

    });





    Route::group(['middleware'=>['auth','organization']],function(){

        Route::get('/organizations-provider', [OrganizationController::class, 'catalogOrganizationProvider'])->name('organizations.provider');

        Route::group(['prefix'=>'account'], function() {
            
            Route::group(['prefix'=>'agency'], function() {
                Route::get('settings', [AgencyController::class, 'settings'])->name('account.agency.settings');
                Route::get('organization/settings/{id}', [AgencyOrganizationController::class, 'settings'])->name('account.agency.organization.settings');
                Route::get('choose-organization/{id}', [AgencyController::class, 'chooseOrganization'])->name('account.agency.choose.organization');
                Route::post('organization/settings/update', [AgencyOrganizationController::class, 'update'])->name('account.agency.organization.settings.update');

                Route::get('add-organization', [AgencyOrganizationController::class, 'searchOrganizations'])->name('account.agency.add.organization');     
                
            });

        });


        Route::post('/add-cemetery-agency', [AgencyController::class, 'addCemetery'])->name('organization.add.cemetery');


        Route::get('/account/agency/beautifications', [AgencyController::class, 'beautificationsIndex'])->name('account.organization.beautifications.index');

        Route::post('/account/agency/upload-seal/add', [AgencyController::class, 'addUploadSeal'])->name('account.organization.upload-seal.add');
        Route::get('/account/agency/upload-seal/{id}/delete', [AgencyController::class, 'deleteUploadSeal'])->name('account.organization.upload-seal.delete');


        Route::get('/account/agency/services', [AgencyController::class, 'serviceIndex'])->name('account.organization.services.index');
        Route::get('/account/agency/services/{status}/status', [AgencyController::class, 'serviceFilter'])->name('account.organization.services.filter');
        Route::post('/account/agency/services/rent', [AgencyController::class, 'rentService'])->name('account.organization.services.rent');
        Route::get('/account/agency/service/{id}/accept', [AgencyController::class, 'acceptService'])->name('account.organization.service.accept');

        Route::post('/account/agency/settings/update', [AgencyController::class, 'organizationSettingsUpdate'])->name('account.organization.settings.update');

        Route::get('/account/agency/beatification/{id}/accept', [AgencyController::class, 'acceptBeatification'])->name('account.organization.beatification.accept');



    });





    Route::group(['middleware'=>['auth','decoder']],function(){

        Route::group(['prefix'=>'account'], function() {
            
            Route::group(['prefix'=>'decoder'], function() {

                Route::get('withdraw/{id}', [DecoderController::class, 'withdraw'])->name('account.decoder.withdraw');

                Route::get('settings', [DecoderController::class, 'settings'])->name('account.decoder.settings');
                Route::get('settings/update', [DecoderController::class, 'settingsUpdate'])->name('account.decoder.settings.update');
                
                Route::get('training-material/video', [DecoderController::class, 'trainingMaterialVideo'])->name('account.decoder.training-material.video');
                Route::get('training-material/file', [DecoderController::class, 'trainingMaterialFile'])->name('account.decoder.training-material.file');
                
                Route::get('payments/payd', [DecoderController::class, 'paymentsPaid'])->name('account.decoder.payments.paid');
                Route::get('payments/verification', [DecoderController::class, 'paymentsOnVerification'])->name('account.decoder.payments.verification');
                
                Route::post('icon/add', [DecoderController::class, 'iconUpdateUser'])->name('account.decoder.upload-icon.add');
           
                Route::get('view-edit-burial', [DecoderController::class, 'viewEditBurial'])->name('account.decoder.burial.edit');

                Route::get('burial/add/comment', [DecoderController::class, 'addCommentBurial'])->name('account.decoder.burial.add.comment');
                
                Route::get('burial/update', [DecoderController::class, 'updateBurial'])->name('account.decoder.burial.update');

                
            });    

        });

    });








    Route::group(['middleware'=>['auth','admin']],function(){

        Route::group(['prefix'=>'account'], function() {
            Route::group(['prefix'=>'admin'], function() {


                Route::group(['prefix'=>'cemetery'], function() {
                    Route::get('/', [AdminRitualObjectsController::class, 'cemetery'])->name('account.admin.cemetery');
                    Route::get('/delete/{id}', [AdminRitualObjectsController::class, 'cemeteryDelete'])->name('account.admin.cemetery.delete');
                    
                    Route::get('/parser', [AdminRitualObjectsController::class, 'cemeteryParser'])->name('account.admin.parser.cemetery');
                    Route::post('/import', [AdminRitualObjectsController::class, 'cemeteryImport'])->name('account.admin.parsing.cemetery');
                    Route::post('/import/reviews', [AdminRitualObjectsController::class, 'cemeteryReviewsImport'])->name('account.admin.parsing.cemetery.reviews');
                    
                });
              
                Route::group(['prefix'=>'mortuary'], function() {
                    Route::get('/', [AdminRitualObjectsController::class, 'mortuary'])->name('account.admin.mortuary');
                    Route::get('/delete/{id}', [AdminRitualObjectsController::class, 'mortuaryDelete'])->name('account.admin.mortuary.delete');
                    Route::get('/parser', [AdminRitualObjectsController::class, 'mortuaryParser'])->name('account.admin.parser.mortuary');
                    Route::post('/import', [AdminRitualObjectsController::class, 'mortuaryImport'])->name('account.admin.parsing.mortuary');
                });

                Route::group(['prefix'=>'crematorium'], function() {
                    Route::get('/', [AdminRitualObjectsController::class, 'crematorium'])->name('account.admin.crematorium');
                    Route::get('/delete/{id}', [AdminRitualObjectsController::class, 'crematoriumDelete'])->name('account.admin.crematorium.delete');
                    Route::get('/parser', [AdminRitualObjectsController::class, 'crematoriumParser'])->name('account.admin.parser.crematorium');
                    Route::post('/import', [AdminRitualObjectsController::class, 'crematoriumImport'])->name('account.admin.parsing.crematorium');
                });

                Route::group(['prefix'=>'columbarium'], function() {
                    Route::get('/', [AdminRitualObjectsController::class, 'columbarium'])->name('account.admin.columbarium');
                    Route::get('/delete/{id}', [AdminRitualObjectsController::class, 'columbariumDelete'])->name('account.admin.columbarium.delete');
                    Route::get('/parser', [AdminRitualObjectsController::class, 'columbariumParser'])->name('account.admin.parser.columbarium');
                    Route::post('/import', [AdminRitualObjectsController::class, 'columbariumImport'])->name('account.admin.parsing.columbarium');
                });
              
            });
    
        });

    });

});
