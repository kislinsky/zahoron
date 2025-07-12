<?php

namespace App\Http\Controllers\Swagger\Account\Agency;

use App\Http\Controllers\Controller;

use App\Models\ProductRequestToSupplier;
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
        //  существующий код метода 
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

/**
 * @OA\Post(
 *     path="/v1/account/agency/settings/update",
 *     summary="Обновление настроек организации/ИП",
 *     description="Обновляет профиль организации или индивидуального предпринимателя",
 *     tags={"Профиль организации"},
 *     security={{"bearerAuth": {}}},
 *     @OA\RequestBody(
 *         required=true,
 *         description="Данные для обновления профиля",
 *         @OA\JsonContent(
 *             oneOf={
 *                 @OA\Schema(ref="#/components/schemas/IndividualEntrepreneurUpdateRequest"),
 *                 @OA\Schema(ref="#/components/schemas/OrganizationUpdateRequest")
 *             }
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Успешное обновление профиля",
 *         @OA\JsonContent(ref="#/components/schemas/UserProfileResponse")
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Ошибки валидации",
 *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Не авторизован",
 *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedResponse")
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Ошибка сервера",
 *         @OA\JsonContent(ref="#/components/schemas/ServerErrorResponse")
 *     )
 * )
 * @OA\Schema(
 *     schema="IndividualEntrepreneurUpdateRequest",
 *     type="object",
 *     required={"user_id", "phone", "inn", "city_id", "edge_id"},
 *     @OA\Property(property="user_id", type="integer", example=1, description="ID пользователя"),
 *     @OA\Property(property="name", type="string", example="Иван", maxLength=255),
 *     @OA\Property(property="surname", type="string", example="Иванов", maxLength=255),
 *     @OA\Property(property="patronymic", type="string", example="Иванович", maxLength=255),
 *     @OA\Property(property="phone", type="string", example="+79991234567", pattern="^\+\d{11,15}$"),
 *     @OA\Property(property="address", type="string", example="ул. Ленина, 1", maxLength=500),
 *     @OA\Property(property="email", type="string", format="email", example="org@example.com", maxLength=255),
 *     @OA\Property(property="whatsapp", type="string", example="79991234567", maxLength=20),
 *     @OA\Property(property="telegram", type="string", example="@username", maxLength=50),
 *     @OA\Property(property="password", type="string", format="password", example="oldPassword123", minLength=8),
 *     @OA\Property(property="password_new", type="string", format="password", example="newPassword123", minLength=8),
 *     @OA\Property(property="password_new_2", type="string", format="password", example="newPassword123", minLength=8),
 *     @OA\Property(property="email_notifications", type="boolean", example=true),
 *     @OA\Property(property="sms_notifications", type="boolean", example=false),
 *     @OA\Property(property="language", type="integer", example=1, enum={1, 2}),
 *     @OA\Property(property="theme", type="string", example="light", enum={"light", "dark"}),
 *     @OA\Property(property="inn", type="string", example="123456789012", pattern="^\d{10,12}$"),
 *     @OA\Property(property="name_organization", type="string", example="ИП Иванов", maxLength=255),
 *     @OA\Property(property="ogrn", type="string", example="1234567890123", pattern="^\d{13}$"),
 *     @OA\Property(property="city_id", type="integer", example=1),
 *     @OA\Property(property="edge_id", type="integer", example=1)
 * )
 * @OA\Schema(
 *     schema="OrganizationUpdateRequest",
 *     type="object",
 *     required={"phone", "inn", "city_id", "edge_id", "in_face", "regulation"},
 *     @OA\Property(property="organizational_form", type="string", enum={"organization"}, example="organization"),
 *     @OA\Property(property="name", type="string", example="Иван", maxLength=255),
 *     @OA\Property(property="surname", type="string", example="Иванов", maxLength=255),
 *     @OA\Property(property="patronymic", type="string", example="Иванович", maxLength=255),
 *     @OA\Property(property="phone", type="string", example="+79991234567", pattern="^\+\d{11,15}$"),
 *     @OA\Property(property="address", type="string", example="ул. Ленина, 1", maxLength=500),
 *     @OA\Property(property="email", type="string", format="email", example="org@example.com", maxLength=255),
 *     @OA\Property(property="whatsapp", type="string", example="79991234567", maxLength=20),
 *     @OA\Property(property="telegram", type="string", example="@username", maxLength=50),
 *     @OA\Property(property="password", type="string", format="password", example="oldPassword123", minLength=8),
 *     @OA\Property(property="password_new", type="string", format="password", example="newPassword123", minLength=8),
 *     @OA\Property(property="password_new_2", type="string", format="password", example="newPassword123", minLength=8),
 *     @OA\Property(property="email_notifications", type="boolean", example=true),
 *     @OA\Property(property="sms_notifications", type="boolean", example=false),
 *     @OA\Property(property="language", type="integer", example=1, enum={1, 2}),
 *     @OA\Property(property="theme", type="string", example="light", enum={"light", "dark"}),
 *     @OA\Property(property="inn", type="string", example="123456789012", pattern="^\d{10}$"),
 *     @OA\Property(property="name_organization", type="string", example="ООО Ромашка", maxLength=255),
 *     @OA\Property(property="city_id", type="integer", example=1),
 *     @OA\Property(property="edge_id", type="integer", example=1),
 *     @OA\Property(property="in_face", type="string", example="Генеральный директор", maxLength=255),
 *     @OA\Property(property="regulation", type="string", example="Устава", maxLength=255),
 *     @OA\Property(property="kpp", type="string", example="123456789", pattern="^\d{9}$")
 * )
 * @OA\Schema(
 *     schema="UserProfileResponse",
 *     type="object",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Настройки успешно обновлены"),
 *     @OA\Property(
 *         property="data",
 *         type="object",
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="name", type="string", example="Иван"),
 *         @OA\Property(property="surname", type="string", example="Иванов"),
 *         @OA\Property(property="email", type="string", example="user@example.com"),
 *         @OA\Property(property="phone", type="string", example="+79991234567"),
 *         @OA\Property(property="organizational_form", type="string", example="ep"),
 *         @OA\Property(property="inn", type="string", example="123456789012"),
 *         @OA\Property(property="email_notifications", type="boolean", example=true),
 *         @OA\Property(property="updated_at", type="string", format="date-time")
 *     )
 * )
 * @OA\Schema(
 *     schema="ValidationErrorResponse",
 *     type="object",
 *     @OA\Property(property="success", type="boolean", example=false),
 *     @OA\Property(property="message", type="string", example="Validation errors"),
 *     @OA\Property(
 *         property="errors",
 *         type="object",
 *         @OA\Property(
 *             property="phone",
 *             type="array",
 *             @OA\Items(type="string", example="Номер телефона уже занят")
 *         )
 *     )
 * )
 * @OA\Schema(
 *     schema="UnauthorizedResponse",
 *     type="object",
 *     @OA\Property(property="success", type="boolean", example=false),
 *     @OA\Property(property="message", type="string", example="Unauthenticated")
 * )
 * @OA\Schema(
 *     schema="ServerErrorResponse",
 *     type="object",
 *     @OA\Property(property="success", type="boolean", example=false),
 *     @OA\Property(property="message", type="string", example="Ошибка при обновлении настроек")
 * )
 */
