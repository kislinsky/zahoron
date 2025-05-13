<?php

namespace App\Http\Controllers\Api\Account\Agency;

use App\Http\Controllers\Controller;
use App\Models\ActivityCategoryOrganization;
use App\Models\CategoryProduct;
use App\Models\ImageOrganization;
use App\Models\ImageProduct;
use App\Models\Organization;
use App\Models\Product;
use App\Models\RequestsCostProductsSupplier;
use App\Models\User;
use App\Models\WorkingHoursOrganization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

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
            'ogrn' => 'nullable|string',
        ];

        // Правила для организаций
        $orgRules = [
            'in_face' => 'required|string',
            'regulation' => 'required|string',
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
            $updateData['ogrn'] = $data['ogrn'] ?? null;
        } else {
            $updateData['in_face'] = $data['in_face'];
            $updateData['regulation'] = $data['regulation'];
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
}






