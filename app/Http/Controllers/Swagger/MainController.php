<?php

namespace App\Http\Controllers\Swagger;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;



/**
 * @OA\OpenApi(
 *     openapi="3.0.0",
 *     @OA\Info(
 *         title="API documentation",
 *         version="1.0"
 *     ),
 *     @OA\Server(
 *         url="/api",
 *         description="API server"
 *     )
 * )
 */
class MainController extends Controller
{
    //
}