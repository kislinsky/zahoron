<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Symfony\Component\HttpFoundation\Response;

class CheckCashierRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Пользователь не найден'
                ], 401);
            }

            if ($user->role !== 'cashier') {
                return response()->json([
                    'success' => false,
                    'message' => 'Доступ запрещен. Требуется роль кассира'
                ], 403);
            }

            return $next($request);

        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Недействительный токен'
            ], 401);
        }
    }
}