<?php

namespace App\Http\Controllers\Api\Account\Agency;

use App\Http\Controllers\Controller;
use App\Models\CategoryProduct;
use App\Models\ImageProduct;
use App\Models\Organization;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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
}