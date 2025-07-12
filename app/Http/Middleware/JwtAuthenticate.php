<?php

namespace App\Http\Middleware;

use Closure;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class JwtAuthenticate
{
    public function handle($request, Closure $next)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            
            if (!$user) {
                return response()->json(['error' => 'Пользователь не найден'], 404);
            }
            
            // Проверка роли пользователя
            if ($user->role !== 'organization') {
                return response()->json(['error' => 'Доступ запрещён. Требуется роль organization.'], 403);
            }
            
        } catch (JWTException $e) {
            return response()->json(['error' => 'Неавторизованный доступ'], 401);
        }

        return $next($request);
    }
}