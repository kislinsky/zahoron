<?php

namespace App\Http\Controllers\Swagger\Account\Cashier;

use App\Http\Controllers\Controller;


class CashierController extends Controller
{

    /**
     * @OA\Get(
     *     path="/app/cashier/account/cashier/cemeteries",
     *     summary="Список кладбищ в которых работает организация",
     *     tags={"Кассир лк"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="title", type="string", example="Central Cemetery"),
     *                     @OA\Property(property="city_id", type="integer", example=5),
     *                     @OA\Property(property="adres", type="string", example="Main Street 123")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function getCemeteries(){}


    /**
     * @OA\Get(
     *     path="/app/cashier/account/cashier/morgues",
     *     summary="Список моргов округа этой организации",
     *     tags={"Кассир лк"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="City Morgue"),
     *                     @OA\Property(property="city_id", type="integer", example=5),
     *                     @OA\Property(property="adres", type="string", example="Hospital Street 45")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function getMorgues(){}

 /**
     * @OA\Get(
     *     path="/app/cashier/account/cashier/call-stats",
     *     summary="Получить звонки органиазции",
     *     tags={"Кассир лк"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="date_from",
     *         in="query",
     *         description="Start date for filtering (format: Y-m-d)",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="date_to",
     *         in="query",
     *         description="End date for filtering (format: Y-m-d)",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="city", type="string", example="Москва"),
     *                     @OA\Property(property="call_status", type="string", example="answered"),
     *                     @OA\Property(property="duration", type="integer", example=120),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-15T10:30:00Z")
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="total_calls", type="integer", example=150),
     *                 @OA\Property(property="avg_duration", type="number", format="float", example=45.5)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function getCallStats(){}

}