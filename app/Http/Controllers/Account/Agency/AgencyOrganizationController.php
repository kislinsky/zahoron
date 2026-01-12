<?php

namespace App\Http\Controllers\Account\Agency;

use App\Http\Controllers\Controller;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\TypeService;
use App\Models\Wallet;
use App\Services\Account\Agency\AgencyOrganizationService;
use Illuminate\Http\Request;

class AgencyOrganizationController extends Controller
{

    public static function createPage(){
        return AgencyOrganizationService::createPage();
    }

    public static function create(Request $request)
    {
        $data = $request->validate([
            'title'                   => ['string', 'required'],
            'content'                 => ['string', 'nullable'],
            'phone'                   => ['string', 'nullable'],
            'telegram'                => ['string', 'nullable'],
            'whatsapp'                => ['string', 'nullable'],
            'email'                   => ['string', 'nullable'],
            'city_id'                 => ['integer', 'required'],
            'next_to'                 => ['string', 'nullable'],
            'underground'             => ['string', 'nullable'],
            'adres'                   => ['string', 'required'],
            'width'                   => ['numeric', 'required'],
            'longitude'               => ['numeric', 'required'],
            'available_installments'  => ['boolean', 'nullable'],
            'found_cheaper'           => ['boolean', 'nullable'],
            'сonclusion_contract'     => ['boolean', 'nullable'],
            'state_compensation'      => ['boolean', 'nullable'],
            'categories_organization' => ['nullable', 'array'],
            'price_cats_organization' => ['nullable', 'array'],
            'working_day'             => ['nullable', 'array'],
            'holiday_day'             => ['nullable', 'array'],
            'cemetery_ids'            => 'required|array',
            'img'                     => 'nullable|max:2048',
            'img_main'                => 'nullable|max:2048',
            'images'                  => 'array|max:5', // Здесь вы ограничиваете количество изображений до 5
            'images.0'                => 'image|mimes:jpeg,jpg,png,gif,svg,webp,bmp,tiff,ico,heic,heif|max:2048',
            'images.1'                => 'image|mimes:jpeg,jpg,png,gif,svg,webp,bmp,tiff,ico,heic,heif|max:2048',
            'images.2'                => 'image|mimes:jpeg,jpg,png,gif,svg,webp,bmp,tiff,ico,heic,heif|max:2048',
            'images.3'                => 'image|mimes:jpeg,jpg,png,gif,svg,webp,bmp,tiff,ico,heic,heif|max:2048',
            'images.4'                => 'image|mimes:jpeg,jpg,png,gif,svg,webp,bmp,tiff,ico,heic,heif|max:2048',
        ]);

        return AgencyOrganizationService::create($data);
    }

    public static function settings($id){
        return AgencyOrganizationService::settings($id);
    }


    public static function update(Request $request)
    {
        $data = request()->validate([
            'images'                  => ['nullable'], // Поле может быть пустым
            'images.*'                => ['nullable', function ($attribute, $value, $fail) {
                // Проверяем, является ли значение файлом или ссылкой
                if (!is_string($value) && !($value instanceof \Illuminate\Http\UploadedFile)) {
                    $fail('Поле ' . $attribute . ' должно быть файлом или ссылкой.');
                }
            }],
            'img'                     => ['nullable','max:2048'],
            'img_main'                => ['nullable','max:2048'],
            'cemetery_ids.*'          => ['nullable'],
            'id'                      => ['integer', 'required'],
            'title'                   => ['string', 'required'],
            'content'                 => ['string', 'nullable'],
            'phone'                   => ['string', 'nullable'],
            'telegram'                => ['string', 'nullable'],
            'whatsapp'                => ['string', 'nullable'],
            'email'                   => ['string', 'nullable'],
            'city_id'                 => ['integer', 'required'],
            'next_to'                 => ['string', 'nullable'],
            'underground'             => ['string', 'nullable'],
            'adres'                   => ['string', 'required'],
            'width'                   => ['string', 'required'],
            'longitude'               => ['string', 'required'],
            'categories_organization' => ['nullable'],
            'price_cats_organization' => ['nullable'],
            'working_day'             => ['nullable'],
            'holiday_day'             => ['nullable'],
            'available_installments'  => ['nullable'],
            'found_cheaper'           => ['nullable'],
            'сonclusion_contract'     => ['nullable'],
            'state_compensation'      => ['nullable'],

        ]);

        return AgencyOrganizationService::update($data);
    }

