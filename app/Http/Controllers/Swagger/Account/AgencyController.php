<?php

namespace App\Http\Controllers\Swagger\Account;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class AgencyController extends Controller
{
    /**
     * Получить список продуктов агентства с возможностью фильтрации и пагинации
     *
     * @OA\Get(
     *     path="/v1/account/agency/products",
     *     tags={"Продукты организции"},
     *     summary="Получение списка продуктов агентства",
     *     description="Возвращает отфильтрованный список продуктов организации с пагинацией",
     *     operationId="getAgencyProducts",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="organization_id",
     *         in="query",
     *         description="ID организации",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="category",
     *         in="query",
     *         description="Фильтр по категории",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="subcategory",
     *         in="query",
     *         description="Фильтр по подкатегории",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="search_text",
     *         in="query",
     *         description="Текст для поиска по названию продукта",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Количество элементов на странице (1-100)",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             default=10
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Номер страницы",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             default=1
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Успешный запрос",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="current_category", type="string", nullable=true),
     *                 @OA\Property(property="current_subcategory", type="string", nullable=true),
     *                 @OA\Property(
     *                     property="products",
     *                     type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="name", type="string", example="Название продукта"),
     *                         @OA\Property(property="price", type="number", format="float", example=99.99),
     *                         @OA\Property(property="discount", type="number", format="float", example=0),
     *                         @OA\Property(
     *                             property="features",
     *                             type="object",
     *                             @OA\Property(property="size", type="string", example="M"),
     *                             @OA\Property(property="material", type="string", example="Хлопок"),
     *                             @OA\Property(property="color", type="string", example="Синий")
     *                         ),
     *                         @OA\Property(property="stars", type="number", format="float", example=4.5),
     *                         @OA\Property(property="reviews_count", type="integer", example=10)
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="pagination",
     *                     type="object",
     *                     @OA\Property(property="total", type="integer", example=100),
     *                     @OA\Property(property="per_page", type="integer", example=10),
     *                     @OA\Property(property="current_page", type="integer", example=1),
     *                     @OA\Property(property="last_page", type="integer", example=10)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Ошибки валидации",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation errors"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 example={"organization_id": {"The organization id field is required."}}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Организация не найдена",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Organization not found")
     *         )
     *     )
     * )
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public static function products(Request $request)
    {
        // ... существующий код метода ...
    }
}