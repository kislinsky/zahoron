<?php

namespace App\Http\Controllers\Swagger;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;




 class AuthController extends Controller
 {
     /**
      * @OA\Post(
      *     path="/v1/register",
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
      *     path="/v1/confirm-info",
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
      *     path="/v1/confirm-phone",
      *     summary="Подтверждение телефона по коду из SMS",
      *     tags={"Авторизация/регистрация"},
      *     @OA\RequestBody(
      *         required=true,
      *         @OA\JsonContent(
      *             required={"reg_id", "code"},
      *             @OA\Property(property="reg_id", type="string", format="uuid", example="550e8400-e29b-41d4-a716-446655440000"),
      *             @OA\Property(property="code", type="string", example="123456", maxLength=6, minLength=6)
      *         )
      *     ),
      *     @OA\Response(
      *         response=200,
      *         description="Успешное подтверждение",
      *         @OA\JsonContent(
      *             @OA\Property(property="jwt", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...")
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
     public function confirmPhone(Request $request)
     {
         // ... существующий код
     }
 
     /**
      * @OA\Post(
      *     path="/v1/auth",
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
      *     path="/v1/auth/confirm",
      *     summary="Подтверждение авторизации по коду из SMS",
      *     tags={"Авторизация/регистрация"},
      *     @OA\RequestBody(
      *         required=true,
      *         @OA\JsonContent(
      *             required={"reg_id", "code"},
      *             @OA\Property(property="reg_id", type="string", format="uuid", example="550e8400-e29b-41d4-a716-446655440000"),
      *             @OA\Property(property="code", type="string", example="123456", maxLength=6, minLength=6)
      *         )
      *     ),
      *     @OA\Response(
      *         response=200,
      *         description="Успешная авторизация",
      *         @OA\JsonContent(
      *             @OA\Property(property="jwt", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...")
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
     public function authConfirm(Request $request)
     {
         // ... существующий код
     }
 }