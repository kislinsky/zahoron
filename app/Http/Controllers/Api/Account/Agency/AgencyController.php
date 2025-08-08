<?php

namespace App\Http\Controllers\Api\Account\Agency;

use App\Http\Controllers\Controller;
use App\Models\ActivityCategoryOrganization;
use App\Models\CategoryProduct;
use App\Models\City;
use App\Models\CommentProduct;
use App\Models\ImageOrganization;
use App\Models\ImageProduct;
use App\Models\Organization;
use App\Models\Product;
use App\Models\ProductRequestToSupplier;
use App\Models\RequestsCostProductsSupplier;
use App\Models\ReviewsOrganization;
use App\Models\TypeApplication;
use App\Models\TypeService;
use App\Models\User;
use App\Models\UserRequestsCount;
use App\Models\Wallet;
use App\Models\WorkingHoursOrganization;
use App\Services\YooMoneyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Contracts\Providers\Storage;

class AgencyController extends Controller
{
    public static function products(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'organization_id' => 'required|integer',
            'category' => 'nullable|string',
            'subcategory' => 'nullable|string',
            'search_text' => 'nullable|string',
            'limit' => 'nullable|integer|min:1|max:100',
            'page' => 'nullable|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 40);
        }

        $organization = Organization::find($request->organization_id);
        if (!$organization) {
            return response()->json([
                'success' => false,
                'message' => 'Organization not found'
            ], 404);
        }

        $query = $organization->products()
            ->with('category') // Предполагаем, что есть связь с категорией
            ->withCount('reviews'); // Предполагаем, что есть связь с отзывами

        // Поиск по тексту
        if ($request->filled('search_text')) {
            $query->where('title', 'like', '%' . $request->search_text . '%');
        }

