<?php

namespace App\Http\Controllers\Swagger\Account\Cashier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthCashierController extends Controller
{
    /**
     * @OA\Post(
     *     path="/app/cashier/auth/init",
     *     summary="Инициализация авторизации кассира",
     *     description="Отправляет SMS с кодом подтверждения на телефон кассира",
     *     tags={"Авторизация кассира"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"phone"},
     *             @OA\Property(property="phone", type="string", example="+79991234567", description="Номер телефона кассира")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="SMS отправлено успешно",
     *         @OA\JsonContent(
     *             @OA\Property(property="reg_id", type="string", format="uuid", example="550e8400-e29b-41d4-a716-446655440000"),
     *             @OA\Property(property="message", type="string", example="SMS с кодом отправлено")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Ошибка валидации",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="object", example={"phone": {"Поле phone обязательно для заполнения."}})
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Пользователь не найден или не соответствует требованиям",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Пользователь с таким номером телефона не найден")
     *         )
     *     )
     * )
     */
    public function authInit(){}

    /**
     * @OA\Post(
     *     path="/app/cashier/auth/confirm",
     *     summary="Подтверждение SMS кода",
     *     description="Подтверждает SMS код и завершает авторизацию кассира",
     *     tags={"Авторизация кассира"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"reg_id", "code"},
     *             @OA\Property(property="reg_id", type="string", format="uuid", example="550e8400-e29b-41d4-a716-446655440000"),
     *             @OA\Property(property="code", type="string", example="123456", description="6-значный SMS код")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Успешная авторизация",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Успешная авторизация"),
     *             @OA\Property(
     *                 property="auth_data",
     *                 type="object",
     *                 @OA\Property(property="access_token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."),
     *                 @OA\Property(property="token_type", type="string", example="bearer"),
     *                 @OA\Property(property="expires_in", type="integer", example=3600)
     *             ),
     *             @OA\Property(
     *                 property="user",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Иван Иванов"),
     *                 @OA\Property(property="phone", type="string", example="+79991234567"),
     *                 @OA\Property(property="email", type="string", example="ivan@example.com")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Неверный код или код истек",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Неверный код")
     *         )
     *     )
     * )
     */
    public function authConfirm(){}



    
/**
     * @OA\Post(
     *     path="/app/cashier/validate-token",
     *     summary="Проверка JWT токена из тела запроса",
     *     tags={"Авторизация кассира"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"token"},
     *             @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Токен действителен",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Токен действителен"),
     *             @OA\Property(property="user", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Иван Иванов"),
     *                 @OA\Property(property="email", type="string", example="user@example.com"),
     *                 @OA\Property(property="role", type="string", example="organization")
     *             ),
     *             @OA\Property(property="expires_at", type="string", example="2024-12-31 23:59:59")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Токен недействителен",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Токен истек"),
     *             @OA\Property(property="errors", type="object", example=null)
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Ошибка валидации",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Токен обязателен"),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="token", type="array",
     *                     @OA\Items(type="string", example="Поле token обязательно.")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function checkJwtToken(Request $request){}

    /**
     * @OA\Get(
     *     path="/app/cashier/validate-token-header",
     *     summary="Проверка JWT токена из заголовка Authorization",
     *     tags={"Авторизация кассира"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Токен действителен",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="user", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Иван Иванов"),
     *                 @OA\Property(property="email", type="string", example="user@example.com"),
     *                 @OA\Property(property="role", type="string", example="cashier"),
     *                 @OA\Property(property="created_at", type="string", example="2024-01-01T00:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", example="2024-01-01T00:00:00.000000Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Токен недействителен или отсутствует",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Токен не предоставлен")
     *         )
     *     )
     * )
     */
    public function checkJwtTokenFromHeader(Request $request){}

}