public static function settingsUserUpdate(Request $request){}


/**
 * @OA\Post(
 *     path="/v1/account/agency/organization/create",
 *     tags={"Организация"},
 *     summary="Создание новой организации",
 *     description="Создает новую организацию с категориями, рабочими часами и изображениями",
 *     operationId="createOrganization",
 *     security={{"bearerAuth": {}}},
 *     @OA\RequestBody(
 *         required=true,
 *         description="Данные организации с файлами",
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 required={"title", "city_id", "adres", "width", "longitude", "cemetery_ids[]", "img", "img_main", "user_id"},
 *                 @OA\Property(
 *                     property="title",
 *                     type="string",
 *                     maxLength=255,
 *                     example="Ритуальные услуги"
 *                 ),
 *                 @OA\Property(
 *                     property="content",
 *                     type="string",
 *                     nullable=true,
 *                     example="Профессиональные ритуальные услуги с 1990 года"
 *                 ),
 *                 @OA\Property(
 *                     property="cemetery_ids[]",
 *                     type="array",
 *                     @OA\Items(
 *                         type="integer",
 *                         example=1
 *                     )
 *                 ),
 *                 @OA\Property(
 *                     property="phone",
 *                     type="string",
 *                     maxLength=20,
 *                     nullable=true,
 *                     example="+79001234567"
 *                 ),
 *                 @OA\Property(
 *                     property="telegram",
 *                     type="string",
 *                     maxLength=50,
 *                     nullable=true,
 *                     example="@username"
 *                 ),
 *                 @OA\Property(
 *                     property="whatsapp",
 *                     type="string",
 *                     maxLength=20,
 *                     nullable=true,
 *                     example="+79001234567"
 *                 ),
 *                 @OA\Property(
 *                     property="email",
 *                     type="string",
 *                     format="email",
 *                     maxLength=255,
 *                     nullable=true,
 *                     example="info@ritual.ru"
 *                 ),
 *                 @OA\Property(
 *                     property="city_id",
 *                     type="integer",
 *                     example=1
 *                 ),
 *                 @OA\Property(
 *                     property="next_to",
 *                     type="string",
 *                     maxLength=255,
 *                     nullable=true,
 *                     example="Рядом с центральным парком"
 *                 ),
 *                 @OA\Property(
 *                     property="underground",
 *                     type="string",
 *                     maxLength=255,
 *                     nullable=true,
 *                     example="Центральная"
 *                 ),
 *                 @OA\Property(
 *                     property="adres",
 *                     type="string",
 *                     maxLength=255,
 *                     example="ул. Центральная, д. 1"
 *                 ),
 *                 @OA\Property(
 *                     property="width",
 *                     type="number",
 *                     format="float",
 *                     example=55.7558
 *                 ),
 *                 @OA\Property(
 *                     property="longitude",
 *                     type="number",
 *                     format="float",
 *                     example=37.6173
 *                 ),
 *                 @OA\Property(
 *                     property="available_installments",
 *                     type="integer",
 *                     enum={0, 1},
 *                     example=1,
 *                     description="0 - false, 1 - true"
 *                 ),
 *                 @OA\Property(
 *                     property="found_cheaper",
 *                     type="integer",
 *                     enum={0, 1},
 *                     example=0,
 *                     description="0 - false, 1 - true"
 *                 ),
 *                 @OA\Property(
 *                     property="сonclusion_contract",
 *                     type="integer",
 *                     enum={0, 1},
 *                     example=1,
 *                     description="0 - false, 1 - true"
 *                 ),
 *                 @OA\Property(
 *                     property="state_compensation",
 *                     type="integer",
 *                     enum={0, 1},
 *                     example=0,
 *                     description="0 - false, 1 - true"
 *                 ),
 *                 @OA\Property(
 *                     property="user_id",
 *                     type="integer",
 *                     example=1
 *                 ),
 *                 @OA\Property(
 *                     property="categories_organization[]",
 *                     type="array",
 *                     @OA\Items(
 *                         type="integer",
 *                         example=29
 *                     )
 *                 ),
 *                 @OA\Property(
 *                     property="price_cats_organization[]",
 *                     type="array",
 *                     @OA\Items(
 *                         type="number",
 *                         format="float",
 *                         example=100
 *                     )
 *                 ),
 *                 @OA\Property(
 *                     property="working_day[]",
 *                     type="array",
 *                     minItems=7,
 *                     maxItems=7,
 *                     @OA\Items(
 *                         type="string",
 *                         example="09:00 - 18:00"
 *                     )
 *                 ),
 *                 @OA\Property(
 *                     property="holiday_day[]",
 *                     type="array",
 *                     @OA\Items(
 *                         type="string",
 *                         enum={"Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"},
 *                         example="Sunday"
 *                     )
 *                 ),
 *                 @OA\Property(
 *                     property="img",
 *                     type="string",
 *                     format="binary",
 *                     description="Основное изображение организации (jpeg, jpg, png, max 5MB)"
 *                 ),
 *                 @OA\Property(
 *                     property="img_main",
 *                     type="string",
 *                     format="binary",
 *                     description="Дополнительное изображение организации (jpeg, jpg, png, max 5MB)"
 *                 ),
 *                 @OA\Property(
 *                     property="images[]",
 *                     type="array",
 *                     maxItems=5,
 *                     @OA\Items(
 *                         type="string",
 *                         format="binary",
 *                         description="Дополнительные изображения (jpeg, jpg, png, max 5MB каждая)"
 *                     )
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Организация создана",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Организация отправлена на модерацию"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="title", type="string", example="Ритуальные услуги"),
 *                 @OA\Property(property="status", type="integer", example=0),
 *                 @OA\Property(property="content", type="string", example="Профессиональные ритуальные услуги с 1990 года"),
 *                 @OA\Property(property="phone", type="string", example="+79001234567"),
 *                 @OA\Property(property="telegram", type="string", example="@username"),
 *                 @OA\Property(property="user_id", type="integer", example=1),
 *                 @OA\Property(property="img_file", type="string", example="uploads_organization/abc123.jpg"),
 *                 @OA\Property(property="img_main_file", type="string", example="uploads_organization/def456.jpg"),
 *                 @OA\Property(property="whatsapp", type="string", example="+79001234567"),
 *                 @OA\Property(property="email", type="string", example="info@ritual.ru"),
 *                 @OA\Property(property="city_id", type="integer", example=1),
 *                 @OA\Property(property="slug", type="string", example="ritualnye-uslugi"),
 *                 @OA\Property(property="next_to", type="string", example="Рядом с центральным парком"),
 *                 @OA\Property(property="underground", type="string", example="Центральная"),
 *                 @OA\Property(property="adres", type="string", example="ул. Центральная, д. 1"),
 *                 @OA\Property(property="width", type="number", format="float", example=55.7558),
 *                 @OA\Property(property="longitude", type="number", format="float", example=37.6173),
 *                 @OA\Property(property="available_installments", type="integer", example=1),
 *                 @OA\Property(property="found_cheaper", type="integer", example=0),
 *                 @OA\Property(property="state_compensation", type="integer", example=0),
 *                 @OA\Property(property="сonclusion_contract", type="integer", example=1),
 *                 @OA\Property(
 *                     property="cemetery_ids",
 *                     type="array",
 *                     @OA\Items(
 *                         type="integer",
 *                         example=1
 *                     )
 *                 ),
 *                 @OA\Property(
 *                     property="activityCategories",
 *                     type="array",
 *                     @OA\Items(
 *                         type="object",
 *                         @OA\Property(property="id", type="integer", example=1),
 *                         @OA\Property(property="category_id", type="integer", example=29),
 *                         @OA\Property(property="price", type="number", example=100)
 *                     )
 *                 ),
 *                 @OA\Property(
 *                     property="working_hours",
 *                     type="array",
 *                     @OA\Items(
 *                         type="object",
 *                         @OA\Property(property="day", type="string", example="Monday"),
 *                         @OA\Property(property="time_start_work", type="string", example="09:00"),
 *                         @OA\Property(property="time_end_work", type="string", example="18:00"),
 *                         @OA\Property(property="holiday", type="integer", example=0)
 *                     )
 *                 ),
 *                 @OA\Property(
 *                     property="images",
 *                     type="array",
 *                     @OA\Items(
 *                         type="object",
 *                         @OA\Property(property="id", type="integer", example=1),
 *                         @OA\Property(property="img_file", type="string", example="uploads_organization/ghi789.jpg")
 *                     )
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Ошибки валидации",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Ошибки валидации"),
 *             @OA\Property(
 *                 property="errors",
 *                 type="object",
 *                 example={
 *                     "title": {"Поле title обязательно для заполнения"},
 *                     "cemetery_ids": {"Необходимо выбрать хотя бы одно кладбище"}
 *                 }
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Ошибка сервера",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Ошибка при создании организации"),
 *             @OA\Property(property="error", type="string", example="Детали ошибки")
 *         )
 *     )
 * )
 */
