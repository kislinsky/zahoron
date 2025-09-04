<?php

namespace App\Http\Controllers\Swagger\Account\Cashier;

use App\Http\Controllers\Controller;
use App\Models\AuthSession;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

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
    public function authInit(Request $request){}

    /**
     * @OA\Post(
     *     path="/app/cashier/auth/confirm",
     *     summary="Подтверждение SMS кода",
     *     description="Подтверждает SMS код и отправляет звонок с кодом подтверждения",
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
     *         description="SMS код подтвержден, звонок отправлен",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="SMS код подтвержден. Ожидайте звонок с кодом подтверждения"),
     *             @OA\Property(property="reg_id", type="string", format="uuid", example="550e8400-e29b-41d4-a716-446655440000"),
     *             @OA\Property(property="call_sent", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Неверный код или ошибка валидации",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Неверный код")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Ошибка отправки звонка",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Ошибка отправки звонка")
     *         )
     *     )
     * )
     */
    public function authConfirm(Request $request){}

    /**
     * @OA\Post(
     *     path="/app/cashier/auth/confirm-call",
     *     summary="Подтверждение кода из звонка",
     *     description="Подтверждает код из звонка и завершает авторизацию кассира",
     *     tags={"Авторизация кассира"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"reg_id", "code"},
     *             @OA\Property(property="reg_id", type="string", format="uuid", example="550e8400-e29b-41d4-a716-446655440000"),
     *             @OA\Property(property="code", type="string", example="654321", description="6-значный код из звонка")
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
     *             @OA\Property(property="error", type="string", example="Неверный код из звонка")
     *         )
     *     )
     * )
     */
    public function authConfirmCall(Request $request){}
}