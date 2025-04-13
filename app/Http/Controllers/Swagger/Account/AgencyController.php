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
     *     tags={"Продукты организации"},
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


/**
 * @OA\Post(
 *     path="/v1/account/agency/products",
 *     summary="Добавить новый товар",
 *     tags={"Продукты организации"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 required={"organization_id", "title", "price", "category_id"},
 *                 @OA\Property(property="organization_id", type="integer", example=1),
 *                 @OA\Property(property="title", type="string", example="Кепка Adidas"),
 *                 @OA\Property(property="content", type="string", example="Удобная кепка"),
 *                 @OA\Property(property="price", type="number", format="float", example=1999.99),
 *                 @OA\Property(property="price_sale", type="number", format="float", example=10),
 *                 @OA\Property(property="category_id", type="integer", example=2),
 *                 @OA\Property(property="size", type="string", example="L"),
 *                 @OA\Property(property="material", type="string", example="Хлопок"),
 *                 @OA\Property(property="color", type="string", example="Красный"),
 *                 @OA\Property(
 *                     property="images[]",
 *                     type="array",
 *                     @OA\Items(type="string", format="binary")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Product added successfully"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validation errors"
 *     )
 * )

*/

        public function addProduct(Request $request){}
/**
 * @OA\Post(
 *     path="/v1/account/agency/products/{productId}/update",
 *     summary="Обновление товара",
 *     description="Обновляет информацию о товаре. Требуется ID организации для проверки владельца. Все поля, кроме organization_id, являются необязательными.",
 *     tags={"Продукты организации"},
 *     security={{"bearerAuth": {}}},
 *     @OA\Parameter(
 *         name="productId",
 *         in="path",
 *         required=true,
 *         description="ID товара",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         description="Данные для обновления товара",
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 required={"organization_id"},
 *                 @OA\Property(property="organization_id", type="integer", example=1),
 *                 @OA\Property(property="title", type="string", maxLength=255, nullable=true, example="Новая кепка"),
 *                 @OA\Property(property="content", type="string", nullable=true, example="Описание товара"),
 *                 @OA\Property(property="price", type="number", format="float", minimum=0, nullable=true, example=1999.99),
 *                 @OA\Property(property="price_sale", type="number", format="float", nullable=true, minimum=0, maximum=100, example=1499.99),
 *                 @OA\Property(property="category_id", type="integer", nullable=true, example=2),
 *                 @OA\Property(property="size", type="string", nullable=true, example="XL"),
 *                 @OA\Property(property="material", type="string", nullable=true, example="Хлопок"),
 *                 @OA\Property(property="color", type="string", nullable=true, example="Красный"),
 *                 @OA\Property(
 *                     property="images[]",
 *                     type="array",
 *                     @OA\Items(type="string", format="binary"),
 *                     nullable=true,
 *                     description="До 5 изображений (jpeg,png,jpg,gif), макс. размер 2MB"
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Товар успешно обновлен",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Product updated successfully"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="organization_id", type="integer", example=1),
 *                 @OA\Property(property="title", type="string", example="Новая кепка"),
 *                 @OA\Property(property="slug", type="string", example="novaya-kepka"),
 *                 @OA\Property(property="content", type="string", nullable=true, example="Описание товара"),
 *                 @OA\Property(property="price", type="number", format="float", example=1999.99),
 *                 @OA\Property(property="price_sale", type="number", format="float", nullable=true, example=1499.99),
 *                 @OA\Property(property="total_price", type="number", format="float", example=1499.99),
 *                 @OA\Property(property="category_id", type="integer", example=2),
 *                 @OA\Property(property="size", type="string", nullable=true, example="XL"),
 *                 @OA\Property(property="material", type="string", nullable=true, example="Хлопок"),
 *                 @OA\Property(property="color", type="string", nullable=true, example="Красный"),
 *                 @OA\Property(property="status", type="string", example="active"),
 *                 @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00Z"),
 *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00Z"),
 *                 @OA\Property(
 *                     property="images",
 *                     type="array",
 *                     @OA\Items(
 *                         type="object",
 *                         @OA\Property(property="id", type="integer", example=1),
 *                         @OA\Property(property="title", type="string", example="uploads/product/image1.jpg"),
 *                         @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00Z"),
 *                         @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00Z")
 *                     )
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
 *                 example={
 *                     "organization_id": {"Поле organization_id обязательно для заполнения."},
 *                     "price": {"Поле price должно быть числом."}
 *                 }
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Доступ запрещен",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Product doesn't belong to this organization")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Товар не найден",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Product not found")
 *         )
 *     )
 * )
 */
    public function updateProduct(Request $request, $productId){}

/**
 * @OA\Delete(
 *     path="/v1/account/agency/products/{productId}",
 *     summary="Удалить товар",
 *     tags={"Продукты организации"},
 *     security={{"bearerAuth": {}}},
 *     @OA\Parameter(
 *         name="productId",
 *         in="path",
 *         description="ID товара",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 required={"organization_id"},
 *                 @OA\Property(property="organization_id", type="integer", example=1)
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Product deleted successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Product deleted successfully")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validation error",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Validation errors"),
 *             @OA\Property(property="errors", type="object")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Product not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Product not found")
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Forbidden",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Product does not belong to this organization")
 *         )
 *     )
 * )
 */
  public function deleteProduct(Request $request, $productId){}

}