public static function createOrganization(Request $request) {}


/**
 * @OA\Post(
 *     path="/v1/account/agency/organization/update",
 *     summary="Обновление данных организации",
 *     tags={"Организация"},
 *     security={{"bearerAuth": {}}},
 *     @OA\RequestBody(
 *         required=true,
 *         description="Данные для обновления организации",
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 required={"id"},
 *                 @OA\Property(property="id", type="integer", description="ID организации (обязательное поле)"),
 *                 @OA\Property(
 *                     property="title",
 *                     type="string",
 *                     maxLength=255,
 *                     description="Название организации"
 *                 ),
 *                 @OA\Property(
 *                     property="content",
 *                     type="string",
 *                     description="Описание организации"
 *                 ),
 *                 @OA\Property(
 *                     property="phone",
 *                     type="string",
 *                     maxLength=20,
 *                     description="Телефон организации"
 *                 ),
 *                 @OA\Property(
 *                     property="telegram",
 *                     type="string",
 *                     maxLength=50,
 *                     description="Телеграм контакт"
 *                 ),
 *                 @OA\Property(
 *                     property="whatsapp",
 *                     type="string",
 *                     maxLength=20,
 *                     description="Whatsapp контакт"
 *                 ),
 *                 @OA\Property(
 *                     property="email",
 *                     type="string",
 *                     format="email",
 *                     maxLength=255,
 *                     description="Email организации"
 *                 ),
 *                 @OA\Property(
 *                     property="city_id",
 *                     type="integer",
 *                     description="ID города"
 *                 ),
 *                 @OA\Property(
 *                     property="next_to",
 *                     type="string",
 *                     maxLength=255,
 *                     description="Рядом с"
 *                 ),
 *                 @OA\Property(
 *                     property="underground",
 *                     type="string",
 *                     maxLength=255,
 *                     description="Ближайшее метро"
 *                 ),
 *                 @OA\Property(
 *                     property="adres",
 *                     type="string",
 *                     maxLength=255,
 *                     description="Адрес организации"
 *                 ),
 *                 @OA\Property(
 *                     property="width",
 *                     type="string",
 *                     maxLength=50,
 *                     description="Широта"
 *                 ),
 *                 @OA\Property(
 *                     property="longitude",
 *                     type="string",
 *                     maxLength=50,
 *                     description="Долгота"
 *                 ),
 *                 @OA\Property(
 *                     property="img",
 *                     type="string",
 *                     format="binary",
 *                     description="Основное изображение (макс. 2MB)"
 *                 ),
 *                 @OA\Property(
 *                     property="img_main",
 *                     type="string",
 *                     format="binary",
 *                     description="Главное изображение (макс. 2MB)"
 *                 ),
 *                 @OA\Property(
 *                     property="cemetery_ids[]",
 *                     type="array",
 *                     @OA\Items(type="integer"),
 *                     description="Массив ID кладбищ"
 *                 ),
 *                 @OA\Property(
 *                     property="images[]",
 *                     type="array",
 *                     @OA\Items(
 *                         oneOf={
 *                             @OA\Schema(type="string", format="binary"),
 *                             @OA\Schema(type="string", description="URL изображения"),
 *                             @OA\Schema(type="string", description="Base64 изображения")
 *                         }
 *                     ),
 *                     description="Массив изображений (файлы, URL или base64)"
 *                 ),
 *                 @OA\Property(
 *                     property="categories_organization[]",
 *                     type="array",
 *                     @OA\Items(type="integer"),
 *                     description="Массив ID категорий"
 *                 ),
 *                 @OA\Property(
 *                     property="price_cats_organization[]",
 *                     type="array",
 *                     @OA\Items(type="number", format="float"),
 *                     description="Массив цен для категорий"
 *                 ),
 *                 @OA\Property(
 *                     property="working_day[]",
 *                     type="array",
 *                     @OA\Items(type="string", example="09:00 - 18:00"),
 *                     description="Рабочие часы по дням недели"
 *                 ),
 *                 @OA\Property(
 *                     property="holiday_day[]",
 *                     type="array",
 *                     @OA\Items(type="string", enum={"Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"}),
 *                     description="Выходные дни"
 *                 ),
 *                 @OA\Property(
 *                     property="available_installments",
 *                     type="integer",
 *                     enum={0, 1},
 *                     description="Доступна рассрочка: 0 - нет, 1 - да"
 *                 ),
 *                 @OA\Property(
 *                     property="found_cheaper",
 *                     type="integer",
 *                     enum={0, 1},
 *                     description="Нашли дешевле: 0 - нет, 1 - да"
 *                 ),
 *                 @OA\Property(
 *                     property="conclusion_contract",
 *                     type="integer",
 *                     enum={0, 1},
 *                     description="Заключение договора: 0 - нет, 1 - да"
 *                 ),
 *                 @OA\Property(
 *                     property="state_compensation",
 *                     type="integer",
 *                     enum={0, 1},
 *                     description="Государственная компенсация: 0 - нет, 1 - да"
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Успешное обновление",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Организация успешно обновлена"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer"),
 *                 @OA\Property(property="title", type="string"),
 *                 @OA\Property(property="content", type="string"),
 *                 @OA\Property(property="phone", type="string"),
 *                 @OA\Property(property="email", type="string"),
 *                 @OA\Property(property="city_id", type="integer"),
 *                 @OA\Property(property="adres", type="string"),
 *                 @OA\Property(property="width", type="string"),
 *                 @OA\Property(property="longitude", type="string"),
 *                 @OA\Property(property="img_file", type="string"),
 *                 @OA\Property(property="img_main_file", type="string"),
 *                 @OA\Property(
 *                     property="images",
 *                     type="array",
 *                     @OA\Items(
 *                         @OA\Property(property="id", type="integer"),
 *                         @OA\Property(property="img_file", type="string"),
 *                         @OA\Property(property="img_url", type="string"),
 *                         @OA\Property(property="href_img", type="integer")
 *                     )
 *                 ),
 *                 @OA\Property(
 *                     property="workingHours",
 *                     type="array",
 *                     @OA\Items(
 *                         @OA\Property(property="day", type="string"),
 *                         @OA\Property(property="time_start_work", type="string"),
 *                         @OA\Property(property="time_end_work", type="string"),
 *                         @OA\Property(property="holiday", type="integer")
 *                     )
 *                 ),
 *                 @OA\Property(
 *                     property="activityCategories",
 *                     type="array",
 *                     @OA\Items(
 *                         @OA\Property(property="category_main_id", type="integer"),
 *                         @OA\Property(property="category_children_id", type="integer"),
 *                         @OA\Property(property="price", type="number")
 *                     )
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Ошибка валидации",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Ошибка валидации"),
 *             @OA\Property(
 *                 property="errors",
 *                 type="object",
 *                 additionalProperties={
 *                     @OA\Property(type="array", @OA\Items(type="string"))
 *                 }
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Ошибка сервера",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Ошибка сервера")
 *         )
 *     )
 * )
 */