    public static function searchOrganizations(Request $request){
        $data=request()->validate([
            's'=>['nullable','string'],
            'city_id'=>['nullable','integer'],
        ]);
        return AgencyOrganizationService::searchOrganizations($data);
    }

    public static function aplications(){

        return AgencyOrganizationService::aplications();
    }




    public static function addProduct(){
        return AgencyOrganizationService::addProduct();
    }

    public static function allProducts(){
        $data=request()->validate([
            'category_id'=>['nullable','string'],
            'parent_category_id'=>['nullable','string'],
            's'=>['nullable','string'],
        ]);
        return AgencyOrganizationService::allProducts($data);
    }

    public static function deleteProduct($id){
        return AgencyOrganizationService::deleteProduct($id);
    }


    public static function updatePriceProduct(Request $request){
        $data=request()->validate([
            'price'=>['required','integer'],
            'product_id'=>['required','integer'],
        ]);
        return AgencyOrganizationService::updatePriceProduct($data);
    }


    public static function searchProduct(Request $request){
        $data=request()->validate([
            's'=>['nullable','string'],
        ]);
        return AgencyOrganizationService::searchProduct($data);
    }

    public static function filtersProduct(Request $request){
        $data=request()->validate([
            'category_id'=>['nullable','string'],
            'parent_category_id'=>['nullable','string'],
        ]);
        return AgencyOrganizationService::filtersProduct($data);

    }

    public static function createProduct(Request $request){
        $data=request()->validate([
            'title'=>['required','string'],
            'content'=>['required','string'],
            'price'=>['required','string'],
            'price_sale'=>['nullable','integer'],
            'material'=>['nullable','string'],
            'size'=>['nullable','string'],
            'your_size'=>['nullable','string'],
            'parameters'=>['nullable','string'],
            'width'=>['nullable','string'],
            'longitude'=>['nullable','string'],
            'menus'=>['nullable','string'],
            'images' => 'required|array|max:5', // Здесь вы ограничиваете количество изображений до 5
            'images.0' => 'required|image|mimes:jpeg,jpg,png,gif,svg,webp,bmp,tiff,ico,heic,heif|max:2048',
            'images.1' => 'image|mimes:jpeg,jpg,png,gif,svg,webp,bmp,tiff,ico,heic,heif|max:2048',
            'images.2' => 'image|mimes:jpeg,jpg,png,gif,svg,webp,bmp,tiff,ico,heic,heif|max:2048',
            'images.3' => 'image|mimes:jpeg,jpg,png,gif,svg,webp,bmp,tiff,ico,heic,heif|max:2048',
            'images.4' => 'image|mimes:jpeg,jpg,png,gif,svg,webp,bmp,tiff,ico,heic,heif|max:2048',
            'cat'=>['required','integer'],
            'cat_children'=>['required','integer'],
        ]);

        return AgencyOrganizationService::createProduct($data);
    }


    public static function editProduct(Product $product){
        return AgencyOrganizationService::editProduct($product);
    }

    public static function updateProduct(Request $request, $product_id){
        $product = Product::where('id', $product_id)
            ->where('organization_id', user()->organization()->id)
            ->firstOrFail();

        $data = request()->validate([
            'title' => ['required', 'string', 'max:120'],
            'content' => ['required', 'string', 'max:1000'],
            'price' => ['required', 'numeric', 'min:1'],
            'price_sale' => ['nullable', 'numeric', 'min:1'],
            'material' => ['nullable', 'string'],
            'size' => ['nullable', 'string'],
            'your_size' => ['nullable', 'string'],
            'parameters' => ['nullable', 'string'],
            'width' => ['nullable', 'string'],
            'longitude' => ['nullable', 'string'],
            'menus' => ['nullable', 'string'],
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpeg,jpg,png,gif,svg,webp,bmp,tiff,ico,heic,heif|max:2048',
            'cat' => ['required', 'integer'],
            'cat_children' => ['required', 'integer'],
        ]);
        return AgencyOrganizationService::updateProduct( $data, $product);
    }
    
