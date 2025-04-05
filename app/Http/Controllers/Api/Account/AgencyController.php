<?php

namespace App\Http\Controllers\Api\Account;

use App\Http\Controllers\Controller;
use App\Models\CategoryProduct;
use App\Models\Organization;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AgencyController extends Controller
{
    public static function products(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'organization_id' => 'required|integer|exists:organizations,id',
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
            ], 400);
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
                    'discount' => $product->discount ?? 0,
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
}