public static function update(Request $request) {}





/**
 * @OA\Post(
 *     path="/v1/account/agency/organization-provider/create-requests-cost",
 *     summary="Создание заявки на расчет стоимости",
 *     tags={"Заявки от организаций поставщикам"},
 *     security={{"bearerAuth": {}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"organization_id", "user_id", "products", "count"},
 *             @OA\Property(property="organization_id", type="integer", example=1),
 *             @OA\Property(property="user_id", type="integer", example=1),
 *             @OA\Property(
 *                 property="products", 
 *                 type="array",
 *                 @OA\Items(type="integer", example=1)
 *             ),
 *             @OA\Property(
 *                 property="count", 
 *                 type="array",
 *                 @OA\Items(type="integer", example=1)
 *             ),
 *             @OA\Property(
 *                 property="lcs",
 *                 type="array",
 *                 nullable=true,
 *                 @OA\Items(type="string", example="1")
 *             ),
 *             @OA\Property(
 *                 property="all_lcs",
 *                 type="boolean",
 *                 nullable=true
 *             )
 *         )
 *     ),
 *     @OA\Response(response=201, description="Успешно создано"),
 *     @OA\Response(response=422, description="Ошибка валидации")
 * )
 */

public static function addRequestsCostProductSuppliers(Request $request)
{
    //  ваш существующий код 
}


