<?php

namespace App\Http\Controllers\Api\Account\Agency;

use App\Http\Controllers\Controller;
use App\Models\ActivityCategoryOrganization;
use App\Models\CallStat;
use App\Models\CategoryProduct;
use App\Models\Cemetery;
use App\Models\City;
use App\Models\CommentProduct;
use App\Models\ImageOrganization;
use App\Models\ImageProduct;
use App\Models\OrderProduct;
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
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AgencyController extends Controller
{
    public static function products(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'organization_id' => 'required|integer',
            'category'        => 'nullable|string',
            'subcategory'     => 'nullable|string',
            'search_text'     => 'nullable|string',
            'limit'           => 'nullable|integer|min:1|max:100',
            'page'            => 'nullable|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors'  => $validator->errors()
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
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('title', $request->category)
                    ->whereNull('parent_id'); // Категории верхнего уровня
            });
        }

        // Фильтр по подкатегории
        if ($request->filled('subcategory')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('title', $request->subcategory)
                    ->whereNotNull('parent_id'); // Подкатегории
            });
        }

        // Пагинация
        $limit = $request->limit ?? 10;
        $products = $query->paginate($limit, ['*'], 'page', $request->page ?? 1);

        // Форматируем ответ согласно спецификации
        $response = [
            'current_category'    => $request->category ?? null,
            'current_subcategory' => $request->subcategory ?? null,
            'products'            => $products->map(function ($product) {
                return [
                    'name'          => $product->title,
                    'price'         => $product->total_price,
                    'features'      => [
                        'size'     => $product->size ?? '',
                        'material' => $product->material ?? '',
                        'color'    => $product->color ?? '',
                    ],
                    'stars'         => $product->rating ?? 5.0,
                    'reviews_count' => $product->reviews->count() ?? 0
                ];
            }),
            'pagination'          => [
                'total'        => $products->total(),
                'per_page'     => $products->perPage(),
                'current_page' => $products->currentPage(),
                'last_page'    => $products->lastPage(),
            ]
        ];

        return response()->json([
            'success' => true,
            'data'    => $response
        ]);
    }

    public function addProduct(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'organization_id' => 'required|integer|exists:organizations,id',
            'title'           => 'required|string|max:255',
            'content'         => 'nullable|string',
            'price'           => 'required|numeric|min:0',
            'price_sale'      => 'nullable|numeric|min:0|max:100',
            'category_id'     => 'required|integer|exists:category_products,id',
            'images'          => 'nullable|array|max:5',
            'images.*'        => 'required|image|mimes:jpeg,png,jpg,gif,webp', // 5MB max per file
            'size'            => 'nullable|string',
            'material'        => 'nullable|string',
            'color'           => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors'  => $validator->errors()
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
        if ($request->price_sale != null) {
            $product->total_price = $request->price_sale;
        }
        // Обработка изображений

        $product->save();

        if ($request->hasFile('images') && count($request->images) > 0) {
            foreach ($request->images as $image) {
                $filename = generateRandomString() . ".jpeg";
                $image->storeAs("uploads_product", $filename, "public");
                ImageProduct::create([
                    'title'      => 'uploads_product/' . $filename,
                    'product_id' => $product->id,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Product added successfully',
            'data'    => $product
        ], 201);
    }

    // Обновление товара (без удаления файлов из папки)
    public function updateProduct(Request $request, $productId)
    {
        $validator = Validator::make($request->all(), [
            'organization_id' => 'required|integer|exists:organizations,id',
            'title'           => 'nullable|string|max:255',
            'content'         => 'nullable|string',
            'price'           => 'nullable|numeric|min:0',
            'price_sale'      => 'nullable|numeric|min:0',
            'category_id'     => 'nullable|integer|exists:category_products,id',
            'images'          => 'nullable|array|max:5',
            'images.*'        => 'image|mimes:jpeg,png,jpg,gif',
            'size'            => 'nullable|string',
            'material'        => 'nullable|string',
            'color'           => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors'  => $validator->errors()
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
                $filename = generateRandomString() . ".jpeg";
                $image->storeAs("uploads_product", $filename, "public");
                ImageProduct::create([
                    'title'      => 'uploads_product/' . $filename,
                    'product_id' => $product->id,
                ]);
            }
        }

        $product->save();

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'data'    => $product
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
                'errors'  => $validator->errors()
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

    public static function getLinksOrganization()
    {

    }


    public static function settingsUserUpdate(Request $request)
    {
        // Общие правила валидации
        $commonRules = [
            'user_id'             => 'required|integer',
            'phone'               => 'required|string',
            'address'             => 'string|nullable',
            'email'               => 'email|nullable',
            'whatsapp'            => 'string|nullable',
            'telegram'            => 'string|nullable',
            'password'            => 'nullable|string|min:8',
            'password_new'        => 'nullable|string|min:8',
            'password_new_2'      => 'nullable|string|min:8',
            'email_notifications' => 'nullable|boolean',
            'sms_notifications'   => 'nullable|boolean',
            'language'            => 'nullable|integer',
            'acting_basis_of'     => 'nullable|string',
            'theme'               => 'nullable|string',
            'inn'                 => 'required|string',
            'name_organization'   => 'nullable|string',
            'city_id'             => 'required|integer',
            'edge_id'             => 'required|integer',
        ];

        // ВАЖНО: Валидируем запрос ДО поиска пользователя
        $validator = Validator::make($request->all(), $commonRules);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors'  => $validator->errors()
            ], 422);
        }

        // Находим пользователя после успешной валидации
        $user = User::find($request->user_id);
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Несуществующий или недействующий пользователь'
            ], 400);
        }
        
        // Проверяем, что пользователь является организацией
        if (!$user->organizational_form) {
            return response()->json([
                'success' => false,
                'message' => 'Пользователь не является организацией'
            ], 400);
        }

        // Дополнительные правила в зависимости от типа организации
        if ($user->organizational_form == 'ep') {
            $epRules = [
                'name'       => 'string|nullable',
                'surname'    => 'string|nullable',
                'patronymic' => 'string|nullable',
                'ogrnip'     => 'nullable|string',
            ];
            $validationRules = array_merge($commonRules, $epRules);
        } else {
            $orgRules = [
                'in_face'    => 'required|string',
                'regulation' => 'required|string',
                'ogrn'       => 'nullable|string',
                'kpp'        => 'nullable|string',
            ];
            $validationRules = array_merge($commonRules, $orgRules);
        }

        // Валидация с учетом типа организации
        $validator = Validator::make($request->all(), $validationRules);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors'  => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();

        // Остальная логика остается без изменений...
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
            'phone'               => $data['phone'],
            'address'             => $data['address'] ?? null,
            'email'               => $data['email'],
            'whatsapp'            => $data['whatsapp'] ?? null,
            'telegram'            => $data['telegram'] ?? null,
            'language'            => $data['language'] ?? null,
            'theme'               => $data['theme'] ?? null,
            'inn'                 => $data['inn'],
            'acting_basis_of'     => $data['acting_basis_of'] ?? null,
            'name_organization'   => $data['name_organization'] ?? null,
            'city_id'             => $data['city_id'],
            'edge_id'             => $data['edge_id'],
            'email_notifications' => $data['email_notifications'] ?? false,
            'sms_notifications'   => $data['sms_notifications'] ?? false,
        ];

        // Добавляем специфичные поля в зависимости от типа организации
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
                'data'    => $user
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
            'title'                     => 'required|string|max:255',
            'content'                   => 'nullable|string',
            'cemetery_ids'              => 'required|array',
            'cemetery_ids.*'            => 'integer',
            'phone'                     => 'nullable|string|max:20',
            'telegram'                  => 'nullable|string|max:50',
            'whatsapp'                  => 'nullable|string|max:20',
            'email'                     => 'nullable|email|max:255',
            'city_id'                   => 'required|integer|exists:cities,id',
            'next_to'                   => 'nullable|string|max:255',
            'underground'               => 'nullable|string|max:255',
            'adres'                     => 'required|string|max:255',
            'width'                     => 'required|numeric',
            'longitude'                 => 'required|numeric',
            'available_installments'    => 'nullable|boolean',
            'found_cheaper'             => 'nullable|boolean',
            'сonclusion_contract'       => 'nullable|boolean',
            'state_compensation'        => 'nullable|boolean',
            'user_id'                   => 'required|integer|exists:users,id',

            // Categories and prices
            'categories_organization'   => 'nullable|array',
            'categories_organization.*' => 'integer|exists:category_products,id',
            'price_cats_organization'   => 'nullable|array',
            'price_cats_organization.*' => 'numeric',

            // Working hours
            'working_day'               => 'nullable|array',
            'working_day.*'             => 'nullable|string',
            'holiday_day'               => 'nullable|array',
            'holiday_day.*'             => 'string|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',

            // Images
            'img'                       => 'nullable|mimes:jpeg,jpg,png|max:5048',
            'img_main'                  => 'nullable|mimes:jpeg,jpg,png|max:5048',
            'images'                    => 'nullable|array|max:5',
            'images.*'                  => 'nullable|image|mimes:jpeg,jpg,png|max:5120',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $data = $validator->validated();


            // Process cemetery_ids
            $data['cemetery_ids'] = implode(",", $data['cemetery_ids']) . ',';

            // Handle file uploads
            if (!empty($data['img']) && $data['img'] instanceof \Illuminate\Http\UploadedFile) {
                $filename = Str::random(40) . '.jpeg';
                $data['img']->storeAs("uploads_organization", $filename, "public");
                $imgPath = 'uploads_organization/' . $filename;
                $hrefImg = 0; // не дефолтная картинка
            } else {
                $imgPath = 'default';
                $hrefImg = 1; // дефолтная картинка
            }

            if (!empty($data['img_main']) && $data['img_main'] instanceof \Illuminate\Http\UploadedFile) {
                $filename_main = Str::random(40) . '.jpeg';
                $data['img_main']->storeAs("uploads_organization", $filename_main, "public");
                $imgMainPath = 'uploads_organization/' . $filename_main;
                $hrefImgMain = 0; // не дефолтная картинка
            } else {
                $imgMainPath = 'default';
                $hrefImgMain = 1; // дефолтная картинка
            }

            // Create organization
            $organization = Organization::create([
                'title'                  => $data['title'],
                'status'                 => 0,
                'content'                => $data['content'],
                'cemetery_ids'           => $data['cemetery_ids'],
                'phone'                  => normalizePhone($data['phone']) ?? null,
                'telegram'               => $data['telegram'] ?? null,
                'user_id'                => $data['user_id'],
                'img_file'               => $imgPath,
                'img_main_file'          => $imgMainPath,
                'href_img'               => $hrefImg, // 1 если дефолтная, 0 если загруженная
                'href_main_img'          => $hrefImgMain, // 1 если дефолтная, 0 если загруженная
                'whatsapp'               => $data['whatsapp'] ?? null,
                'email'                  => $data['email'] ?? null,
                'city_id'                => $data['city_id'],
                'slug'                   => slugOrganization($data['title']),
                'next_to'                => $data['next_to'] ?? null,
                'underground'            => $data['underground'] ?? null,
                'adres'                  => $data['adres'],
                'width'                  => $data['width'],
                'longitude'              => $data['longitude'],
                'available_installments' => $data['available_installments'] ?? 0,
                'found_cheaper'          => $data['found_cheaper'] ?? 0,
                'state_compensation'     => $data['state_compensation'] ?? 0,
                'conclusion_contract'    => $data['conclusion_contract'] ?? 0,
            ]);


            // Create category relationships
            if (!empty($data['categories_organization']) && !empty($data['price_cats_organization'])) {
                $categoryData = [];

                foreach ($data['categories_organization'] as $key => $categoryId) {
                    $cat = CategoryProduct::find($categoryId);
                    if ($cat) {
                        $categoryData[] = [
                            'organization_id'      => $organization->id,
                            'category_main_id'     => $cat->parent_id,
                            'category_children_id' => $cat->id,
                            'rating'               => $organization->rating,
                            'price'                => $data['price_cats_organization'][$key],
                            'created_at'           => now(),
                            'updated_at'           => now(),
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
                        'day'             => $day,
                        'holiday'         => 1,
                        'time_start_work' => null,
                        'time_end_work'   => null,
                        'organization_id' => $organization->id,
                        'created_at'      => now(),
                        'updated_at'      => now(),
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
                        'day'             => $day,
                        'holiday'         => 0,
                        'time_start_work' => $startTime,
                        'time_end_work'   => $endTime,
                        'organization_id' => $organization->id,
                        'created_at'      => now(),
                        'updated_at'      => now(),
                    ];
                }
            }

            WorkingHoursOrganization::insert($workingHoursData);

            // Handle additional images
            if ($request->hasFile('images') && count($request->images) > 0) {
                $imagesData = [];

                foreach ($data['images'] as $image) {
                    if ($image != null) {
                        $filename = generateRandomString() . ".jpeg";
                        $image->storeAs("uploads_organization", $filename, "public");

                        $imagesData[] = [
                            'img_file'        => 'uploads_organization/' . $filename,
                            'href_img'        => 0,
                            'organization_id' => $organization->id,
                            'created_at'      => now(),
                            'updated_at'      => now(),
                        ];
                    }

                }

                ImageOrganization::insert($imagesData);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Organization submitted for moderation',
                'data'    => $organization->load(['activityCategories', 'workingHours', 'images'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error creating organization',
                'error'   => $e->getMessage()
            ], 500);
        }
    }


    public static function updateOrganization(Request $request)
    {
        try {
            // Валидация входных данных
            $data = $request->validate([
                'images'                  => ['nullable', 'array'],
                'images.*'                => ['nullable'],
                'img'                     => ['nullable', 'file', 'max:2048'],
                'img_main'                => ['nullable', 'file', 'max:2048'],
                'cemetery_ids'            => ['nullable', 'array'],
                'cemetery_ids.*'          => ['nullable', 'integer'],
                'id'                      => ['required', 'integer'],
                'title'                   => ['required', 'string', 'max:255'],
                'content'                 => ['nullable', 'string'],
                'phone'                   => ['nullable', 'string', 'max:20'],
                'telegram'                => ['nullable', 'string', 'max:50'],
                'whatsapp'                => ['nullable', 'string', 'max:20'],
                'email'                   => ['nullable', 'string', 'email', 'max:255'],
                'city_id'                 => ['required', 'integer'],
                'next_to'                 => ['nullable', 'string', 'max:255'],
                'underground'             => ['nullable', 'string', 'max:255'],
                'adres'                   => ['required', 'string', 'max:255'],
                'width'                   => ['required', 'string', 'max:50'],
                'longitude'               => ['required', 'string', 'max:50'],
                'categories_organization' => ['nullable', 'array'],
                'price_cats_organization' => ['nullable', 'array'],
                'working_day'             => ['nullable', 'array'],
                'holiday_day'             => ['nullable', 'array'],
                'available_installments'  => ['nullable', 'boolean'],
                'found_cheaper'           => ['nullable', 'boolean'],
                'conclusion_contract'     => ['nullable', 'boolean'],
                'state_compensation'      => ['nullable', 'boolean'],
            ]);

            // Поиск организации
            $organization = Organization::findOrFail($data['id']);

            // Обновление основных данных
            $cemeteries = isset($data['cemetery_ids']) ? implode(",", $data['cemetery_ids']) . ',' : '';

            $organization->update([
                'title'                  => $data['title'],
                'slug'                   => slugOrganization($data['title']),
                'content'                => $data['content'] ?? null,
                'cemetery_ids'           => $cemeteries,
                'phone'                  => $data['phone'] ?? null,
                'telegram'               => $data['telegram'] ?? null,
                'whatsapp'               => $data['whatsapp'] ?? null,
                'email'                  => $data['email'] ?? null,
                'city_id'                => $data['city_id'],
                'next_to'                => $data['next_to'] ?? null,
                'underground'            => $data['underground'] ?? null,
                'adres'                  => $data['adres'],
                'width'                  => $data['width'],
                'longitude'              => $data['longitude'],
                'available_installments' => $data['available_installments'] ?? false,
                'found_cheaper'          => $data['found_cheaper'] ?? false,
                'conclusion_contract'    => $data['conclusion_contract'] ?? false,
                'state_compensation'     => $data['state_compensation'] ?? false,
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
            if (isset($data['categories_organization']) && count($data['categories_organization']) > 0) {
                foreach ($data['categories_organization'] as $key => $category_organization) {
                    $cat = CategoryProduct::find($category_organization);
                    if ($cat != null) {
                        ActivityCategoryOrganization::create([
                            'organization_id'      => $organization->id,
                            'category_main_id'     => $cat->parent_id,
                            'category_children_id' => $cat->id,
                            'rating'               => $organization->rating,
                            'price'                => $data['price_cats_organization'][$key] ?? null,
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
                        'day'             => $day,
                        'holiday'         => 1,
                        'organization_id' => $organization->id,
                    ]);
                } elseif (isset($working_days[$key])) {
                    $times = explode(' - ', $working_days[$key]);
                    if (count($times) === 2) {
                        WorkingHoursOrganization::create([
                            'day'             => $day,
                            'time_start_work' => $times[0],
                            'time_end_work'   => $times[1],
                            'organization_id' => $organization->id,
                        ]);
                    }
                }
            }

            // Обработка изображений
            if (isset($data['images']) && count($data['images']) > 0) {
                // Удаляем старые изображения
                $organization->images()->delete();

                foreach ($data['images'] as $image) {
                    if ($image instanceof \Illuminate\Http\UploadedFile) {
                        $filename = uniqid() . '.' . $image->getClientOriginalExtension();
                        $path = $image->storeAs('uploads_organization', $filename, 'public');

                        ImageOrganization::create([
                            'img_file'        => $path,
                            'href_img'        => 0,
                            'organization_id' => $organization->id,
                        ]);
                    } elseif (filter_var($image, FILTER_VALIDATE_URL)) {
                        ImageOrganization::create([
                            'img_url'         => $image,
                            'href_img'        => 1,
                            'organization_id' => $organization->id,
                        ]);
                    } elseif (str_starts_with($image, 'data:image')) {
                        $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $image));
                        $filename = uniqid() . '.jpg';
                        $path = 'uploads_organization/' . $filename;
                        file_put_contents(public_path('storage/' . $path), $imageData);

                        ImageOrganization::create([
                            'img_file'        => $path,
                            'href_img'        => 0,
                            'organization_id' => $organization->id,
                        ]);
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Организация успешно обновлена',
                'data'    => $organization->fresh()->load('images', 'workingHours', 'activityCategories')
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка валидации',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка сервера: ' . $e->getMessage()
            ], 500);
        }
    }

    public function attachCashierToOrganization(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'organization_id' => 'required|string|exists:organizations,id',
            'phone'           => 'required|string|size:12|regex:/^\+\d{11}$/'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка валидации',
                'errors'  => $validator->errors()
            ], 422);
        }

        $user = auth()->user();

        $organization = Organization::find($request->organization_id);

        if (!$organization) {
            return response()->json(['success' => false, 'message' => 'Организация не найдена'], 404);
        }

