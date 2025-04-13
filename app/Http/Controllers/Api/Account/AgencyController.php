<?php

namespace App\Http\Controllers\Api\Account;

use App\Http\Controllers\Controller;
use App\Models\CategoryProduct;
use App\Models\ImageProduct;
use App\Models\Organization;
use App\Models\Product;
use Illuminate\Http\Request;
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
}