/**
 * @OA\Delete(
 *     path="/v1/account/agency/organization-provider/delete-requests-cost/{request}",
 *     summary="Удаление заявки на стоимость товара поставщика",
 *     tags={"Заявки от организаций поставщикам"},
 *     @OA\Parameter(
 *         name="request",
 *         in="path",
 *         required=true,
 *         description="ID заявки",
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Заявка успешно удалена",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="success",
 *                 type="boolean",
 *                 example=true
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Заявка успешно удалена."
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Заявка не найдена",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="No query results for model [App\\Models\\RequestsCostProductsSupplier] {id}"
 *             )
 *         )
 *     )
 * )
 */


 public function deleteRequestCostProductProvider($request){}

/**
 * @OA\Post(
 *     path="/v1/account/agency/organization-provider/offer/add",
 *     summary="Заявка на создание товара",
 *     description="Создание запроса товара",
 *     tags={"Заявки от организаций поставщикам"},
 *     security={{"bearerAuth": {}}},
 *     @OA\RequestBody(
 *         required=true,
 *         description="Offer data with images",
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 required={"title", "content", "images[]"},
 *                 @OA\Property(
 *                     property="title",
 *                     type="string",
 *                     example="Request for 50 wooden caskets",
 *                     maxLength=255,
 *                     description="Title of the offer"
 *                 ),
 *                 @OA\Property(
 *                     property="content",
 *                     type="string",
 *                     example="We need 50 high-quality wooden caskets by next month",
 *                     description="Detailed description of the offer"
 *                 ),
 *                 @OA\Property(
 *                     property="images[]",
 *                     type="array",
 *                     @OA\Items(
 *                         type="string",
 *                         format="binary"
 *                     ),
 *                     maxItems=5,
 *                     minItems=1,
 *                     description="Array of image files (1-5 images allowed)"
 *                 ),
 *                 @OA\Property(
 *                     property="category_id",
 *                     type="integer",
 *                     nullable=true,
 *                     example=1,
 *                     description="Category ID (optional)"
 *                 ),
 * 
 *              @OA\Property(
 *                     property="delivery_required",
 *                     type="integer",
 *                     nullable=true,
 *                     example=1,
 *                     description="Whether delivery is required (optional)"
 *                 ),
 *                
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Offer created successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Offer created successfully"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="title", type="string", example="Request for 50 wooden caskets"),
 *                 @OA\Property(property="content", type="string", example="We need 50 high-quality wooden caskets by next month"),
 *                 @OA\Property(
 *                     property="images",
 *                     type="string",
 *                     example="[\u0022filename1.jpeg\u0022,\u0022filename2.jpeg\u0022]",
 *                     description="JSON encoded array of image filenames"
 *                 ),
 *                 @OA\Property(property="organization_id", type="integer", example=5),
 *                 @OA\Property(property="category_id", type="integer", nullable=true, example=3),
 *                 @OA\Property(property="delivery_required", type="boolean", example=true),
 *                 @OA\Property(
 *                     property="status",
 *                     type="string",
 *                     example="pending",
 *                     enum={"pending", "accepted", "rejected", "completed"}
 *                 ),
 *                 @OA\Property(property="created_at", type="string", format="date-time"),
 *                 @OA\Property(property="updated_at", type="string", format="date-time")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validation error",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(
 *                 property="errors",
 *                 type="object",
 *                 example={
 *                     "title": {"The title field is required."},
 *                     "images": {"The images must have between 1 and 5 items."}
 *                 }
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Unauthenticated.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Organization not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Organization not found for this user")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Image upload error",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="You must upload between 1 and 5 images")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Server error",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Failed to create offer"),
 *             @OA\Property(property="error", type="string", example="Error message details")
 *         )
 *     )
 * )
 */
