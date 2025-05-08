<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use App\Constants\HttpStatus;

class CheckApiToken
{
    public function handle($request, Closure $next)
    {
        $authHeader = $request->header('Authorization');

        if (!$authHeader) {
            return response()->json(
                ['message' => 'Не предоставлен токен'],
                HttpStatus::UNAUTHORIZED
            );
        }

        // Проверяем, что заголовок начинается с 'Bearer '
        if (!str_starts_with($authHeader, 'Bearer ')) {
            return response()->json(
                ['message' => 'Некорректный формат токена'],
                HttpStatus::BAD_REQUEST
            );
        }

        // Извлекаем токен из заголовка
        $token = trim(substr($authHeader, 7)); // длина 'Bearer ' = 7

        if (!$token) {
            return response()->json(
                ['message' => 'Пустой токен'],
                HttpStatus::UNAUTHORIZED
            );
        }

        $user = User::where('api_token', $token)->first();

        if (!$user) {
            return response()->json(
                ['message' => 'Пользователь не найден или токен невалиден'],
                HttpStatus::UNAUTHORIZED
            );
        }

        $request->setUserResolver(fn() => $user);

        return $next($request);
    }
}
