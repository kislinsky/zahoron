<?php

use App\Http\Controllers\Account\Admin\AdminBurialController;
use App\Http\Controllers\Account\Admin\AdminOrganizationController;
use App\Http\Controllers\Account\Admin\AdminRitualObjectsController;
use App\Http\Controllers\Account\Admin\AdminSEOController;
use App\Http\Controllers\Account\Agency\AgencyController;


use App\Http\Controllers\Account\Agency\AgencyOrganizationController;
use App\Http\Controllers\Account\Agency\AgencyOrganizationProviderController;
use App\Http\Controllers\Account\Agency\Aplication\AgencyOrganizationAplicationBeautificationController;
use App\Http\Controllers\Account\Agency\Aplication\AgencyOrganizationAplicationDeadController;
use App\Http\Controllers\Account\Agency\Aplication\AgencyOrganizationAplicationFuneralServiceController;
use App\Http\Controllers\Account\Agency\Aplication\AgencyOrganizationAplicationMemorialController;
use App\Http\Controllers\Account\AgentController;
use App\Http\Controllers\Account\DecoderController;
use App\Http\Controllers\Account\HomeController;
use App\Http\Controllers\Account\User\AccountController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;


use App\Http\Controllers\BasketBurialContoller;
use App\Http\Controllers\BasketProductController;
use App\Http\Controllers\BasketServiceContoller;
use App\Http\Controllers\BeautificationController;
use App\Http\Controllers\BurialController;
use App\Http\Controllers\CategoryProductController;
use App\Http\Controllers\CemeteriesController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\ColumbariumController;
use App\Http\Controllers\CrematoriumController;
use App\Http\Controllers\DeadController;
use App\Http\Controllers\DistrictController;
use App\Http\Controllers\FuneralController;
use App\Http\Controllers\InfoEditBurialController;
use App\Http\Controllers\LifeStoryBurialController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\MemorialController;
use App\Http\Controllers\MortuaryController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\OrderBurialController;
use App\Http\Controllers\OrderProductController;
use App\Http\Controllers\OrderServiceController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductPriceListController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\WordsMemoryController;
use App\Http\Controllers\YooMoneyController;
use App\Http\Middleware\Authenticate;
use Filament\Facades\Filament;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;



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
$city = request()->segment(1); // Более безопасный и понятный способ получить первый сегмент URL

if($city!='livewire'){
    if (!request()->is('storage/*') && !request()->is('css/*') && !request()->is('js/*') && !request()->is('admin/*') && !request()->is('filament*') && $city!='admin' ) {    
        if(city_by_slug($city) == null){
            setcookie('city', '', -1, '/');
            setcookie("city", first_city_id(), time()+20*24*60*60,'/');
            header("location: /".first_city_slug());        

        } else{
            $c_b_s__ = city_by_slug($city);
            if(!isset($_COOKIE['city'])){
                setcookie("city", first_city_id(), time()+20*24*60*60,'/');
                header("Refresh:0");
                
            }
            if(isset($_COOKIE['city']) && $_COOKIE['city'] != $c_b_s__->id){
                setcookie('city', '', -1, '/');
                setcookie("city", $c_b_s__->id, time()+20*24*60*60,'/');
                header("Refresh:0");
                
            }
        }
    }
}

// Route::group(['prefix' => '{city}', 'middleware' => 'check.city'], function () {
//     Route::get('/', [CityController::class, 'index'])->name('city.index');
//     Route::get('/change-city/{selectedCity}', [CityController::class, 'changeCity'])->name('city.change');
// });