public function createProviderOffer(Request $request)
{
    // Method implementation
}


/**
 * @OA\Delete(
 *     path="/v1/account/agency/organization-provider/offer/{id}/delete",
 *     summary="Удаление заявки на создание товара",
 *     description="Удаление запроса товара поставщику по идентификатору",
 *     tags={"Удаление заявки от организаций поставщикам"},
 *     security={{"bearerAuth": {}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="Идентификатор заявки на товар",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Заявка успешно удалена",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Product request to supplier deleted successfully.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Заявка не найдена",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="No query results for model [App\Models\ProductRequestToSupplier] {id}")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Ошибка при удалении заявки",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Failed to delete product request to supplier."),
 *             @OA\Property(property="error", type="string", example="Error message details")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Не авторизован",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Unauthenticated.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Доступ запрещен",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Forbidden.")
 *         )
 *     )
 * )
 */
public static function deleteProviderOffer(ProductRequestToSupplier $offer)
{
    // method implementation
}



/**
 * @OA\Get(
 *     path="/v1/account/agency/reviews/organization/{id}",
 *     summary="Получение отзывов об организации",
 *     tags={"Организация: Отзывы и комментарии"},
 *     security={{"bearerAuth": {}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID организации",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Успешное получение отзывов",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="current_page", type="integer", example=1),
 *                 @OA\Property(property="data", type="array",
 *                     @OA\Items(type="object",
 *                         @OA\Property(property="id", type="integer", example=1),
 *                         @OA\Property(property="content", type="string", example="Отличная организация"),
 *                         @OA\Property(property="rating", type="integer", example=5),
 *                         @OA\Property(property="status", type="integer", example=1),
 *                         @OA\Property(property="created_at", type="string", format="date-time"),
 *                         @OA\Property(property="user", type="object",
 *                             @OA\Property(property="id", type="integer", example=1),
 *                             @OA\Property(property="name", type="string", example="Иван Иванов")
 *                         )
 *                     )
 *                 ),
 *                 @OA\Property(property="first_page_url", type="string", example="http://example.com?page=1"),
 *                 @OA\Property(property="from", type="integer", example=1),
 *                 @OA\Property(property="last_page", type="integer", example=1),
 *                 @OA\Property(property="last_page_url", type="string", example="http://example.com?page=1"),
 *                 @OA\Property(property="links", type="array",
 *                     @OA\Items(type="object",
 *                         @OA\Property(property="url", type="string", nullable=true),
 *                         @OA\Property(property="label", type="string"),
 *                         @OA\Property(property="active", type="boolean")
 *                     )
 *                 ),
 *                 @OA\Property(property="next_page_url", type="string", nullable=true),
 *                 @OA\Property(property="path", type="string", example="http://example.com"),
 *                 @OA\Property(property="per_page", type="integer", example=10),
 *                 @OA\Property(property="prev_page_url", type="string", nullable=true),
 *                 @OA\Property(property="to", type="integer", example=1),
 *                 @OA\Property(property="total", type="integer", example=1)
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Некорректный ID организации",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Некорректный ID организации")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Организация не найдена",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Организация не найдена")
 *         )
 *     )
 * )
 */
