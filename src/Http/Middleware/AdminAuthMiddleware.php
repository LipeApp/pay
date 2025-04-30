<?php

namespace UzPaymentGateways\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class AdminAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Проверяем, включена ли админ-панель
        if (!Config::get('payment-gateways.admin.enabled')) {
            abort(404);
        }

        // Проверяем, включена ли авторизация
        if (!Config::get('payment-gateways.admin.auth.enabled')) {
            return $next($request);
        }

        // Проверяем, можно ли пропустить авторизацию на локальном окружении
        if (Config::get('payment-gateways.admin.auth.local_bypass') && app()->environment('local')) {
            return $next($request);
        }

        // Проверяем авторизацию
        if (!Auth::check()) {
            return redirect()->route('admin.login');
        }

        // Проверяем, есть ли пользователь в списке разрешенных
        $allowedUsers = Config::get('payment-gateways.admin.auth.users', []);
        $user = Auth::user();

        foreach ($allowedUsers as $allowedUser) {
            if ($user->email === $allowedUser['email']) {
                return $next($request);
            }
        }

        abort(403, 'Unauthorized access to admin panel');
    }
} 