Route::group(['prefix' => $city, 'middleware' => 'check.city'], function () {
    


    Route::post('/reset-password/phone', [ForgotPasswordController::class, 'resetPasswordWithPhone'])->name('reset-password.phone');
    Route::get('/verificate-code/phone', [ForgotPasswordController::class, 'pageVerificateCode'])->name('reset-password.phone.verify.code');
    Route::post('/verificate-code/phone/send', [ForgotPasswordController::class, 'verificateCode'])->name('reset-password.phone.verify.code.send');
    Route::get('/new-password/phone', [ForgotPasswordController::class, 'passwordNewPhone'])->name('reset-password.phone.new');
    Route::post('/accept-new-password/phone', [ForgotPasswordController::class, 'acceptPasswordNewPhone'])->name('reset-password.phone.new.accept');
    


    Route::group(['prefix'=>'yoomoney'], function() {
        Route::get('/payment', [YooMoneyController::class, 'createPayment'])->name('yoomoney.payment');
        Route::post('/callback', [YooMoneyController::class, 'handleCallback'])->name('yoomoney.callback');
        Route::get('/success', [YooMoneyController::class, 'success'])->name('yoomoney.success');
    });

    Auth::routes();
    
    Route::get('/', [MainController::class, 'index'])->name('index');
    Route::get('/speczialist', [MainController::class, 'speczialist'])->name('speczialist');

    Route::get('/change-theme', [MainController::class, 'changeTheme'])->name('change-theme');

    Route::get('/city/{id}', [CityController::class, 'selectCity'])->name('city.select');


    Route::get('/organization/{slug}', [OrganizationController::class, 'single'])->name('organization.single');
    Route::get('/organizations', [OrganizationController::class, 'catalogOrganization'])->name('organizations');
    Route::get('/organizations/{slug}', [OrganizationController::class, 'catalogOrganizationCategory'])->name('organizations.category');



    Route::post('/register-with-phone', [RegisterController::class, 'registerWithPhone'])->name('register.phone');
    Route::get('/confirm-inn-information-phone', [RegisterController::class, 'confirmInnInformationPhone'])->name('confirm.inn.information.phone');
    Route::get('/confirm-inn-information-email', [RegisterController::class, 'confirmInnInformationEmail'])->name('confirm.inn.information.email');
    Route::post('/accept-inn-information', [RegisterController::class, 'acceptInnInformation'])->name('accept.inn.information');
    Route::post('/create-user-organization-email', [RegisterController::class, 'createUserOrganizationEmail'])->name('create.user.organization.email');

    

    Route::get('/verify-code', [RegisterController::class, 'verifyCode'])->name('register.verify.code');
    Route::post('/verify-code-send', [RegisterController::class, 'verifyCodeSend'])->name('register.verify.code.send');
    Route::post('/login-with-phone', [LoginController::class, 'loginWithPhone'])->name('login.phone');

    

    
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');


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
        Route::get('/city/input', [CityController::class, 'ajaxCityInInput'])->name('city.input.ajax');
        Route::get('/city/from/edge', [CityController::class, 'ajaxCityFromEdge'])->name('city.from.edge.ajax');

        Route::post('/city/search', [CityController::class, 'ajaxCitySearchInInput'])->name('ajax.cities.search.input');

        

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



    Route::get('/result-search', [BurialController::class, 'searchBurialResult'])->name('search.burial.result');
    Route::get('/search', [BurialController::class, 'searchBurial'])->name('search.burial');




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



    Route::get('/kontakty', [MainController::class, 'contacts'])->name('contacts');
    Route::get('/terms-of-use', [MainController::class, 'termsIUser'])->name('terms-user');

    Route::get('/search-filter', [MainController::class, 'searchProductFilter'])->name('page.search.burial.filter');
    Route::get('/search-filter-who', [BurialController::class, 'searchProductFilter'])->name('search.burial.filter');
    Route::get('/search-request', [BurialController::class, 'searchProductRequest'])->name('page.search.burial.request');
    Route::get('/search-request/add', [BurialController::class, 'searchProductRequestAdd'])->name('search.burial.request');


    Route::get('/our-works', [MainController::class, 'ourWorks'])->name('our.products');


    Route::get('/cemeteries', [CemeteriesController::class, 'index'])->name('cemeteries');





    Route::group(['prefix'=>'cemetery'], function() {
        Route::get('/{cemetery}', [CemeteriesController::class, 'singleCemetery'])->name('cemeteries.single');
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
        Route::post('/product/add/details', [OrderProductController::class, 'addOrderOne'])->name('order.product.add.details');
        
    });



    Route::group(['prefix'=>'checkout'], function() {
        Route::get('/burial', [BasketBurialContoller::class, 'checkout'])->name('checkout.burial');
        Route::get('/service', [BasketServiceContoller::class, 'checkout'])->name('checkout.service');
        Route::get('/product', [BasketProductController::class, 'cartItems'])->name('product.checkout');    
    });



    


    Route::group(['middleware'=>'auth'],function(){

        Route::post('/organization/send-code', [OrganizationController::class, 'sendCode'])->name('organization.send-code');
        Route::post('/organization/accept-code', [OrganizationController::class, 'acceptCode'])->name('organization.accept-code');



            Route::group(['prefix'=>'account'], function() {
            

                Route::group(['prefix'=>'user'], function() {

                    Route::group(['middleware'=>'user.role.check'],function(){
                       
                        Route::group(['prefix'=>'products'], function() {
                            Route::get('/', [AccountController::class, 'products'])->name('account.user.products');
                            Route::delete('/{order}/delete', [AccountController::class, 'productDelete'])->name('account.user.product.delete');        
                        }); 
                        
                        Route::get('/services', [AccountController::class, 'services'])->name('account.user.services.index');       
                        Route::get('/service/{order}/pay', [AccountController::class, 'payService'])->name('account.user.service.pay');       
                        Route::get('/burial-request/{order}/callback', [AccountController::class, 'callbackPayService'])->name('account.user.burial-request.callback');            

                        Route::get('/burials', [AccountController::class, 'burials'])->name('account.user.burial');

                        Route::get('/burial-requests', [AccountController::class, 'burialRequestIndex'])->name('account.user.burial-request.index');
                        Route::get('/burial-request/{order}/pay', [AccountController::class, ''])->name('account.user.burial-request.pay');            
                        Route::get('/burial-request/{order}/callback', [AccountController::class, 'callbackPayBurialRequest'])->name('account.user.burial-request.callback');            

                        Route::delete('/burial-request/{burial_request}/delete', [AccountController::class, 'burialRequestDelete'])->name('account.user.burial-request.delete');
                        Route::get('/burial/{order}/pay', [AccountController::class, 'payBurial'])->name('account.user.burial.pay');            
                        Route::get('/burial/{id}/delete', [AccountController::class, 'burialDelete'])->name('account.burial.delete');
                        Route::get('/burials/favorite', [AccountController::class, 'favoriteProduct'])->name('account.user.burial.favorite');
                    });

                    Route::get('/settings', [AccountController::class, 'userSettings'])->name('account.user.settings');
                    Route::post('/update', [AccountController::class, 'userSettingsUpdate'])->name('account.user.settings.update');

                });
            
            });



        Route::get('/organization/like/add/{id}', [OrganizationController::class, 'addLikeOrganization'])->name('organization.like.add');
        
       

        Route::post('/burial-info/image-personal/add', [InfoEditBurialController::class, 'imagePersonalBurialEdit'])->name('burial.image-personal.add');
        Route::post('/burial-info/image-monument/add', [InfoEditBurialController::class, 'imageMonumentBurialEdit'])->name('burial.image-monument.add');


        Route::get('/life-story/{id}/add', [LifeStoryBurialController::class, 'lifeStoryAdd'])->name('life-story.add');
        Route::get('/burial-info/{id}/edit', [InfoEditBurialController::class, 'infoBurialEdit'])->name('info-burial.edit');

       

        Route::get('/favorite/{id}/add', [BurialController::class, 'favoriteAdd'])->name('favorite.add');
        Route::get('/favorite/{id}/delete', [BurialController::class, 'favoriteDelete'])->name('favorite.delete');

      
    });



    Route::group(['middleware'=>['auth','agent']],function(){
        
        Route::group(['middleware'=>'user.role.check'],function(){

            Route::get('/services', [AgentController::class, 'services'])->name('account.agent.services.index');       
            Route::post('/account/agent/services/rent', [AgentController::class, 'rentService'])->name('account.agent.services.rent');
            Route::patch('/account/agent/service/{id}/accept', [AgentController::class, 'acceptService'])->name('account.agent.service.accept');
            Route::patch('/account/agent/service/{id}/get-to-work', [AgentController::class, 'getToWorkService'])->name('account.agent.service.get-to-work');
        
        });

        Route::get('/account/agent/settings', [AgentController::class, 'agentSettings'])->name('account.agent.settings');
        Route::post('/account/agent/settings/update', [AgentController::class, 'agentSettingsUpdate'])->name('account.agent.settings.update');

        Route::post('/account/agent/upload-seal/add', [AgentController::class, 'addUploadSeal'])->name('account.agent.upload-seal.add');
        Route::get('/account/agent/upload-seal/{id}/delete', [AgentController::class, 'deleteUploadSeal'])->name('account.agent.upload-seal.delete');



        
        
    });


    Route::group(['middleware'=>['auth','catalog.provider']],function(){   
        Route::get('/organizations-provider', [OrganizationController::class, 'catalogOrganizationProvider'])->name('organizations.provider');
    });
    

    Route::group(['middleware'=>['auth','organization']],function(){

        Route::group(['prefix'=>'account'], function() {
            
            Route::group(['prefix'=>'agency'], function() {


                Route::group(['middleware'=>'user.role.check'],function(){

                    Route::group(['prefix'=>'aplication'], function() {

                        Route::group(['prefix'=>'dead'], function() {
                            Route::get('new', [AgencyOrganizationAplicationDeadController::class, 'new'])->name('account.agency.organization.aplication.dead.new');
                            Route::get('in-work', [AgencyOrganizationAplicationDeadController::class, 'inWork'])->name('account.agency.organization.aplication.dead.in-work');
                            Route::get('completed', [AgencyOrganizationAplicationDeadController::class, 'completed'])->name('account.agency.organization.aplication.dead.completed');
                            // Route::get('not-completed', [AgencyOrganizationAplicationDeadController::class, 'Notcompleted'])->name('account.agency.organization.aplication.dead.not-completed');
                            Route::patch('{aplication}/complete', [AgencyOrganizationAplicationDeadController::class, 'complete'])->name('account.agency.organization.aplication.dead.complete');     
                            Route::patch('{aplication}/accept', [AgencyOrganizationAplicationDeadController::class, 'accept'])->name('account.agency.organization.aplication.dead.accept');     

                        });

                        Route::group(['prefix'=>'memorial'], function() {
                            Route::get('new', [AgencyOrganizationAplicationMemorialController::class, 'new'])->name('account.agency.organization.aplication.memorial.new');
                            Route::get('in-work', [AgencyOrganizationAplicationMemorialController::class, 'inWork'])->name('account.agency.organization.aplication.memorial.in-work');
                            Route::get('completed', [AgencyOrganizationAplicationMemorialController::class, 'completed'])->name('account.agency.organization.aplication.memorial.completed');
                            Route::get('not-completed', [AgencyOrganizationAplicationMemorialController::class, 'Notcompleted'])->name('account.agency.organization.aplication.memorial.not-completed');
                            Route::patch('{aplication}/complete', [AgencyOrganizationAplicationMemorialController::class, 'complete'])->name('account.agency.organization.aplication.memorial.complete');     
                            Route::patch('{aplication}/accept', [AgencyOrganizationAplicationMemorialController::class, 'accept'])->name('account.agency.organization.aplication.memorial.accept');     

                        });


                        Route::group(['prefix'=>'beautification'], function() {
                            Route::get('new', [AgencyOrganizationAplicationBeautificationController::class, 'new'])->name('account.agency.organization.aplication.beautification.new');
                            Route::get('in-work', [AgencyOrganizationAplicationBeautificationController::class, 'inWork'])->name('account.agency.organization.aplication.beautification.in-work');
                            Route::get('completed', [AgencyOrganizationAplicationBeautificationController::class, 'completed'])->name('account.agency.organization.aplication.beautification.completed');
                            Route::get('not-completed', [AgencyOrganizationAplicationBeautificationController::class, 'Notcompleted'])->name('account.agency.organization.aplication.beautification.not-completed');
                            Route::patch('{aplication}/accept', [AgencyOrganizationAplicationBeautificationController::class, 'accept'])->name('account.agency.organization.aplication.beautification.accept');     
                            Route::patch('{aplication}/complete', [AgencyOrganizationAplicationBeautificationController::class, 'complete'])->name('account.agency.organization.aplication.beautification.complete');     

                        });

                        Route::group(['prefix'=>'funeral-service'], function() {
                            Route::get('new', [AgencyOrganizationAplicationFuneralServiceController::class, 'new'])->name('account.agency.organization.aplication.funeral-service.new');
                            Route::get('in-work', [AgencyOrganizationAplicationFuneralServiceController::class, 'inWork'])->name('account.agency.organization.aplication.funeral-service.in-work');
                            Route::get('completed', [AgencyOrganizationAplicationFuneralServiceController::class, 'completed'])->name('account.agency.organization.aplication.funeral-service.completed');
                            Route::get('not-completed', [AgencyOrganizationAplicationFuneralServiceController::class, 'Notcompleted'])->name('account.agency.organization.aplication.funeral-service.not-completed');
                            Route::get('filter-service', [AgencyOrganizationAplicationFuneralServiceController::class, 'filterService'])->name('account.agency.organization.aplication.funeral-service.filter');
                            Route::patch('{aplication}/accept', [AgencyOrganizationAplicationFuneralServiceController::class, 'accept'])->name('account.agency.organization.aplication.funeral-service.accept');     
                            Route::patch('{aplication}/complete', [AgencyOrganizationAplicationFuneralServiceController::class, 'complete'])->name('account.agency.organization.aplication.funeral-service.complete');     
                        });
                        
                    });
                    Route::get('organization/create-page', [AgencyOrganizationController::class, 'createPage'])->name('account.agency.organization.create-page');
                    Route::post('organization/create', [AgencyOrganizationController::class, 'create'])->name('account.agency.organization.create');
                    Route::get('organization/settings/{id}', [AgencyOrganizationController::class, 'settings'])->name('account.agency.organization.settings');
                    Route::get('choose-organization/{id}', [AgencyController::class, 'chooseOrganization'])->name('account.agency.choose.organization');
                    Route::post('organization/settings/update', [AgencyOrganizationController::class, 'update'])->name('account.agency.organization.settings.update');
    
                    Route::get('add-organization', [AgencyOrganizationController::class, 'searchOrganizations'])->name('account.agency.add.organization');     
                    
                    Route::get('applications', [AgencyOrganizationController::class, 'aplications'])->name('account.agency.applications');     
                    Route::get('applications/{type_service}/pay', [AgencyOrganizationController::class, 'payApplication'])->name('account.agency.applications.pay');     
                    
    
                    Route::get('products', [AgencyOrganizationController::class, 'allProducts'])->name('account.agency.products');     
                    Route::get('add-product', [AgencyOrganizationController::class, 'addProduct'])->name('account.agency.add.product');     
                    Route::get('delete-product/{id}', [AgencyOrganizationController::class, 'deleteProduct'])->name('account.agency.delete.product');     
                    Route::get('update-product-price', [AgencyOrganizationController::class, 'updatePriceProduct'])->name('account.agency.update.product.price');     
                    Route::get('search-product', [AgencyOrganizationController::class, 'searchProduct'])->name('account.agency.search.product');     
                    Route::get('filters-product', [AgencyOrganizationController::class, 'filtersProduct'])->name('account.agency.filters.product');     
                    Route::post('create-product', [AgencyOrganizationController::class, 'createProduct'])->name('account.agency.create.product');     
                    Route::get('product/orders/new', [AgencyOrganizationController::class, 'ordersNew'])->name('account.agency.product.orders.new');  
                    Route::get('product/orders/in-work', [AgencyOrganizationController::class, 'ordersInWork'])->name('account.agency.product.orders.in-work');  
                    Route::get('product/orders/completed', [AgencyOrganizationController::class, 'ordersCompleted'])->name('account.agency.product.orders.completed');     
                    Route::patch('product/order/{order}/complete', [AgencyOrganizationController::class, 'orderComplete'])->name('account.agency.product.order.complete');     
                    Route::patch('product/order/{order}/accept', [AgencyOrganizationController::class, 'orderAccept'])->name('account.agency.product.order.accept');     
    
                    
    
    
    
                    Route::get('reviews-organization', [AgencyOrganizationController::class, 'reviewsOrganization'])->name('account.agency.reviews.organization');     
                    Route::get('reviews-products', [AgencyOrganizationController::class, 'reviewsProduct'])->name('account.agency.reviews.product');     
                    Route::get('review-organization/{id}/delete', [AgencyOrganizationController::class, 'reviewOrganizationDelete'])->name('account.agency.review.organization.delete');     
                    Route::get('review-product/{id}/delete', [AgencyOrganizationController::class, 'reviewProductDelete'])->name('account.agency.review.product.delete');     
                    Route::get('review-organization/{id}/accept', [AgencyOrganizationController::class, 'reviewOrganizationAccept'])->name('account.agency.review.organization.accept');     
                    Route::get('review-product/{id}/accept', [AgencyOrganizationController::class, 'reviewProductAccept'])->name('account.agency.review.product.accept');     
                    Route::get('review-organization/update', [AgencyOrganizationController::class, 'updateReviewOrganization'])->name('account.agency.review.organization.update');     
                    Route::get('review-product/update', [AgencyOrganizationController::class, 'updateReviewProduct'])->name('account.agency.review.product.update');     
                    Route::get('review-organization/update/organization-response', [AgencyOrganizationController::class, 'updateOrganizationResponseReviewOrganization'])->name('account.agency.review.organization.update.organization-response');     
                    Route::get('review-product/update/organization-response', [AgencyOrganizationController::class, 'updateOrganizationResponseReviewProduct'])->name('account.agency.review.product.update.organization-response');     
                    
                    Route::get('provider/requests/products/add', [AgencyOrganizationProviderController::class, 'requestsCostProductSuppliers'])->name('account.agency.provider.requests.products.add');     
                    Route::get('provider/requests/products/create', [AgencyOrganizationProviderController::class, 'addRequestsCostProductSuppliers'])->name('account.agency.provider.requests.products.create');     
                    Route::get('provider/requests/products/created', [AgencyOrganizationProviderController::class, 'createdRequestsCostProductSuppliers'])->name('account.agency.provider.requests.products.created');     
                    Route::get('provider/requests/products/answer', [AgencyOrganizationProviderController::class, 'answerRequestsCostProductSuppliers'])->name('account.agency.provider.requests.products.answer');     
                    Route::delete('provider/request/products/{request}/delete', [AgencyOrganizationProviderController::class, 'deletRequest'])->name('account.agency.provider.request.delete');     
    
                    
                    
    
                    Route::get('like-organizations', [AgencyOrganizationProviderController::class, 'likeOrganizations'])->name('account.agency.provider.like.organizations');     
    
                    Route::get('provider/stocks', [AgencyOrganizationProviderController::class, 'stocksOrganizationProviders'])->name('account.agency.provider.stocks');     
                    Route::get('provider/discounts', [AgencyOrganizationProviderController::class, 'discountsOrganizationProviders'])->name('account.agency.provider.discounts');     
    
                    Route::get('provider/offer/add', [AgencyOrganizationProviderController::class, 'addOfferToProvider'])->name('account.agency.provider.offer.add');     
                    Route::post('provider/offer/add', [AgencyOrganizationProviderController::class, 'createOfferToProvider'])->name('account.agency.provider.offer.create');     
    
                    Route::get('provider/offers/created', [AgencyOrganizationProviderController::class, 'createdOfferToProvider'])->name('account.agency.provider.offer.created');     
                    Route::get('provider/offers/answers', [AgencyOrganizationProviderController::class, 'answerOfferToProvider'])->name('account.agency.provider.offer.answers'); 
    
    
                    Route::get('provider/offers/created/category', [AgencyOrganizationProviderController::class, 'filterCategoryCreatedOfferToProvider'])->name('account.agency.provider.offer.created.category');     
                    Route::get('provider/offers/answers/category', [AgencyOrganizationProviderController::class, 'filterCategoryAnswerOfferToProvider'])->name('account.agency.provider.offer.answers.category'); 
    
    
                    Route::delete('provider/offer/{offer}/delete', [AgencyOrganizationProviderController::class, 'deleteOffer'])->name('account.agency.provider.offer.delete');     

                });
                
                Route::get('settings', [AgencyController::class, 'settings'])->name('account.agency.settings');
                
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
                
                Route::group(['middleware'=>'user.role.check'],function(){
                
                    Route::get('training-material/video', [DecoderController::class, 'trainingMaterialVideo'])->name('account.decoder.training-material.video');
                    Route::get('training-material/file', [DecoderController::class, 'trainingMaterialFile'])->name('account.decoder.training-material.file');
                    
                    Route::get('payments/payd', [DecoderController::class, 'paymentsPaid'])->name('account.decoder.payments.paid');
                    Route::get('payments/verification', [DecoderController::class, 'paymentsOnVerification'])->name('account.decoder.payments.verification');
                    
                    Route::post('icon/add', [DecoderController::class, 'iconUpdateUser'])->name('account.decoder.upload-icon.add');
               
                    Route::get('view-edit-burial', [DecoderController::class, 'viewEditBurial'])->name('account.decoder.burial.edit');
    
                    Route::get('burial/add/comment', [DecoderController::class, 'addCommentBurial'])->name('account.decoder.burial.add.comment');
                    
                    Route::get('burial/update', [DecoderController::class, 'updateBurial'])->name('account.decoder.burial.update');

                    Route::get('withdraw/{id}', [DecoderController::class, 'withdraw'])->name('account.decoder.withdraw');

                });

                Route::get('settings', [DecoderController::class, 'settings'])->name('account.decoder.settings');
                Route::get('settings/update', [DecoderController::class, 'settingsUpdate'])->name('account.decoder.settings.update');

            });    

        });

    });








    Route::group(['middleware'=>['auth','admin']],function(){

        Route::group(['prefix'=>'account'], function() {
            Route::group(['prefix'=>'admin-old'], function() {


                Route::group(['prefix'=>'seo'], function() {
                    Route::get('/settings', [AdminSEOController::class, 'settings'])->name('account.admin.seo.settings');
                    Route::get('/object/{page}', [AdminSEOController::class, 'object'])->name('account.admin.seo.object');
                    Route::post('/object/{page}/update', [AdminSEOController::class, 'updateSeo'])->name('account.admin.seo.object.update');
                });
                

                Route::group(['prefix'=>'organization'], function() {
                    Route::get('/parser', [AdminOrganizationController::class, 'parser'])->name('account.admin.parser.organization');
                    Route::post('/import', [AdminOrganizationController::class, 'import'])->name('account.admin.parsing.organization');
                    Route::post('/import/reviews', [AdminOrganizationController::class, 'importReviews'])->name('account.admin.parsing.organization.reviews');
                    Route::post('/import/prices', [AdminOrganizationController::class, 'importPrices'])->name('account.admin.parsing.organization.prices');
                    
                });
                


                Route::group(['prefix'=>'burial'], function() {
                    Route::get('/', [AdminBurialController::class, 'index'])->name('account.admin.burial');
                    Route::delete('/delete/{burial}', [AdminBurialController::class, 'delete'])->name('account.admin.burial.delete');
                    Route::get('/parser', [AdminBurialController::class, 'parser'])->name('account.admin.burial.parser');
                    Route::post('/import', [AdminBurialController::class, 'import'])->name('account.admin.burial.import');
                    
                });

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