public function getOrganizationReviews($id) {}

/**
 * @OA\Get(
 *     path="/v1/account/agency/product-comments/organization/{id}",
 *     summary="Получение комментариев к товарам организации",
 *     tags={"Организация: Отзывы и комментарии"},
 *     security={{"bearerAuth": {}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID организации",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Успешное получение комментариев",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="current_page", type="integer", example=1),
 *                 @OA\Property(property="data", type="array",
 *                     @OA\Items(type="object",
 *                         @OA\Property(property="id", type="integer", example=1),
 *                         @OA\Property(property="content", type="string", example="Хороший товар"),
 *                         @OA\Property(property="status", type="integer", example=1),
 *                         @OA\Property(property="created_at", type="string", format="date-time"),
 *                         @OA\Property(property="user", type="object",
 *                             @OA\Property(property="id", type="integer", example=1),
 *                             @OA\Property(property="name", type="string", example="Иван Иванов")
 *                         ),
 *                         @OA\Property(property="product", type="object",
 *                             @OA\Property(property="id", type="integer", example=1),
 *                             @OA\Property(property="name", type="string", example="Название товара")
 *                         )
 *                     )
 *                 ),
 *                 @OA\Property(property="first_page_url", type="string", example="http://example.com?page=1"),
 *                 @OA\Property(property="from", type="integer", example=1),
 *                 @OA\Property(property="last_page", type="integer", example=1),
 *                 @OA\Property(property="last_page_url", type="string", example="http://example.com?page=1"),
 *                 @OA\Property(property="links", type="array",
 *                     @OA\Items(type="object",
 *                         @OA\Property(property="url", type="string", nullable=true),
 *                         @OA\Property(property="label", type="string"),
 *                         @OA\Property(property="active", type="boolean")
 *                     )
 *                 ),
 *                 @OA\Property(property="next_page_url", type="string", nullable=true),
 *                 @OA\Property(property="path", type="string", example="http://example.com"),
 *                 @OA\Property(property="per_page", type="integer", example=10),
 *                 @OA\Property(property="prev_page_url", type="string", nullable=true),
 *                 @OA\Property(property="to", type="integer", example=1),
 *                 @OA\Property(property="total", type="integer", example=1)
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Некорректный ID организации",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Некорректный ID организации")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Организация не найдена",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Организация не найдена")
 *         )
 *     )
 * )
 */
public function getProductComments($id) {}

/**
 * @OA\Delete(
 *     path="/v1/account/agency/reviews/{id}",
 *     summary="Удаление отзыва об организации",
 *     tags={"Организация: Отзывы и комментарии"},
 *     security={{"bearerAuth": {}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID отзыва",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Отзыв успешно удален",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Отзыв успешно удален")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Некорректный ID отзыва",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Некорректный ID отзыва")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Отзыв не найден",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Отзыв не найден")
 *         )
 *     )
 * )
 */
public function deleteReview($id) {}

/**
 * @OA\Patch(
 *     path="/v1/account/agency/reviews/{id}/approve",
 *     summary="Одобрение отзыва об организации",
 *     tags={"Организация: Отзывы и комментарии"},
 *     security={{"bearerAuth": {}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID отзыва",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Отзыв одобрен",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Отзыв одобрен"),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="content", type="string", example="Отличная организация"),
 *                 @OA\Property(property="rating", type="integer", example=5),
 *                 @OA\Property(property="status", type="integer", example=1),
 *                 @OA\Property(property="created_at", type="string", format="date-time")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Некорректный ID отзыва",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Некорректный ID отзыва")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Отзыв не найден",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Отзыв не найден")
 *         )
 *     )
 * )
 */
public function approveReview($id) {}

/**
 * @OA\Put(
 *     path="/v1/account/agency/reviews/{id}/content",
 *     summary="Обновление текста отзыва",
 *     tags={"Организация: Отзывы и комментарии"},
 *     security={{"bearerAuth": {}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID отзыва",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"content"},
 *             @OA\Property(property="content", type="string", minLength=10, maxLength=2000, example="Обновленный текст отзыва")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Отзыв обновлен",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Отзыв обновлен"),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="content", type="string", example="Обновленный текст отзыва"),
 *                 @OA\Property(property="rating", type="integer", example=5),
 *                 @OA\Property(property="status", type="integer", example=1),
 *                 @OA\Property(property="created_at", type="string", format="date-time"),
 *                 @OA\Property(property="edited_at", type="string", format="date-time")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Ошибки валидации",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="errors", type="object", example={"content": {"Поле content обязательно для заполнения."}})
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Отзыв не найден",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Отзыв не найден")
 *         )
 *     )
 * )
 */
