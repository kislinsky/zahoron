<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Пропускаем страницу логина
        if ($request->is('admin/login') || $request->is('filament/login')) {
            return $next($request);
        }

        // Проверяем авторизацию и роль для всех остальных страниц админки
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            abort(404);
        }
        
        return $next($request);
    }
}