    public static function reviewsOrganization(){
        return AgencyOrganizationService::reviewsOrganization();
    }

    public static function reviewsProduct(){
        return AgencyOrganizationService::reviewsProduct();
    }


    public static function reviewOrganizationAccept($id){
        return AgencyOrganizationService::reviewOrganizationAccept($id);

    }

    public static function reviewProductAccept($id){
        return AgencyOrganizationService::reviewProductAccept($id);

    }


    public static function reviewOrganizationDelete($id){
        return AgencyOrganizationService::reviewOrganizationDelete($id);

    }

    public static function reviewProductDelete($id){
        return AgencyOrganizationService::reviewProductDelete($id);

    }

    public static function updateReviewOrganization(Request $request){
        $data=request()->validate([
            'id_review'=>['required','integer'],
            'content_review'=>['required','string'],
        ]);
        return AgencyOrganizationService::updateReviewOrganization($data);

    }

    public static function updateReviewProduct(Request $request){
        $data=request()->validate([
            'id_review'=>['required','integer'],
            'content_review'=>['required','string'],
        ]);
        return AgencyOrganizationService::updateReviewProduct($data);

    }


    public static function updateOrganizationResponseReviewOrganization(Request $request){
        $data=request()->validate([
            'id_review'=>['required','integer'],
            'organization_response_review'=>['required','string'],
        ]);
        return AgencyOrganizationService::updateOrganizationResponseReviewOrganization($data);

    }

    public static function updateOrganizationResponseReviewProduct(Request $request){
        $data=request()->validate([
            'id_review'=>['required','integer'],
            'organization_response_review'=>['required','string'],
        ]);
        return AgencyOrganizationService::updateOrganizationResponseReviewProduct($data);
    }


    public static function ordersNew(){
        return AgencyOrganizationService::ordersNew();
    }

    public static function ordersInWork(){
        return AgencyOrganizationService::ordersInWork();
    }

    public static function ordersCompleted(){
        return AgencyOrganizationService::ordersCompleted();
    }

    public static function orderComplete(OrderProduct $order){
        return AgencyOrganizationService::orderComplete($order);
    }

    public static function orderAccept(OrderProduct $order){
        return AgencyOrganizationService::orderAccept($order);
    }

    public static function payApplication(TypeService $type_service,Request $request){
        $data=request()->validate([
            'count'=>['required','integer'],
        ]);
        return AgencyOrganizationService::payApplication($type_service,$data['count']);
    }

    public static function pageBuyPriority(){
        return AgencyOrganizationService::pageBuyPriority();
    }

    public static function buyPriority(Request $request){
        $data=request()->validate([
            'type_priority'=>['required','integer'],
            'priority'=>['required','string'],
        ]);
        return AgencyOrganizationService::buyPriority($data);
    }

    public static function wallets(){
        return AgencyOrganizationService::wallets();
    }

    public static function walletDelete(Wallet $wallet){
        return AgencyOrganizationService::walletDelete($wallet);
    }

    public static function walletUpdateBalance(Request $request){
        $data=request()->validate([
            'wallet_id'=>['required','integer'],
            'count'=>['required','integer'],
        ]);

        return AgencyOrganizationService::walletUpdateBalance($data);
    }

    public function callStats(Request $request)
    {
        // Валидация входных параметров
        $validated = $request->validate([
            'period' => 'nullable',
            'date_from' => 'nullable|required_if:period,custom|date',
            'sort' => 'nullable|in:asc,desc',
            'date_to' => 'nullable|required_if:period,custom|date|after_or_equal:date_from'
        ]);

        return AgencyOrganizationService::callStats($validated,$request);
    }

    public static function sessions(){
        return AgencyOrganizationService::sessions();
    }
}