public function updateReviewContent(Request $request, $id) {}

/**
 * @OA\Delete(
 *     path="/v1/account/agency/product-comments/{id}",
 *     summary="Удаление комментария о товаре",
 *     tags={"Организация: Отзывы и комментарии"},
 *     security={{"bearerAuth": {}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID комментария",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Комментарий успешно удален",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Комментарий успешно удален")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Некорректный ID комментария",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Некорректный ID комментария")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Комментарий не найден",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Комментарий не найден")
 *         )
 *     )
 * )
 */
public function deleteProductComment($id) {}

/**
 * @OA\Patch(
 *     path="/v1/account/agency/product-comments/{id}/approve",
 *     summary="Одобрение комментария о товаре",
 *     tags={"Организация: Отзывы и комментарии"},
 *     security={{"bearerAuth": {}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID комментария",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Комментарий одобрен",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Комментарий одобрен"),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="content", type="string", example="Хороший товар"),
 *                 @OA\Property(property="status", type="integer", example=1),
 *                 @OA\Property(property="created_at", type="string", format="date-time"),
 *                 @OA\Property(property="user_id", type="integer", example=1),
 *                 @OA\Property(property="product_id", type="integer", example=1)
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Некорректный ID комментария",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Некорректный ID комментария")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Комментарий не найден",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Комментарий не найден")
 *         )
 *     )
 * )
 */
public function approveProductComment($id) {}

/**
 * @OA\Put(
 *     path="/v1/account/agency/product-comments/{id}/content",
 *     summary="Обновление текста комментария о товаре",
 *     tags={"Организация: Отзывы и комментарии"},
 *     security={{"bearerAuth": {}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID комментария",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"content"},
 *             @OA\Property(property="content", type="string", minLength=10, maxLength=2000, example="Обновленный текст комментария")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Комментарий обновлен",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Комментарий обновлен"),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="content", type="string", example="Обновленный текст комментария"),
 *                 @OA\Property(property="status", type="integer", example=1),
 *                 @OA\Property(property="created_at", type="string", format="date-time"),
 *                 @OA\Property(property="edited_at", type="string", format="date-time"),
 *                 @OA\Property(property="user_id", type="integer", example=1),
 *                 @OA\Property(property="product_id", type="integer", example=1)
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Ошибки валидации",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="errors", type="object", example={"content": {"Поле content обязательно для заполнения."}})
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Комментарий не найден",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Комментарий не найден")
 *         )
 *     )
 * )
 */
public function updateProductCommentContent(Request $request, $id) {}


/**
 * @OA\Post(
 *     path="/v1/account/agency/reviews/{id}/response",
 *     summary="Добавление ответа организации на отзыв",
 *     tags={"Организация: Отзывы и комментарии"},
 *     security={{"bearerAuth": {}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID отзыва",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"response"},
 *             @OA\Property(property="response", type="string", minLength=10, maxLength=2000, example="Благодарим за ваш отзыв!")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Ответ организации сохранен",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Ответ организации сохранен"),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="content", type="string", example="Отличная организация"),
 *                 @OA\Property(property="organization_response", type="string", example="Благодарим за ваш отзыв!"),
 *                 @OA\Property(property="organization_response_at", type="string", format="date-time"),
 *                 @OA\Property(property="created_at", type="string", format="date-time")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Ошибки валидации",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="errors", type="object", example={"response": {"Поле response обязательно для заполнения."}})
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Отзыв не найден",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Отзыв не найден")
 *         )
 *     )
 * )
 */
public function addOrganizationReviewResponse(Request $request, $id) {}

/**
 * @OA\Post(
 *     path="/v1/account/agency/product-comments/{id}/response",
 *     summary="Добавление ответа организации на комментарий к товару",
 *     tags={"Организация: Отзывы и комментарии"},
 *     security={{"bearerAuth": {}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID комментария",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"response"},
 *             @OA\Property(property="response", type="string", minLength=10, maxLength=2000, example="Спасибо за ваш отзыв о товаре!")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Ответ организации сохранен",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Ответ организации сохранен"),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="content", type="string", example="Хороший товар"),
 *                 @OA\Property(property="organization_response", type="string", example="Спасибо за ваш отзыв о товаре!"),
 *                 @OA\Property(property="organization_response_at", type="string", format="date-time"),
 *                 @OA\Property(property="created_at", type="string", format="date-time"),
 *                 @OA\Property(property="product_id", type="integer", example=1)
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Ошибки валидации",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="errors", type="object", example={"response": {"Поле response обязательно для заполнения."}})
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Комментарий не найден",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Комментарий не найден")
 *         )
 *     )
 * )
 */
public function addProductCommentResponse(Request $request, $id) {}
}