        // Фильтр по категории
        if ($request->filled('category')) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('title', $request->category)
                  ->whereNull('parent_id'); // Категории верхнего уровня
            });
        }

        // Фильтр по подкатегории
        if ($request->filled('subcategory')) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('title', $request->subcategory)
                  ->whereNotNull('parent_id'); // Подкатегории
            });
        }

        // Пагинация
        $limit = $request->limit ?? 10  ;
        $products = $query->paginate($limit, ['*'], 'page', $request->page ?? 1);

        // Форматируем ответ согласно спецификации
        $response = [
            'current_category' => $request->category ?? null,
            'current_subcategory' => $request->subcategory ?? null,
            'products' => $products->map(function ($product) {
                return [
                    'name' => $product->title,
                    'price' => $product->total_price,
                    'features' => [
                        'size' => $product->size ?? '',
                        'material' => $product->material ?? '',
                        'color' => $product->color ?? '',
                    ],
                    'stars' => $product->rating ?? 5.0,
                    'reviews_count' => $product->reviews->count() ?? 0
                ];
            }),
            'pagination' => [
                'total' => $products->total(),
                'per_page' => $products->perPage(),
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $response
        ]);
    }

    public function addProduct(Request $request)
{
    
    $validator = Validator::make($request->all(), [
        'organization_id' => 'required|integer|exists:organizations,id',
        'title' => 'required|string|max:255',
        'content' => 'nullable|string',
        'price' => 'required|numeric|min:0',
        'price_sale' => 'nullable|numeric|min:0|max:100',
        'category_id' => 'required|integer|exists:category_products,id',
        'images' => 'nullable|array|max:5',
        'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,webp', // 5MB max per file
        'size' => 'nullable|string',
        'material' => 'nullable|string',
        'color' => 'nullable|string',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Validation errors',
            'errors' => $validator->errors()
        ], 400);
    }

        $organization = Organization::find($request->organization_id);
        if (!$organization) {
            return response()->json([
                'success' => false,
                'message' => 'Organization not found'
            ], 404);
        }

        // Создаем товар
        $product = new Product();
        $product->organization_id = $organization->id;
        $product->title = $request->title;
        $product->slug = slugCheckProduct($request->title);
        $product->content = $request->content;
        $product->price = $request->price;
        $product->category_id = $request->category_id;
        $product->size = $request->size;
        $product->material = $request->material;
        $product->color = $request->color;
        $product->total_price = $request->price;
        if($request->price_sale!=null){
            $product->total_price = $request->price_sale;
        }
        // Обработка изображений
        
        $product->save();

        if ($request->hasFile('images') && count($request->images) > 0) {
            foreach($request->images as $image){
                $filename=generateRandomString().".jpeg";
                $image->storeAs("uploads_product", $filename, "public");
                ImageProduct::create([
                    'title'=>'uploads_product/'.$filename,
                    'product_id'=>$product->id,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Product added successfully',
            'data' => $product
        ], 201);
    }

    // Обновление товара (без удаления файлов из папки)
    public function updateProduct(Request $request, $productId)
    {
        $validator = Validator::make($request->all(), [
            'organization_id' => 'required|integer|exists:organizations,id',
            'title' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'price_sale' => 'nullable|numeric|min:0',
            'category_id' => 'nullable|integer|exists:category_products,id',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif',
            'size' => 'nullable|string',
            'material' => 'nullable|string',
            'color' => 'nullable|string',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 400);
        }
    
        $product = Product::find($productId);
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }
    
        // Проверка владельца товара
        if ($product->organization_id != $request->organization_id) {
            return response()->json([
                'success' => false,
                'message' => 'Product does not belong to this organization'
            ], 403);
        }
    
        // Обновляем только переданные поля
        $fields = ['title', 'content', 'price', 'price_sale', 'category_id', 'size', 'material', 'color'];
        foreach ($fields as $field) {
            if ($request->has($field)) {
                $product->$field = $request->$field;
            }
        }
    
        // Пересчет итоговой цены
        if ($request->has('price') || $request->has('price_sale')) {
            $product->total_price = $request->price_sale ?? $request->price ?? $product->price;
        }
    
        // Обновление slug если изменился title
        if ($request->has('title')) {
            $product->slug = slugCheckProduct($request->title);
        }
    
        // Обновление изображений
        if ($request->hasFile('images')) {
            $product->images()->delete();
            foreach ($request->file('images') as $image) {
                $filename = generateRandomString().".jpeg";
                $image->storeAs("uploads_product", $filename, "public");
                ImageProduct::create([
                    'title' => 'uploads_product/'.$filename,
                    'product_id' => $product->id,
                ]);
            }
        }
    
        $product->save();
    
        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'data' => $product
        ]);
    }

    // Удаление товара (без удаления файлов из папки)
    public function deleteProduct(Request $request, $productId)
    {
        $validator = Validator::make($request->all(), [
            'organization_id' => 'required|integer|exists:organizations,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 400);
        }

        $product = Product::find($productId);
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        // Проверка, что товар принадлежит организации
        if ($product->organization_id != $request->organization_id) {
            return response()->json([
                'success' => false,
                'message' => 'Product does not belong to this organization'
            ], 403);
        }

        // Удаляем товар (файлы изображений остаются на диске)
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully'
        ]);
    }

    public static function getLinksOrganization(){

    }


    public static function settingsUserUpdate(Request $request)
    {
        
        // Общие правила валидации
        $commonRules = [
            'user_id'=>'required|integer',
            'phone' => 'required|string',
            'address' => 'string|nullable',
            'email' => 'email|nullable',
            'whatsapp' => 'string|nullable',
            'telegram' => 'string|nullable',
            'password' => 'nullable|string|min:8',
            'password_new' => 'nullable|string|min:8',
            'password_new_2' => 'nullable|string|min:8',
            'email_notifications' => 'nullable|boolean',
            'sms_notifications' => 'nullable|boolean',
            'language' => 'nullable|integer',
            'theme' => 'nullable|string',
            'inn' => 'required|string',
            'name_organization' => 'nullable|string',
            'city_id' => 'required|integer',
            'edge_id' => 'required|integer',
        ];

        // Правила для ИП
        $epRules = [
            'name' => 'string|nullable',
            'surname' => 'string|nullable',
            'patronymic' => 'string|nullable',
            'ogrnip' => 'nullable|string',
        ];

        // Правила для организаций
        $orgRules = [
            'in_face' => 'required|string',
            'regulation' => 'required|string',
            'ogrn' => 'nullable|string',
            'kpp' => 'nullable|string',

        ];

        $user=null;
        if($request->user_id!=null){
            $user = User::find($request->user_id);
            if ($user==null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Несуществующий или недействующий пользователь'
                ], 400);
            }
            if ($user->organizational_form==null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Пользователь не является организацией'
                ], 400);
            }
        }
       
        // Объединяем правила в зависимости от типа организации
        $validationRules = $user->organizational_form == 'ep' 
            ? array_merge($commonRules, $epRules) 
            : array_merge($commonRules, $orgRules);

        $validator = Validator::make($request->all(), $validationRules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();


       

        // Проверка организации через API, если включено
        if (env('API_WORK') == 'true') {
            $organizationData = checkOrganizationInn($data['inn']);
            
            if (!$organizationData || $organizationData['state']['status'] != 'ACTIVE') {
                return response()->json([
                    'success' => false,
                    'message' => 'Несуществующий или недействующий ИНН'
                ], 400);
            }

            // Обновляем данные из API
            $data['name'] = $organizationData['fio']['name'] ?? $data['name'] ?? null;
            $data['surname'] = $organizationData['fio']['surname'] ?? $data['surname'] ?? null;
            $data['patronymic'] = $organizationData['fio']['patronymic'] ?? $data['patronymic'] ?? null;
            $data['ogrn'] = $organizationData['ogrn'] ?? $data['ogrn'] ?? null;
        }

        // Проверка уникальности email и телефона
        $emailExists = User::where('email', $data['email'])
            ->where('id', '!=', $user->id)
            ->exists();

        $phoneExists = User::where('phone', $data['phone'])
            ->where('id', '!=', $user->id)
            ->exists();

        if ($emailExists || $phoneExists) {
            return response()->json([
                'success' => false,
                'message' => 'Такой телефон или email уже существует'
            ], 400);
        }

        // Подготовка данных для обновления
        $updateData = [
            'phone' => $data['phone'],
            'address' => $data['address'] ?? null,
            'email' => $data['email'],
            'whatsapp' => $data['whatsapp'] ?? null,
            'telegram' => $data['telegram'] ?? null,
            'language' => $data['language'] ?? null,
            'theme' => $data['theme'] ?? null,
            'inn' => $data['inn'],
            'name_organization' => $data['name_organization'] ?? null,
            'city_id' => $data['city_id'],
            'edge_id' => $data['edge_id'],
            'email_notifications' => $data['email_notifications'] ?? false,
            'sms_notifications' => $data['sms_notifications'] ?? false,
        ];

        // Добавляем специфичные поля для ИП
        if ($user->organizational_form == 'ep') {
            $updateData['name'] = $data['name'] ?? null;
            $updateData['surname'] = $data['surname'] ?? null;
            $updateData['patronymic'] = $data['patronymic'] ?? null;
            $updateData['ogrnip'] = $data['ogrnip'] ?? null;
            
        } else {
            $updateData['in_face'] = $data['in_face'];
            $updateData['regulation'] = $data['regulation'];
            $updateData['ogrn'] = $data['ogrn'] ?? null;
            $updateData['kpp'] = $data['kpp'] ?? null;

        }

        // Обновление пароля
        if (!empty($data['password']) && !empty($data['password_new'])) {
            if (!Hash::check($data['password'], $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Неверный текущий пароль'
                ], 400);
            }

            if ($data['password_new'] !== $data['password_new_2']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Новые пароли не совпадают'
                ], 400);
            }

            $updateData['password'] = Hash::make($data['password_new']);
        }

        // Обновление пользователя
        try {
            $user->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Настройки успешно обновлены',
                'data' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при обновлении настроек: ' . $e->getMessage()
            ], 500);
        }
    }


    public static function createOrganization(Request $request) 
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'cemetery_ids' => 'required|array',
            'cemetery_ids.*' => 'integer',
            'phone' => 'nullable|string|max:20',
            'telegram' => 'nullable|string|max:50',
            'whatsapp' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'city_id' => 'required|integer|exists:cities,id',
            'next_to' => 'nullable|string|max:255',
            'underground' => 'nullable|string|max:255',
            'adres' => 'required|string|max:255',
            'width' => 'required|numeric',
            'longitude' => 'required|numeric',
            'available_installments' => 'nullable|boolean',
            'found_cheaper' => 'nullable|boolean',
            'сonclusion_contract' => 'nullable|boolean',
            'state_compensation' => 'nullable|boolean',
            'user_id' => 'required|integer|exists:users,id',
            
            // Categories and prices
            'categories_organization' => 'nullable|array',
            'categories_organization.*' => 'integer|exists:category_products,id',
            'price_cats_organization' => 'nullable|array',
            'price_cats_organization.*' => 'numeric',
            
            // Working hours
            'working_day' => 'nullable|array',
            'working_day.*' => 'nullable|string',
            'holiday_day' => 'nullable|array',
            'holiday_day.*' => 'string|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            
            // Images
            'img' => 'required|mimes:jpeg,jpg,png|max:5048',
            'img_main' => 'required|mimes:jpeg,jpg,png|max:5048',
            'images' => 'nullable|array|max:5',
            'images.*' => 'nullable|image|mimes:jpeg,jpg,png|max:5120',
        ]);
       

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }
      
        try {
            DB::beginTransaction();
            
            $data = $validator->validated();
            
           

            // Process cemetery_ids
            $data['cemetery_ids'] = implode(",", $data['cemetery_ids']) . ',';
    
            // Handle file uploads
            $filename = Str::random(40) . '.jpeg';
            $data['img']->storeAs("uploads_organization", $filename, "public");
            
            $filename_main = Str::random(40) . '.jpeg';
            $data['img_main']->storeAs("uploads_organization", $filename_main, "public");
    
            // Create organization
            $organization = Organization::create([
                'title' => $data['title'],
                'status' => 0,
                'content' => $data['content'],
                'cemetery_ids' => $data['cemetery_ids'],
                'phone' => normalizePhone($data['phone']) ?? null,
                'telegram' => $data['telegram'] ?? null,
                'user_id' => $data['user_id'],
                'img_file' => 'uploads_organization/' . $filename,
                'img_main_file' => 'uploads_organization/' . $filename_main,
                'whatsapp' => $data['whatsapp'] ?? null,
                'email' => $data['email'] ?? null,
                'city_id' => $data['city_id'],
                'slug' => slugOrganization($data['title']),
                'next_to' => $data['next_to'] ?? null,
                'underground' => $data['underground'] ?? null,
                'adres' => $data['adres'],
                'width' => $data['width'],
                'longitude' => $data['longitude'],
                'available_installments' => $data['available_installments'] ?? 0,
                'found_cheaper' => $data['found_cheaper'] ?? 0,
                'state_compensation' => $data['state_compensation'] ?? 0,
                'conclusion_contract' => $data['conclusion_contract'] ?? 0,
            ]);
    

           
            
            // Create category relationships
            if (!empty($data['categories_organization']) && !empty($data['price_cats_organization'])) {
                $categoryData = [];
                
                foreach ($data['categories_organization'] as $key => $categoryId) {
                    $cat = CategoryProduct::find($categoryId);
                    if ($cat) {
                        $categoryData[] = [
                            'organization_id' => $organization->id,
                            'category_main_id' => $cat->parent_id,
                            'category_children_id' => $cat->id,
                            'rating' => $organization->rating,
                            'price' => $data['price_cats_organization'][$key],
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                   
                }
                
                if (!empty($categoryData)) {
                    ActivityCategoryOrganization::insert($categoryData);
                }
            }
    
           // Process working hours
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $workingHoursData = [];

        foreach ($days as $key => $day) {
            $isHoliday = in_array($day, $data['holiday_day'] ?? []);
            
            if ($isHoliday) {
                $workingHoursData[] = [
                    'day' => $day,
                    'holiday' => 1,
                    'time_start_work' => null,
                    'time_end_work' => null,
                    'organization_id' => $organization->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            } else {
                
                // Берем соответствующий день из массива
                $timeRange = $data['working_day'][$key] ?? null;
                
                // Разбиваем диапазон времени с учетом пробелов вокруг дефиса
                $times = $timeRange ? array_map('trim', explode(' - ', $timeRange)) : [null, null];
               
                // Проверяем, что получили 2 значения времени
                if (count($times) === 2) {
                    $startTime = trim($times[0]);
                    $endTime = trim($times[1]);
                } else {
                    $startTime = null;
                    $endTime = null;
                }
                
                $workingHoursData[] = [
                    'day' => $day,
                    'holiday' => 0,
                    'time_start_work' => $startTime,
                    'time_end_work' => $endTime,
                    'organization_id' => $organization->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        WorkingHoursOrganization::insert($workingHoursData);
    
            // Handle additional images
            if ($request->hasFile('images') && count($request->images) > 0) {
                $imagesData = [];
                
                foreach ($data['images'] as $image) {
                    if($image!=null){
                        $filename=generateRandomString().".jpeg";
                        $image->storeAs("uploads_organization", $filename, "public");
                        
                        $imagesData[] = [
                            'img_file' => 'uploads_organization/' . $filename,
                            'href_img' => 0,
                            'organization_id' => $organization->id,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                    
                }
                
                ImageOrganization::insert($imagesData);
            }
    
            DB::commit();
    
            return response()->json([
                'success' => true,
                'message' => 'Organization submitted for moderation',
                'data' => $organization->load(['activityCategories', 'workingHours', 'images'])
            ], 201);
    
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error creating organization',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    public static function updateOrganization(Request $request) {
        try {
            // Валидация входных данных
            $data = $request->validate([
                'images' => ['nullable', 'array'],
                'images.*' => ['nullable'],
                'img' => ['nullable', 'file', 'max:2048'],
                'img_main' => ['nullable', 'file', 'max:2048'],
                'cemetery_ids' => ['nullable', 'array'],
                'cemetery_ids.*' => ['nullable', 'integer'],
                'id' => ['required', 'integer'],
                'title' => ['required', 'string', 'max:255'],
                'content' => ['nullable', 'string'],
                'phone' => ['nullable', 'string', 'max:20'],
                'telegram' => ['nullable', 'string', 'max:50'],
                'whatsapp' => ['nullable', 'string', 'max:20'],
                'email' => ['nullable', 'string', 'email', 'max:255'],
                'city_id' => ['required', 'integer'],
                'next_to' => ['nullable', 'string', 'max:255'],
                'underground' => ['nullable', 'string', 'max:255'],
                'adres' => ['required', 'string', 'max:255'],
                'width' => ['required', 'string', 'max:50'],
                'longitude' => ['required', 'string', 'max:50'],
                'categories_organization' => ['nullable', 'array'],
                'price_cats_organization' => ['nullable', 'array'],
                'working_day' => ['nullable', 'array'],
                'holiday_day' => ['nullable', 'array'],
                'available_installments' => ['nullable', 'boolean'],
                'found_cheaper' => ['nullable', 'boolean'],
                'conclusion_contract' => ['nullable', 'boolean'],
                'state_compensation' => ['nullable', 'boolean'],
            ]);
    
            // Поиск организации
            $organization = Organization::findOrFail($data['id']);
    
            // Обновление основных данных
            $cemeteries = isset($data['cemetery_ids']) ? implode(",", $data['cemetery_ids']) . ',' : '';
            
            $organization->update([
                'title' => $data['title'],
                'slug'=> slugOrganization($data['title']),
                'content' => $data['content'] ?? null,
                'cemetery_ids' => $cemeteries,
                'phone' => $data['phone'] ?? null,
                'telegram' => $data['telegram'] ?? null,
                'whatsapp' => $data['whatsapp'] ?? null,
                'email' => $data['email'] ?? null,
                'city_id' => $data['city_id'],
                'next_to' => $data['next_to'] ?? null,
                'underground' => $data['underground'] ?? null,
                'adres' => $data['adres'],
                'width' => $data['width'],
                'longitude' => $data['longitude'],
                'available_installments' => $data['available_installments'] ?? false,
                'found_cheaper' => $data['found_cheaper'] ?? false,
                'conclusion_contract' => $data['conclusion_contract'] ?? false,
                'state_compensation' => $data['state_compensation'] ?? false,
            ]);
            
    
            // Обработка основного изображения
            if ($request->hasFile('img')) {
                $filename = generateRandomString() . ".jpeg";
                $request->file('img')->storeAs("uploads_organization", $filename, "public");
                $organization->update([
                    'img_file' => 'uploads_organization/' . $filename,
                    'href_img' => 0,
                ]);
            }
    
            // Обработка главного изображения
            if ($request->hasFile('img_main')) {
                $filename_main = generateRandomString() . ".jpeg";
                $request->file('img_main')->storeAs("uploads_organization", $filename_main, "public");
                $organization->update([
                    'img_main_file' => 'uploads_organization/' . $filename_main,
                    'href_main_img' => 0,
                ]);
            }
    
            // Обновление категорий организации
            ActivityCategoryOrganization::where('organization_id', $organization->id)->delete();
            if (isset($data['categories_organization']) && count($data['categories_organization'])>0) {
                foreach ($data['categories_organization'] as $key => $category_organization) {
                    $cat = CategoryProduct::find($category_organization);
                    if($cat!=null){
                        ActivityCategoryOrganization::create([
                            'organization_id' => $organization->id,
                            'category_main_id' => $cat->parent_id,
                            'category_children_id' => $cat->id,
                            'rating' => $organization->rating,
                            'price' => $data['price_cats_organization'][$key] ?? null,
                        ]);
                    }
                }
            }
    
            // Обновление рабочих часов
            WorkingHoursOrganization::where('organization_id', $organization->id)->delete();
            $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
            $holidays = $data['holiday_day'] ?? [];
            $working_days = $data['working_day'] ?? [];
    
            foreach ($days as $key => $day) {
                if (in_array($day, $holidays)) {
                    WorkingHoursOrganization::create([
                        'day' => $day,
                        'holiday' => 1,
                        'organization_id' => $organization->id,
                    ]);
                } elseif (isset($working_days[$key])) {
                    $times = explode(' - ', $working_days[$key]);
                    if (count($times) === 2) {
                        WorkingHoursOrganization::create([
                            'day' => $day,
                            'time_start_work' => $times[0],
                            'time_end_work' => $times[1],
                            'organization_id' => $organization->id,
                        ]);
                    }
                }
            }
    
            // Обработка изображений
            if (isset($data['images']) && count($data['images'])>0) {
                // Удаляем старые изображения
                $organization->images()->delete();
    
                foreach ($data['images'] as $image) {
                    if ($image instanceof \Illuminate\Http\UploadedFile) {
                        $filename = uniqid() . '.' . $image->getClientOriginalExtension();
                        $path = $image->storeAs('uploads_organization', $filename, 'public');
                        
                        ImageOrganization::create([
                            'img_file' => $path,
                            'href_img' => 0,
                            'organization_id' => $organization->id,
                        ]);
                    } elseif (filter_var($image, FILTER_VALIDATE_URL)) {
                        ImageOrganization::create([
                            'img_url' => $image,
                            'href_img' => 1,
                            'organization_id' => $organization->id,
                        ]);
                    } elseif (str_starts_with($image, 'data:image')) {
                        $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $image));
                        $filename = uniqid() . '.jpg';
                        $path = 'uploads_organization/' . $filename;
                        file_put_contents(public_path('storage/' . $path), $imageData);
                        
                        ImageOrganization::create([
                            'img_file' => $path,
                            'href_img' => 0,
                            'organization_id' => $organization->id,
                        ]);
                    }
                }
            }
    
            return response()->json([
                'success' => true,
                'message' => 'Организация успешно обновлена',
                'data' => $organization->fresh()->load('images', 'workingHours', 'activityCategories')
            ]);
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка валидации',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка сервера: ' . $e->getMessage()
            ], 500);
        }
    }



 public function addRequestsCostProductSuppliers(Request $request)
    {
        // Упрощенная валидация
        $validated = $request->validate([
            'organization_id' => 'required|integer',
            'user_id' => 'required|integer',
            'products' => 'required|array|min:1',
            'products.*' => 'integer',
            'count' => 'required|array|min:1',
            'count.*' => 'integer|min:1',
            'lcs' => 'nullable|array',
            'lcs.*' => 'string',
            'all_lcs' => 'nullable|boolean'
        ]);

        // Проверка соответствия массивов
        if (count($validated['products']) !== count($validated['count'])) {
            return response()->json([
                'message' => 'Количество продуктов и значений count должно совпадать'
            ], 422);
        }

        // Проверка организации
        $organization = Organization::find($validated['organization_id']);
        if (!$organization || $organization->user_id != $validated['user_id']) {
            return response()->json([
                'message' => 'Организация не найдена или не принадлежит пользователю'
            ], 422);
        }

        // Формирование данных
        $products = array_map(function($product, $count) {
            return [(int)$product, (int)$count, 0];
        }, $validated['products'], $validated['count']);

        // Обработка транспортных компаний
        $transportCompanies = ($validated['all_lcs'] ?? false) ? 'all' : ($validated['lcs'] ?? []);

        try {
            $requestCost = RequestsCostProductsSupplier::create([
                'organization_id' => $validated['organization_id'],
                'products' => json_encode($products),
                'transport_companies' => json_encode($transportCompanies),
                'categories_provider_product' => json_encode($validated['products']),
            ]);

            return response()->json($requestCost, 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ошибка при создании заявки',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteRequestCostProductProvider($request)
    {
        $aplication=RequestsCostProductsSupplier::findOrFail($request)->delete();

         return response()->json([
                'success' => true,
                'message' => 'Заявка успешно удалена.'
            ], 200);

    }

     public function createProviderOffer(Request $request)
    {
        // Validate request data
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'images' => ['required', 'array', 'max:5'],
            'images.*' => [
                'required',
                'image',
                'mimes:jpeg,jpg,png,gif,svg,webp',
                'max:2048'
            ],
            'category_id' => ['nullable', 'integer'],
            'delivery_required' => ['nullable', 'integer'],
        ]);


        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Get authenticated user's organization
            $organization = auth()->user()->organization;
            
            if (!$organization) {
                return response()->json([
                    'success' => false,
                    'message' => 'Organization not found for this user'
                ], 404);
            }

            // Process and store images
            $images = [];
            if (count($request->file('images')) > 0 && count($request->file('images')) < 6) {
                foreach ($request->file('images') as $image) {
                    $filename = Str::random(40) . '.jpeg'; // Using Str::random instead of generateRandomString()
                    $image->storeAs('uploads_product', $filename, 'public');
                    $images[] = $filename;
                }
                $imagePaths = json_encode($images);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'You must upload between 1 and 5 images'
                ], 422);
            }

            // Create the offer
            $offer = ProductRequestToSupplier::create([
                'title' => $request->title,
                'content' => $request->content,
                'organization_id' => $organization->id,
                'images' => json_encode($imagePaths),
                'category_id' => $request->category_id,
                'delivery_required' => $request->boolean('delivery_required'),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Offer created successfully',
                'data' => $offer
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create offer',
                'error' => $e->getMessage()
            ], 500);
        }
    }


   public static function deleteProviderOffer($id)
    {
        $offer = ProductRequestToSupplier::find($id);
        
        if (!$offer) {
            return response()->json([
                'success' => false,
                'message' => 'Offer not found'
            ], 404);
        }
        
        $offer->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Product request to supplier deleted successfully.'
        ], 201);
    }


    public function getOrganizationReviews($organizationId)
    {
        // Валидация ID организации (должно быть числом)
        if (!is_numeric($organizationId)) {
            return response()->json([
                'success' => false,
                'message' => 'Некорректный ID организации'
            ], 400);
        }

        // Проверяем, существует ли организация
        $organization = Organization::find($organizationId);
        if (!$organization) {
            return response()->json([
                'success' => false,
                'message' => 'Организация не найдена'
            ], 404);
        }

        // Получаем отзывы
        $reviews = ReviewsOrganization::where('organization_id', $organizationId)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $reviews
        ]);
    }
    
    
    public function getProductComments($organizationId)
    {
        // Валидация ID организации
        if (!is_numeric($organizationId)) {
            return response()->json([
                'success' => false,
                'message' => 'Некорректный ID организации'
            ], 400);
        }

        // Проверяем, существует ли организация
        $organization = Organization::find($organizationId);
        if (!$organization) {
            return response()->json([
                'success' => false,
                'message' => 'Организация не найдена'
            ], 404);
        }

        // Получаем комментарии к товарам организации
        $comments = CommentProduct::whereHas('product', function($query) use ($organizationId) {
                $query->where('organization_id', $organizationId);
            })
            ->with(['product']) // Загружаем товар
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $comments
        ]);
    }


        /**
    * Удаление отзыва об организации
    */
    public function deleteReview($reviewId)
    {
        // Валидация ID отзыва
        if (!is_numeric($reviewId)) {
            return response()->json([
                'success' => false,
                'message' => 'Некорректный ID отзыва'
            ], 400);
        }

        // Поиск отзыва
        $review = ReviewsOrganization::find($reviewId);

        // Проверка существования отзыва
        if (!$review) {
            return response()->json([
                'success' => false,
                'message' => 'Отзыв не найден'
            ], 404);
        }

        // Удаление
        $review->delete();

        return response()->json([
            'success' => true,
            'message' => 'Отзыв успешно удален'
        ]);
    }


        /**
    * Одобрение отзыва (установка статуса 1)
    */
    public function approveReview($reviewId)
    {
        // Валидация ID
        if (!is_numeric($reviewId)) {
            return response()->json([
                'success' => false,
                'message' => 'Некорректный ID отзыва'
            ], 400);
        }

        // Поиск отзыва
        $review = ReviewsOrganization::find($reviewId);

        // Проверки
        if (!$review) {
            return response()->json([
                'success' => false,
                'message' => 'Отзыв не найден'
            ], 404);
        }


        // Обновление статуса
        $review->update(['status' => 1]);

        return response()->json([
            'success' => true,
            'message' => 'Отзыв одобрен',
            'data' => $review
        ]);
    }


        /**
    * Редактирование текста отзыва
    */
    public function updateReviewContent(Request $request, $reviewId)
    {
        // Валидация
        $validator = Validator::make($request->all(), [
            'content' => 'required|string|min:10|max:2000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Поиск отзыва
        $review = ReviewsOrganization::find($reviewId);

        // Проверки
        if (!$review) {
            return response()->json([
                'success' => false,
                'message' => 'Отзыв не найден'
            ], 404);
        }


        // Обновление
        $review->update([
            'content' => $request->content,
            'edited_at' => now() // метка редактирования
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Отзыв обновлен',
            'data' => $review
        ]);
    }





    /**
     * Удаление комментария о товаре
     */
    public function deleteProductComment($commentId)
    {
        // Валидация ID комментария
        if (!is_numeric($commentId)) {
            return response()->json([
                'success' => false,
                'message' => 'Некорректный ID комментария'
            ], 400);
        }

        // Поиск комментария
        $comment = CommentProduct::find($commentId);

        // Проверка существования
        if (!$comment) {
            return response()->json([
                'success' => false,
                'message' => 'Комментарий не найден'
            ], 404);
        }

        // Удаление
        $comment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Комментарий успешно удален'
        ]);
    }


        /**
     * Одобрение комментария о товаре
     */
    public function approveProductComment($commentId)
    {
        // Валидация ID
        if (!is_numeric($commentId)) {
            return response()->json([
                'success' => false,
                'message' => 'Некорректный ID комментария'
            ], 400);
        }

        $comment = CommentProduct::find($commentId);

        // Проверки
        if (!$comment) {
            return response()->json([
                'success' => false,
                'message' => 'Комментарий не найден'
            ], 404);
        }

        // Обновление статуса
        $comment->update(['status' => 1]);

        return response()->json([
            'success' => true,
            'message' => 'Комментарий одобрен',
            'data' => $comment
        ]);
    }


        /**
     * Обновление текста комментария о товаре
     */
    public function updateProductCommentContent(Request $request, $commentId)
    {
        // Валидация
        $validator = Validator::make($request->all(), [
            'content' => 'required|string|min:10|max:2000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Поиск комментария
        $comment = CommentProduct::find($commentId);

        // Проверки
        if (!$comment) {
            return response()->json([
                'success' => false,
                'message' => 'Комментарий не найден'
            ], 404);
        }

        // Обновление
        $comment->update([
            'content' => $request->content,
            'edited_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Комментарий обновлен',
            'data' => $comment
        ]);
    }


    public function addOrganizationReviewResponse(Request $request, $reviewId)
    {
        $validator = Validator::make($request->all(), [
            'response' => 'required|string|min:10|max:3000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $review = ReviewsOrganization::find($reviewId);
        
        if (!$review) {
            return response()->json([
                'success' => false,
                'message' => 'Отзыв не найден'
            ], 404);
        }

        $review->organization_response = $request->response;
        $review->save();

        return response()->json([
            'success' => true,
            'message' => 'Ответ организации сохранен',
            'data' => $review
        ]);
    }

    
    public function addProductCommentResponse(Request $request, $commentId)
    {
        $validator = Validator::make($request->all(), [
            'response' => 'required|string|min:10|max:3000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $comment = CommentProduct::find($commentId);
        
        if (!$comment) {
            return response()->json([
                'success' => false,
                'message' => 'Комментарий не найден'
            ], 404);
        }

        $comment->organization_response = $request->response;
        $comment->save();

        return response()->json([
            'success' => true,
            'message' => 'Ответ организации сохранен',
            'data' => $comment
        ]);
    }

    public static function organizationsCity(City $city){
        $organizations=$city->organizations;
        return response()->json([
            'success' => true,
            'message' => 'Организации успешно найдены',
            'organizations' => $organizations,
            'city' => $city,
        ]);
    }

    public static function citySearch(Request $request){
        $validator = Validator::make($request->all(), [
            'city' => 'required|string|max:3000'
        ]);

        $cities = DB::table('cities')
        ->select('cities.*')
        ->join('organizations', 'organizations.city_id', '=', 'cities.id')
        ->join('areas', 'cities.area_id', '=', 'areas.id')
        ->join('edges', 'areas.edge_id', '=', 'edges.id')
        ->where('cities.title', 'like', $request->city . '%') // Используем начало строки для индекса
        ->where('edges.is_show', 1)
        ->groupBy('cities.id')
        ->orderBy('cities.title', 'asc')
        ->get();

        return response()->json([
            'success' => true,
            'message' => 'Города успешно найдены',
            'cities' => $cities,
        ]);
    }

    public function sendCode(Request $request)
    {
        $request->validate([
            'organization_id' => 'required|exists:organizations,id',
        ]);

        $organization = Organization::find($request->organization_id);
        $code = generateRandomNumber(); // Генерация кода (например, 4-6 цифр)

        // Отправка кода (первый способ)
        $sendCodeResult = sendCode($organization->phone, $code);

        // Если первый способ не сработал, пробуем SMS
        if ($sendCodeResult['tell_code_result']['status'] != 'ok') {
            sendSms($organization->phone, $code);
        }

        // Сохраняем хеш кода в кеш (вместо куки) на 20 минут
        $cacheKey = "verification_code:{$organization->id}";
        Cache::put($cacheKey, Hash::make($code), now()->addMinutes(20));

        return response()->json([
            'success' => true,
            'message' => 'Код отправлен',
        ]);
    }


    public function acceptCode(Request $request)
    {
        $request->validate([
            'organization_id' => 'required|exists:organizations,id',
            'code' => 'required|string|min:4|max:6',
        ]);

        $cacheKey = "verification_code:{$request->organization_id}";
        $hashedCode = Cache::get($cacheKey);

        // Если код не найден или неверный
        if (!$hashedCode || !Hash::check($request->code, $hashedCode)) {
            return response()->json([
                'success' => false,
                'message' => 'Неверный или устаревший код',
            ], 422);
        }

        // Обновляем организацию
        Organization::find($request->organization_id)
            ->update(['user_id' => auth()->id()]);

        // Удаляем код из кеша после успешной проверки
        Cache::forget($cacheKey);

        return response()->json([
            'success' => true,
            'message' => 'Код подтвержден, организация привязана',
        ]);
    }


    public static function userWallets(){
        $wallets = auth()->user()->wallets; 
    
        return response()->json([
            'success' => true,
            'message' => 'Кошельки успешно найдены',
            'wallets' => $wallets
        ]);
    }
    
    public static function deleteWallet(Wallet $wallet){
        $wallet->delete();
        return response()->json([
            'success' => true,
            'message' => 'Кошелек успешно удален',
        ]);
    }
    

    public static function walletUpdateBalance(Request $request)
    {
        $data = $request->validate([
            'wallet_id' => 'required|exists:wallets,id',
            'count' => 'required|integer|min:1',
            'deep_link' => 'required|url' // Ссылка для возврата в приложение
        ]);

        $service = new YooMoneyService();
        $metadata = [
            'wallet_id' => $data['wallet_id'],
            'count' => $data['count'],
            'deep_link' => $data['deep_link']
        ];

        try {
            $payment = $service->createMobilePayment(
                $data['count'],
                $data['deep_link'],
                'Пополнение баланса',
                $metadata
            );

            return response()->json([
                'success' => true,
                'payment_url' => $payment['confirmation_url'],
                'payment_id' => $payment['id']
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public static function getApplicationsForBuy()
    {
        // Получаем активные типы заявок, доступные для покупки организациями
        $applicationTypes = TypeApplication::where('buy_for_organization', 1)
            ->with(['services' => function($query) {
                $query->where('is_show', 1)
                    ->select('id', 'type_application_id', 'title', 'title_ru');
            }])
            ->select('id', 'title', 'title_ru')
            ->get();

        $userId = auth()->id();
        $result = [];

        foreach ($applicationTypes as $appType) {
            $typeData = [
                'id' => $appType->id,
                'title' => $appType->title,
                'title_ru' => $appType->title_ru,
                'services' => []
            ];

            foreach ($appType->services as $service) {
                // Проверяем купленные заявки пользователя
                $purchased = UserRequestsCount::where('user_id', $userId)
                    ->where('type_service_id', $service->id)
                    ->first();

                $typeData['services'][] = [
                    'id' => $service->id,
                    'title' => $service->title,
                    'title_ru' => $service->title_ru,
                    'purchased_count' => $purchased ? $purchased->count : 0,
                    'last_purchased_at' => $purchased ? $purchased->created_at->toDateTimeString() : null
                ];
            }

            if (count($typeData['services']) > 0) {
                $result[] = $typeData;
            }
        }

        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }


    public static function payApplication(Request $request)
    {
        $data = $request->validate([
            'count' => ['required', 'integer', 'min:1'],
            'type_service_id' => ['required', 'exists:type_services,id']
        ]);

        $typeService = TypeService::findOrFail($data['type_service_id']);
        $user = auth()->user();
        $price = $typeService->price * $data['count'];
        $description = "Покупка заявок {$typeService->title_ru}";

        // Проверка и списание баланса
        if (!$user->currentWallet()->balanceCanBeReduced($price)) {
            return response()->json([
                'success' => false,
                'message' => 'Недостаточно средств на балансе'
            ], 402);
        }

        DB::transaction(function () use ($user, $typeService, $data, $price, $description) {
            // Списание средств
            $balance = $user->currentWallet()->withdraw($price, [], $description);
            
            // Обновление или создание счетчика заявок
            UserRequestsCount::updateOrCreate(
                [
                    'organization_id' => $user->organization()->id,
                    'type_service_id' => $typeService->id
                ],
                [
                    'count' => DB::raw("count + {$data['count']}"),
                    'type_application_id' => $typeService->type_application_id
                ]
            );
        });

        return response()->json([
            'success' => true,
            'message' => 'Заявки успешно приобретены',
            'balance' => $user->currentWallet()->fresh()->balance,
            'purchased_count' => $data['count']
        ]);
    }


    public static function buyPriority(Request $request)
    {
        $validated = $request->validate([
            'type_priority' => 'required|string|in:1',
            'priority' => 'required|string|in:priority-list-companies-1-3,priority-list-companies-4-6'
        ]);

        $user = auth()->user();
        $organization = $user->organization();
        $typeService = getTypeService($validated['priority']);
        $price = $typeService->price;
        
        // Проверка баланса
        if (!$user->currentWallet()->hasSufficientBalance($price)) {
            return response()->json([
                'success' => false,
                'message' => 'Недостаточно средств на балансе'
            ], 402);
        }

        // Определение уровня приоритета
        $priorityLevel = match($validated['priority']) {
            'priority-list-companies-1-3' => 1,
            'priority-list-companies-4-6' => 2,
            default => 0
        };

        DB::transaction(function () use ($user, $organization, $price, $priorityLevel) {
            // Списание средств
            $user->currentWallet()->withdraw($price, [], 'Покупка приоритета организации');
            
            // Обновление приоритета
            $organization->update(['priority' => $priorityLevel]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Приоритет организации успешно обновлен',
            'new_priority' => $priorityLevel,
            'balance' => $user->currentWallet()->fresh()->balance
        ]);
    }
    
}






