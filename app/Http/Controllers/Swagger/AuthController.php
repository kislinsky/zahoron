<?php

namespace App\Http\Controllers\Swagger;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;




 class AuthController extends Controller
 {
     /**
      * @OA\Post(
      *     path="/app/organization/register",
      *     summary="Регистрация нового пользователя",
      *     tags={"Авторизация/регистрация"},
      *     @OA\RequestBody(
      *         required=true,
      *         @OA\JsonContent(
      *             required={"type", "inn", "phone", "form"},
      *             @OA\Property(property="type", type="string", example="Ритуальное агенство", enum={"Ритуальное агенство", "Частный агент", "Другое"}),
      *             @OA\Property(property="inn", type="string", example="123456789012", maxLength=12),
      *             @OA\Property(property="phone", type="string", example="+79991234567", maxLength=15),
      *             @OA\Property(property="form", type="string", example="ООО", enum={"ООО", "ИП", "ФЛ"})
      *         )
      *     ),
      *     @OA\Response(
      *         response=200,
      *         description="Успешная регистрация",
      *         @OA\JsonContent(
      *             @OA\Property(property="reg_id", type="string", format="uuid", example="550e8400-e29b-41d4-a716-446655440000"),
      *             @OA\Property(property="agent_name", type="string", example="ООО Ромашка"),
      *             @OA\Property(property="status", type="string", example="ACTIVE"),
      *             @OA\Property(property="okved", type="string", example="62.01")
      *         )
      *     ),
      *     @OA\Response(
      *         response=400,
      *         description="Ошибка валидации",
      *         @OA\JsonContent(
      *             @OA\Property(property="error", type="object", example={"type": {"Поле type обязательно для заполнения."}})
      *         )
      *     ),
      *     @OA\Response(
      *         response=404,
      *         description="Пользователь уже существует или ИНН не найден",
      *         @OA\JsonContent(
      *             @OA\Property(property="error", type="string", example="Пользователь с таким номером телефона или инн уже существует.")
      *         )
      *     )
      * )
      */
     public function register(Request $request)
     {
         // ... существующий код
     }
 
     /**
      * @OA\Post(
      *     path="/app/organization/register/confirm-info",
      *     summary="Подтверждение информации и отправка SMS кода",
      *     tags={"Авторизация/регистрация"},
      *     @OA\RequestBody(
      *         required=true,
      *         @OA\JsonContent(
      *             required={"reg_id"},
      *             @OA\Property(property="reg_id", type="string", format="uuid", example="550e8400-e29b-41d4-a716-446655440000")
      *         )
      *     ),
      *     @OA\Response(
      *         response=200,
      *         description="SMS отправлен",
      *         @OA\JsonContent(
      *             @OA\Property(property="message", type="string", example="SMS отправлен")
      *         )
      *     ),
      *     @OA\Response(
      *         response=400,
      *         description="Ошибка валидации",
      *         @OA\JsonContent(
      *             @OA\Property(property="error", type="object", example={"reg_id": {"Поле reg_id обязательно для заполнения."}})
      *         )
      *     )
      * )
      */
     public function confirmInfo(Request $request)
     {
         // ... существующий код
     }
 
     /**
 * @OA\Post(
 *     path="/app/organization/register/confirm-phone",
 *     summary="Подтверждение телефона по коду из SMS (завершение регистрации)",
 *     tags={"Авторизация/регистрация"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"reg_id", "code"},
 *             @OA\Property(property="reg_id", type="string", format="uuid", example="550e8400-e29b-41d4-a716-446655440000"),
 *             @OA\Property(property="code", type="string", example="123456", minLength=6, maxLength=6)
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Успешная регистрация",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="User successfully registered"),
 *             @OA\Property(
 *                 property="user",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="role", type="string", example="organization"),
 *                 @OA\Property(property="inn", type="string", example="123456789012"),
 *                 @OA\Property(property="phone", type="string", example="+79991234567"),
 *                 @OA\Property(property="form", type="string", example="organization"),
 *                 @OA\Property(property="okved", type="string", example="62.01"),
 *                 @OA\Property(property="created_at", type="string", format="date-time"),
 *                 @OA\Property(property="updated_at", type="string", format="date-time")
 *             ),
 *             @OA\Property(
 *                 property="token",
 *                 type="object",
 *                 @OA\Property(property="access_token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."),
 *                 @OA\Property(property="token_type", type="string", example="bearer"),
 *                 @OA\Property(property="expires_in", type="integer", example=3600),
 *                 @OA\Property(
 *                     property="user",
 *                     type="object",
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="role", type="string", example="organization")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Ошибка валидации или неверный код",
 *         @OA\JsonContent(
 *             oneOf={
 *                 @OA\Schema(
 *                     @OA\Property(property="error", type="object", example={"reg_id": {"Поле reg_id обязательно для заполнения."}})
 *                 ),
 *                 @OA\Schema(
 *                     @OA\Property(property="error", type="string", example="Неверный код")
 *                 ),
 *                 @OA\Schema(
 *                     @OA\Property(property="error", type="string", example="Код истек")
 *                 )
 *             }
 *         )
 *     )
 * )
 */
public function confirmPhone()
{
}
 
     /**
      * @OA\Post(
      *     path="/app/organization/auth",
      *     summary="Инициализация авторизации",
      *     tags={"Авторизация/регистрация"},
      *     @OA\RequestBody(
      *         required=true,
      *         @OA\JsonContent(
      *             required={"phone"},
      *             @OA\Property(property="phone", type="string", example="+79991234567")
      *         )
      *     ),
      *     @OA\Response(
      *         response=200,
      *         description="SMS отправлен",
      *         @OA\JsonContent(
      *             @OA\Property(property="reg_id", type="string", format="uuid", example="550e8400-e29b-41d4-a716-446655440000")
      *         )
      *     ),
      *     @OA\Response(
      *         response=400,
      *         description="Ошибка валидации",
      *         @OA\JsonContent(
      *             @OA\Property(property="error", type="object", example={"phone": {"Поле phone обязательно для заполнения."}})
      *         )
      *     )
      * )
      */
     public function authInit(Request $request)
     {
         // ... существующий код
     }
 
     /**
 * @OA\Post(
 *     path="/app/organization/auth/confirm",
 *     summary="Подтверждение авторизации по коду из SMS",
 *     tags={"Авторизация/регистрация"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"reg_id", "code"},
 *             @OA\Property(property="reg_id", type="string", format="uuid", example="550e8400-e29b-41d4-a716-446655440000"),
 *             @OA\Property(property="code", type="string", example="123456", minLength=6, maxLength=6)
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
 *                 @OA\Property(property="email", type="string", example="user@example.com"),
 *                 @OA\Property(property="role", type="string", example="organization"),
 *                 @OA\Property(property="inn", type="string", example="123456789012")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Ошибка валидации или неверный код",
 *         @OA\JsonContent(
 *             oneOf={
 *                 @OA\Schema(
 *                     @OA\Property(property="error", type="object", example={"reg_id": {"Поле reg_id обязательно для заполнения."}})
 *                 ),
 *                 @OA\Schema(
 *                     @OA\Property(property="error", type="string", example="Неверный код")
 *                 ),
 *                 @OA\Schema(
 *                     @OA\Property(property="error", type="string", example="Код истек")
 *                 )
 *             }
 *         )
 *     )
 * )
 */
public function authConfirm()
{
}


/**
 * @OA\Post(
 *     path="/app/organization/delete/{user}",
 *     summary="Удаление аккаунта пользователя",
 *     tags={"Авторизация/регистрация"},
 *     security={{"bearerAuth": {}}},
 *     @OA\Parameter(
 *         name="user",
 *         in="path",
 *         required=true,
 *         description="ID пользователя для удаления",
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Аккаунт успешно удален",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Аккаунт успешно удален")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Неавторизованный доступ",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Unauthenticated")
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Доступ запрещен",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Forbidden")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Пользователь не найден",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="User not found")
 *         )
 *     )
 * )
 */
public static function deleteAccountTest(User $user) {}
 }