//        if ($organization->user_id !== $user->id) {
//            return response()->json(['success' => false, 'message' => 'Нет доступа'], 403);
//        }

        $cashier = User::where('phone', $request->phone)->first();

        if ($cashier) {
            if ($cashier->parent_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Пользователь с таким номером телефона не найден.'
                ], 403);
            }

            if ($cashier->role !== 'cashier') {
                return response()->json([
                    'success' => false,
                    'message' => 'Пользователь с таким номером телефона не является кассиром.'
                ], 403);
            }

            $cashier->organization_id_branch = $organization->id;
            $cashier->save();

            return response()->json([
                'success' => true,
                'data'    => [
                    'user_id' => $cashier->id
                ],
                'message' => 'Кассир успешно привязан к выбранной организации'
            ]);
        } else {
            try {
                $cashier = User::create([
                    'parent_id'              => $user->id,
                    'phone'                  => $request->phone,
                    'role'                   => 'cashier',
                    'organization_id_branch' => $organization->id
                ]);

                return response()->json([
                    'success' => true,
                    'data'    => [
                        'user_id' => $cashier->id
                    ],
                    'message' => 'Кассир успешно создан привязан к выбранной организации'
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ошибка при создании нового кассира',
                    'error'   => $e->getMessage()
                ], 500);
            }

        }
    }

    public function unlinkCashierFromOrganization(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'organization_id' => 'required|string|exists:organizations,id',
            'cashier_id'      => 'required|integer|exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка валидации',
                'errors'  => $validator->errors()
            ], 422);
        }

        $user = auth()->user();

        $organization = Organization::find($request->organization_id);

        if (!$organization) {
            return response()->json(['success' => false, 'message' => 'Организация не найдена'], 404);
        }

        if ($organization->user_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Нет доступа'], 403);
        }

        $cashier = User::find($request->cashier_id);

        if ($cashier) {
            if ($cashier->parent_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Кассир не найден.'
                ], 403);
            }

            if ($cashier->role !== 'cashier') {
                return response()->json([
                    'success' => false,
                    'message' => 'Пользователь с таким номером телефона не является кассиром.'
                ], 403);
            }

            $cashier->organization_id_branch = null;
            $cashier->save();

            return response()->json([
                'success' => true,
                'data'    => [
                    'user_id' => $cashier->id
                ],
                'message' => 'Кассир успешно отвязан от выбранной организации'
            ]);

        } else {
            return response()->json([
                'success' => false,
                'message' => 'Кассир не найден'
            ], 403);
        }
    }

    public function getCallStats(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'organization_id' => 'required|string|exists:organizations,id',
            'status'          => 'nullable|string|in:accepted,rejected,no_status',
            'limit'           => 'nullable|integer|min:1|max:100',
            'page'            => 'nullable|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка валидации',
                'errors'  => $validator->errors()
            ], 422);
        }

        $user = auth()->user();

        $organization = Organization::find($request->organization_id);

        if (!$organization) {
            return response()->json(['success' => false, 'message' => 'Организация не найдена'], 404);
        }

        if ($organization->user_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Нет доступа'], 403);
        }

        $query = CallStat::query()
            ->where('organization_id', $organization->id)
            ->latest();

        if ($request->filled('status')) {
            $status = $request->status;

            $query->where(function ($q) use ($status) {
                switch ($status) {
                    case 'accepted':
                        $q->where('call_status', 'like', '11%');
                        break;
                    case 'rejected':
                        $q->whereNotNull('call_status')
                            ->whereNot('call_status', 'like', '11%');
                        break;
                    case 'no_status':
                        $q->whereNull('call_status');
                        break;
                }
            });
        }

        $limit = $request->limit ?? 10;
        $calls = $query->paginate($limit, ['*'], 'page', $request->page ?? 1);

        $getStatusText = function ($callStatus) {
            if ($callStatus === null) return 'Без статуса';
            if (str_starts_with($callStatus, '11')) return 'Принят';
            return 'Отклонён';
        };

        $items = $calls->getCollection()->map(function ($call) use ($getStatusText) {
            return [
                'organization_id' => (string)$call->organization_id,
                'date_start'      => $call->date_start->format('Y-m-d H:i'),
                'call_status'     => $call->call_status,
                'status_text'     => $getStatusText($call->call_status),
                'city'            => $call->city,
                'record_url'      => $call->record_url
            ];
        })->toArray();

        return response()->json([
            'success'    => true,
            'message'    => 'Список звонков организации',
            'data'       => $items,
            'pagination' => [
                'total'        => $calls->total(),
                'per_page'     => $calls->perPage(),
                'current_page' => $calls->currentPage(),
                'last_page'    => $calls->lastPage(),
            ]
        ]);
    }

    public function getOrganizationCemeteries(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'organization_id' => 'required|string|exists:organizations,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка валидации',
                'errors'  => $validator->errors()
            ], 422);
        }

        $user = auth()->user();

        $organization = Organization::find($request->organization_id);

        if (!$organization) {
            return response()->json(['success' => false, 'message' => 'Организация не найдена'], 404);
        }

        if ($organization->user_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Нет доступа'], 403);
        }

        $cemeteryIdsString = $organization->cemetery_ids;

        if (empty($cemeteryIdsString)) {
            return response()->json([
                'success' => true,
                'message' => 'Нет кладбищ на которых работает организация',
                'data'    => []
            ]);
        }

        $cemeteryIds = array_map('trim', explode(',', $cemeteryIdsString));
        $cemeteryIds = array_filter($cemeteryIds, function ($id) {
            return !empty($id);
        });

        if (empty($cemeteryIds)) {
            return response()->json([
                'success' => true,
                'message' => 'Нет кладбищ, на которых работает организация',
                'data'    => []
            ]);
        }

        $cemeteries = Cemetery::whereIn('id', $cemeteryIds)
            ->orderBy('title')->get();

        $items = $cemeteries->map(function ($cemetery) {
            return [
                'id'        => (string)$cemetery->id,
                'title'     => $cemetery->title,
                'latitude'  => $cemetery->width,
                'longitude' => $cemetery->longitude
            ];
        })->toArray();

        return response()->json([
            'success' => true,
            'message' => 'Список кладбищ на которых работает организация',
            'data'    => $items
        ]);
    }

    public function addRequestsCostProductSuppliers(Request $request)
    {
        // Упрощенная валидация
        $validated = $request->validate([
            'organization_id' => 'required|integer',
            'user_id'         => 'required|integer',
            'products'        => 'required|array|min:1',
            'products.*'      => 'integer',
            'count'           => 'required|array|min:1',
            'count.*'         => 'integer|min:1',
            'lcs'             => 'nullable|array',
            'lcs.*'           => 'string',
            'all_lcs'         => 'nullable|boolean'
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
        $products = array_map(function ($product, $count) {
            return [(int)$product, (int)$count, 0];
        }, $validated['products'], $validated['count']);

        // Обработка транспортных компаний
        $transportCompanies = ($validated['all_lcs'] ?? false) ? 'all' : ($validated['lcs'] ?? []);

        try {
            $requestCost = RequestsCostProductsSupplier::create([
                'organization_id'             => $validated['organization_id'],
                'products'                    => json_encode($products),
                'transport_companies'         => json_encode($transportCompanies),
                'categories_provider_product' => json_encode($validated['products']),
            ]);

            return response()->json($requestCost, 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ошибка при создании заявки',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function deleteRequestCostProductProvider($request)
    {
        $aplication = RequestsCostProductsSupplier::findOrFail($request)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Заявка успешно удалена.'
        ], 200);

    }

    public function createProviderOffer(Request $request)
    {
        // Validate request data
        $validator = Validator::make($request->all(), [
            'title'             => ['required', 'string', 'max:255'],
            'content'           => ['required', 'string'],
            'images'            => ['required', 'array', 'max:5'],
            'images.*'          => [
                'required',
                'image',
                'mimes:jpeg,jpg,png,gif,svg,webp',
                'max:2048'
            ],
            'category_id'       => ['nullable', 'integer'],
            'delivery_required' => ['nullable', 'integer'],
        ]);


        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
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
                'title'             => $request->title,
                'content'           => $request->content,
                'organization_id'   => $organization->id,
                'images'            => json_encode($imagePaths),
                'category_id'       => $request->category_id,
                'delivery_required' => $request->boolean('delivery_required'),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Offer created successfully',
                'data'    => $offer
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create offer',
                'error'   => $e->getMessage()
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
            'data'    => $reviews
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
        $comments = CommentProduct::whereHas('product', function ($query) use ($organizationId) {
            $query->where('organization_id', $organizationId);
        })
            ->with(['product']) // Загружаем товар
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data'    => $comments
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
            'data'    => $review
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
                'errors'  => $validator->errors()
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
            'content'   => $request->content,
            'edited_at' => now() // метка редактирования
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Отзыв обновлен',
            'data'    => $review
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
            'data'    => $comment
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
                'errors'  => $validator->errors()
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
            'content'   => $request->content,
            'edited_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Комментарий обновлен',
            'data'    => $comment
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
                'errors'  => $validator->errors()
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
            'data'    => $review
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
                'errors'  => $validator->errors()
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
            'data'    => $comment
        ]);
    }

    public static function organizationsCity(City $city, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка валидации',
                'errors'  => $validator->errors()
            ], 422);
        }

        // Прямой запрос к организациям с фильтрацией по городу
        $query = Organization::where('city_id', $city->id)
            ->select('id', 'title', 'name_type', 'phone', 'status', 'adres', 'two_gis_link')
            ->where('status', '1');

        if ($request->has('name') && !empty($request->name)) {
            $query->where('title', 'like', '%' . $request->name . '%');
        }

        $organizations = $query->get();

        // Преобразуем все числовые ID в строки для корректной передачи в JSON
        $organizations->transform(function ($org) {
            return [
                'id'           => (string)$org->id, // Преобразуем ID в строку
                'title'        => $org->title,
                'name_type'    => $org->name_type,
                'phone'        => $org->phone,
                'status'       => $org->status,
                'adres'        => $org->adres,
                'two_gis_link' => $org->two_gis_link
            ];
        });

        return response()->json([
            'success'       => true,
            'message'       => 'Организации успешно найдены',
            'organizations' => $organizations,
            'city'          => [
                'id'    => (string)$city->id, // Также преобразуем ID города в строку
                'title' => $city->title
            ],
        ]);
    }

    public static function citySearch(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'city' => 'required|string|max:3000'
        ]);

        $cities = DB::table('cities')
            ->select('cities.id', 'cities.title') // Выбираем только id и title
            ->join('organizations', 'organizations.city_id', '=', 'cities.id')
            ->join('areas', 'cities.area_id', '=', 'areas.id')
            ->join('edges', 'areas.edge_id', '=', 'edges.id')
            ->where('cities.title', 'like', $request->city . '%')
            ->where('edges.is_show', 1)
            ->groupBy('cities.id', 'cities.title') // Добавляем title в groupBy
            ->orderBy('cities.title', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Города успешно найдены',
            'cities'  => $cities, // Теперь cities содержат только id и title
        ]);
    }

    public static function edgeSearch(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'edge' => 'nullable|string|max:3000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка валидации',
                'errors'  => $validator->errors()
            ], 422);
        }

        $query = DB::table('edges')
            ->select('edges.*')
            ->where('edges.is_show', 1)
            // Исключаем записи, где title содержит JSON-структуру
            ->where(function ($q) {
                $q->where('edges.title', 'not like', '{%')
                    ->where('edges.title', 'not like', '{"%');
            });

        // Добавляем условие поиска только если edge не пустое
        if (!empty($request->edge)) {
            $query->where('edges.title', 'like', $request->edge . '%');
        }

        $edges = $query->orderBy('edges.title', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Регионы успешно найдены',
            'edges'   => $edges,
        ]);
    }

    public function sendCode(Request $request)
    {
        $request->validate([
            'organization_id' => 'required|exists:organizations,id',
        ]);

        $organization = Organization::find($request->organization_id);
        $code = generateRandomNumber(); // Генерация кода (например, 4-6 цифр)

        if ($organization->type_phone == 'mobile') {
            sendSms($organization->phone, $code);
        } else {
            $send_code = sendCode($organization->phone, $code);
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
            'code'            => 'required|string|min:4|max:6',
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


    public static function userWallets()
    {
        $wallets = auth()->user()->wallets;

        return response()->json([
            'success' => true,
            'message' => 'Кошельки успешно найдены',
            'wallets' => $wallets
        ]);
    }

    public static function deleteWallet(Wallet $wallet)
    {
        $wallet->delete();
        return response()->json([
            'success' => true,
            'message' => 'Кошелек успешно удален',
        ]);
    }


    public static function walletUpdateBalance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'wallet_id' => 'required|exists:wallets,id',
            'count'     => 'required|integer|min:1',
            'email'     => 'required|email',
            'deep_link' => ['required', 'regex:/^[a-zA-Z][a-zA-Z0-9+.-]*:\/\/.+/']
        ], [
            'deep_link.regex' => 'Значение поля deep link имеет ошибочный формат URL.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка валидации',
                'errors'  => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();
        $service = new YooMoneyService();
        
        $user = auth()->user();
        $wallet = Wallet::find($data['wallet_id']);
        
        $metadata = [
            'wallet_id' => $data['wallet_id'],
            'count'     => $data['count'],
            'deep_link' => $data['deep_link'],
            'user_id'   => $user->id
        ];

        try {
            // Передаем email и phone как отдельные параметры
            $payment = $service->createMobilePayment(
                $data['count'],           // amount
                $data['deep_link'],       // deepLink
                'Пополнение баланса',     // description
                $metadata,                // metadata
                $data['email'],           // customerEmail
                $user->phone ?? null      // customerPhone
            );

            return response()->json([
                'success'     => true,
                'payment_url' => $payment['confirmation_url'],
                'payment_id'  => $payment['id']
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
            ->with(['typeService' => function ($query) {
                $query->where('is_show', 1)
                    ->select('id', 'type_application_id', 'title', 'title_ru', 'price', 'premium_price');
            }])
            ->select('id', 'title', 'title_ru')
            ->get();

        $result = [];

        foreach ($applicationTypes as $appType) {
            $typeData = [
                'id'       => $appType->id,
                'title'    => $appType->title,
                'title_ru' => $appType->title_ru,
                'services' => []
            ];

            foreach ($appType->typeService as $service) {
                $typeData['services'][] = [
                    'id'            => $service->id,
                    'title'         => $service->title,
                    'title_ru'      => $service->title_ru,
                    'price'         => $service->price ?? 0,
                    'premium_price' => $service->premium_price ?? 0
                ];
            }

            if (count($typeData['services']) > 0) {
                $result[] = $typeData;
            }
        }

        return response()->json([
            'success' => true,
            'data'    => $result
        ]);
    }


    public static function payApplication(Request $request)
    {
        $data = $request->validate([
            'count'           => ['required', 'integer', 'min:1'],
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
                    'count'               => DB::raw("count + {$data['count']}"),
                    'type_application_id' => $typeService->type_application_id
                ]
            );
        });

        return response()->json([
            'success'         => true,
            'message'         => 'Заявки успешно приобретены',
            'balance'         => $user->currentWallet()->fresh()->balance,
            'purchased_count' => $data['count']
        ]);
    }


    public static function buyPriority(Request $request)
    {
        $validated = $request->validate([
            'type_priority' => 'required|string|in:1',
            'priority'      => 'required|string|in:priority-list-companies-1-3,priority-list-companies-4-6'
        ]);

        $user = auth()->user();

        // Проверяем, есть ли у пользователя организация
        if (!$user->organization()) {
            return response()->json([
                'success' => false,
                'message' => 'У пользователя нет организации'
            ], 400);
        }

        $organization = $user->organization(); // Без скобок!
        $typeService = getTypeService($validated['priority']);

        // Проверяем, найден ли тип услуги
        if (!$typeService) {
            return response()->json([
                'success' => false,
                'message' => 'Услуга не найдена'
            ], 400);
        }

        $price = $typeService->price;

        // Проверка баланса
        if (!$user->currentWallet()->hasSufficientBalance($price)) {
            return response()->json([
                'success' => false,
                'message' => 'Недостаточно средств на балансе'
            ], 402);
        }

        // Определение уровня приоритета
        $priorityLevel = match ($validated['priority']) {
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
            'success'      => true,
            'message'      => 'Приоритет организации успешно обновлен',
            'new_priority' => $priorityLevel,
            'balance'      => $user->currentWallet()->fresh()->balance
        ]);
    }

    public static function findUser(User $user)
    {
        return response()->json([
            'success' => true,
            'message' => 'Пользователь успешно найден',
            'data'    => $user
        ]);
    }


    public function getMainCategories(): JsonResponse
    {
        try {
            $categories = CategoryProduct::whereNull('parent_id')
                ->where('display', true)
                ->orderBy('title')
                ->get(['id', 'title', 'slug',]);

            return response()->json([
                'success' => true,
                'data'    => $categories,
                'message' => 'Основные категории успешно получены'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при получении категорий',
                'error'   => $e->getMessage()
            ], 500);
        }
    }


    public function getSubcategories(int $categoryId): JsonResponse
    {
        try {
            // Проверяем существование категории
            $mainCategory = CategoryProduct::find($categoryId);

            if (!$mainCategory) {
                return response()->json([
                    'success' => false,
                    'message' => 'Категория не найдена'
                ], 404);
            }

            $subcategories = CategoryProduct::where('parent_id', $categoryId)
                ->where('display', true)
                ->orderBy('title')
                ->get(['id', 'title', 'slug', 'parent_id']);

            return response()->json([
                'success' => true,
                'data'    => [
                    'main_category' => $mainCategory->only(['id', 'title', 'slug']),
                    'subcategories' => $subcategories
                ],
                'message' => 'Подкатегории успешно получены'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при получении подкатегорий',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function getCemeteries(Request $request): JsonResponse
    {
        try {
            $query = Cemetery::with([
                'city'           => function ($q) {
                    $q->select('id', 'title', 'area_id');
                },
                'city.area'      => function ($q) {
                    $q->select('id', 'title', 'edge_id');
                },
                'city.area.edge' => function ($q) {
                    $q->select('id', 'title');
                }
            ])->orderBy('priority');

            // Проверяем существование сущностей для фильтров
            if ($request->has('city_id') && $request->city_id) {
                $cityExists = \App\Models\City::where('id', $request->city_id)->exists();
                if (!$cityExists) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Город с указанным ID не найден'
                    ], 404);
                }
                $query->where('city_id', $request->city_id);
            }

            if ($request->has('area_id') && $request->area_id) {
                $areaExists = \App\Models\Area::where('id', $request->area_id)->exists();
                if (!$areaExists) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Район с указанным ID не найден'
                    ], 404);
                }
                $query->whereHas('city', function ($q) use ($request) {
                    $q->where('area_id', $request->area_id);
                });
            }

            if ($request->has('edge_id') && $request->edge_id) {
                $edgeExists = \App\Models\Edge::where('id', $request->edge_id)->exists();
                if (!$edgeExists) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Край с указанным ID не найден'
                    ], 404);
                }
                $query->whereHas('city.area', function ($q) use ($request) {
                    $q->where('edge_id', $request->edge_id);
                });
            }

            $cemeteries = $query->get();

            // Преобразуем ID в строки для всей коллекции
            $transformedCemeteries = $cemeteries->map(function ($cemetery) {
                return [
                    'id'       => (string)$cemetery->id,
                    'title'    => $cemetery->title,
                    'city_id'  => (string)$cemetery->city_id,
                    'adres'    => $cemetery->adres,
                    'priority' => $cemetery->priority,
                    'city'     => $cemetery->city ? [
                        'id'      => (string)$cemetery->city->id,
                        'title'   => $cemetery->city->title,
                        'area_id' => $cemetery->city->area_id ? (string)$cemetery->city->area_id : null,
                        'area'    => $cemetery->city->area ? [
                            'id'      => (string)$cemetery->city->area->id,
                            'title'   => $cemetery->city->area->title,
                            'edge_id' => $cemetery->city->area->edge_id ? (string)$cemetery->city->area->edge_id : null,
                            'edge'    => $cemetery->city->area->edge ? [
                                'id'    => (string)$cemetery->city->area->edge->id,
                                'title' => $cemetery->city->area->edge->title,
                            ] : null
                        ] : null
                    ] : null
                ];
            });

            return response()->json([
                'success' => true,
                'data'    => $transformedCemeteries,
                'filters' => $request->only(['city_id', 'area_id', 'edge_id']),
                'count'   => $cemeteries->count(),
                'message' => $cemeteries->count() > 0
                    ? 'Кладбища успешно получены'
                    : 'Кладбища не найдены по заданным фильтрам'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при получении кладбищ',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function getCemetery(int $id): JsonResponse
    {
        try {
            $cemetery = Cemetery::with([
                'city'           => function ($q) {
                    $q->select('id', 'title', 'area_id');
                },
                'city.area'      => function ($q) {
                    $q->select('id', 'title', 'edge_id');
                },
                'city.area.edge' => function ($q) {
                    $q->select('id', 'title');
                }
            ])->find($id);

            if (!$cemetery) {
                return response()->json([
                    'success' => false,
                    'message' => 'Кладбище не найдено'
                ], 404);
            }

            // Создаем преобразованный массив вручную
            $transformedCemetery = [
                'id'       => (string)$cemetery->id,
                'title'    => $cemetery->title,
                'city_id'  => (string)$cemetery->city_id,
                'adres'    => $cemetery->adres,
                'priority' => $cemetery->priority,
                'city'     => $cemetery->city ? [
                    'id'      => (string)$cemetery->city->id,
                    'title'   => $cemetery->city->title,
                    'area_id' => $cemetery->city->area_id ? (string)$cemetery->city->area_id : null,
                    'area'    => $cemetery->city->area ? [
                        'id'      => (string)$cemetery->city->area->id,
                        'title'   => $cemetery->city->area->title,
                        'edge_id' => $cemetery->city->area->edge_id ? (string)$cemetery->city->area->edge_id : null,
                        'edge'    => $cemetery->city->area->edge ? [
                            'id'    => (string)$cemetery->city->area->edge->id,
                            'title' => $cemetery->city->area->edge->title,
                        ] : null
                    ] : null
                ] : null
            ];

            return response()->json([
                'success' => true,
                'data'    => $transformedCemetery,
                'message' => 'Информация о кладбище успешно получена'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при получении информации о кладбище',
                'error'   => $e->getMessage()
            ], 500);
        }
    }


    public function changeOrganization(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'organization_id' => 'required|integer|exists:organizations,id'
            ]);

            $user = auth()->user();

            // Проверяем, принадлежит ли организация пользователю
            $organization = Organization::where('id', $request->organization_id)
                ->where('user_id', $user->id)
                ->first();
            if (!$organization) {
                return response()->json([
                    'success' => false,
                    'message' => 'Организация не найдена или не принадлежит пользователю'
                ], 404);
            }

            // Обновляем выбранную организацию
            $user->organization_id = $request->organization_id;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Организация успешно изменена',
                'data'    => [
                    'organization_id'   => (string)$user->organization_id,
                    'organization_name' => $organization->title
                ]
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка валидации',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при изменении организации',
                'error'   => $e->getMessage()
            ], 500);
        }
    }


    public function getUserOrganizations(): JsonResponse
    {
        try {
            $user = auth()->user();

            $organizations = Organization::where('user_id', $user->id)
                ->orderBy('title')
                ->get(['id', 'slug', 'title', 'created_at']);

            // Преобразуем ID в строки
            $organizations->transform(function ($org) {
                $org->id = (string)$org->id;
                return $org;
            });

            return response()->json([
                'success' => true,
                'data'    => $organizations,
                'count'   => $organizations->count(),
                'message' => 'Организации пользователя успешно получены'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при получении организаций',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function getCurrentOrganization(): JsonResponse
    {
        try {
            $user = auth()->user();

            if (!$user->organization_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Организация не выбрана'
                ], 404);
            }

            $organization = Organization::where('id', $user->organization_id)
                ->where('user_id', $user->id)
                ->first();

            if (!$organization) {
                $user->organization_id = null;
                $user->save();

                return response()->json([
                    'success' => false,
                    'message' => 'Организация не найдена'
                ], 404);
            }

            // Преобразуем ID в строку
            $organization->id = (string)$organization->id;

            return response()->json([
                'success' => true,
                'data'    => $organization,
                'message' => 'Текущая организация успешно получена'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при получении организации',
                'error'   => $e->getMessage()
            ], 500);
        }
    }


    public function getOrderProducts(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'organization_id' => 'required|string|exists:organizations,id',
            'status'          => 'nullable|integer|in:0,1,2',
            'category_id'     => 'nullable|integer|exists:category_products,id',
            'page'            => 'nullable|integer|min:1',
            'limit'           => 'nullable|integer|min:1|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка валидации',
                'errors'  => $validator->errors()
            ], 422);
        }

        $user = auth()->user();
        $organization = Organization::find($request->organization_id);

        if (!$organization) {
            return response()->json(['success' => false, 'message' => 'Организация не найдена'], 404);
        }

        if ($organization->user_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Нет доступа'], 403);
        }

        $query = OrderProduct::query()
            ->where('organization_id', $organization->id)
            ->with([
                'product',
                'product.category',
                'user',
                'cemetery',
                'mortuary'
            ]);

        if ($request->has('status') && $request->status !== null) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category_id')) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where('category_id', $request->category_id)
                    ->orWhere('category_parent_id', $request->category_id);
            });
        }

        $query->orderBy('created_at', 'desc');

        $limit = $request->limit ?? 10;
        $orders = $query->paginate($limit);

        $data = $orders->getCollection()->map(function ($order) {
            return [
                'id'               => $order->id,
                'status'           => $order->status,
                'status_text'      => match ($order->status) {
                    0 => 'Новый',
                    1 => 'В работе',
                    2 => 'Архив',
                    default => 'Неизвестно'
                },
                'count'            => $order->count,
                'price'            => $order->price,
                'all_price'        => $order->all_price,
                'customer_comment' => $order->customer_comment,

                'size'       => $order->size,
                'additional' => $order->additional,
                'date'       => $order->date,
                'time'       => $order->time,

                'city_from' => $order->city_from,
                'city_to'   => $order->city_to,


                'cemetery' => $order->cemetery ? [
                    'id'    => $order->cemetery->id,
                    'title' => $order->cemetery->title,
                ] : null,


                'mortuary' => $order->mortuary ? [
                    'id'    => $order->mortuary->id,
                    'title' => $order->mortuary->title,
                ] : null,

                'product' => [
                    'id'       => $order->product->id,
                    'title'    => $order->product->title,
                    'slug'     => $order->product->slug,
                    'category' => $order->product->category->title,
                    'image'    => $order->product->getImages->first()->title ?? null,
                ],

                'user' => [
                    'id'    => $order->user->id,
                    'name'  => trim($order->user->surname . ' ' . $order->user->name . ' ' . $order->user->patronymic),
                    'phone' => $order->user->phone,
                    'email' => $order->user->email,
                ],

                'created_at' => $order->created_at->format('Y-m-d H:i'),
            ];
        });

        return response()->json([
            'success'    => true,
            'data'       => $data,
            'pagination' => [
                'total'        => $orders->total(),
                'current_page' => $orders->currentPage(),
                'per_page'     => $orders->perPage(),
                'last_page'    => $orders->lastPage(),
            ]
        ]);
    }

    public static function users()
    {
        $users = auth()->user()->users()->with('organizationBranch')->get();

        $formattedUsers = $users->map(function ($user) {
            return [
                'id'                => $user->id,
                'name'              => $user->name,
                'surname'           => $user->surname,
                'patronymic'        => $user->patronymic,
                'phone'             => $user->phone,
                'email'             => $user->email,
                'organization_name' => $user->organizationBranch->organization->title ?? null,
                'branch_name'       => $user->organizationBranch->title ?? null,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Пользователи успешно получены',
            'data'    => $formattedUsers,
        ], 200);
    }

    public static function storeUser(Request $request)
    {
        try {
            // Валидация
            $validator = Validator::make($request->all(), [
                'surname'                => 'required|string|max:255',
                'name'                   => 'required|string|max:255',
                'email'                  => 'nullable|email|unique:users',
                'phone'                  => 'required|string',
                'organization_id_branch' => 'nullable|exists:organizations,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ошибка валидации',
                    'errors'  => $validator->errors()
                ], 422);
            }

            $users_phone = User::where('phone', normalizePhone($request->phone))->first();
            if ($users_phone != null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Пользователь с таким телефоном уже существует'
                ], 422);
            }

            $user = new User();
            $user->surname = $request->surname;
            $user->role = 'cashier';
            $user->name = $request->name;
            $user->patronymic = $request->patronymic;
            $user->phone = $request->phone;
            $user->email = $request->email;
            $user->organization_id_branch = $request->organization_id_branch;
            $user->parent_id = auth()->id();
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Пользователь успешно создан',
                'data'    => $user
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при создании пользователя'
            ], 500);
        }
    }

    public static function editUser(User $user)
    {
        // Проверка принадлежности пользователя
        if ($user->parent_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Пользователь вам не принадлежит'
            ], 403);
        }

        $organization_user = $user->organizationBranch;
        $organizations = auth()->user()->organizations;

        return response()->json([
            'success' => true,
            'data'    => [
                'user'              => $user,
                'organization_user' => $organization_user,
                'organizations'     => $organizations
            ]
        ]);
    }

    public static function updateUser(Request $request, User $user)
    {
        try {
            // Проверка принадлежности пользователя
            if ($user->parent_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Пользователь вам не принадлежит'
                ], 403);
            }

            // Валидация
            $validator = Validator::make($request->all(), [
                'surname'                => 'required|string|max:255',
                'name'                   => 'required|string|max:255',
                'email'                  => 'nullable|email|unique:users,email,' . $user->id,
                'phone'                  => 'required|string|unique:users,phone,' . $user->id,
                'organization_id_branch' => 'nullable|exists:organizations,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ошибка валидации',
                    'errors'  => $validator->errors()
                ], 422);
            }

            $users_phone = User::where('phone', normalizePhone($request->phone))->first();
            if ($user->phone != $request->phone && $users_phone != null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Пользователь с таким телефоном уже существует'
                ], 422);
            }

            $user->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Данные пользователя обновлены',
                'data'    => $user
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при обновлении пользователя'
            ], 500);
        }
    }

    public static function destroyUser(User $user)
    {
        try {
            // Проверка принадлежности пользователя
            if ($user->parent_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Пользователь вам не принадлежит'
                ], 403);
            }

            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'Пользователь успешно удален'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при удалении пользователя'
            ], 500);
        }
    }
}
