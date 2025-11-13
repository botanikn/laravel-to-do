<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use App\Constants\HttpStatus;
use App\Http\Resources\ErrorResource;

class CheckApiToken
{
    public function handle($request, Closure $next)
    {
        $authHeader = $request->header('Authorization');

        if (!$authHeader) {
            return new ErrorResource(message: 'Не предоставлен токен', statusCode: HttpStatus::UNAUTHORIZED);
        }

        // Проверяем, что заголовок не начинается с 'Bearer '
        if (!str_starts_with($authHeader, 'Bearer ')) {
            return new ErrorResource(message: 'Некорректный формат токена', statusCode: HttpStatus::BAD_REQUEST);
        }

        // Извлекаем токен из заголовка
        $token = trim(substr($authHeader, 7)); // длина 'Bearer ' = 7

        if (!$token) {
            return new ErrorResource(message: 'Пустой токен', statusCode: HttpStatus::UNAUTHORIZED);
        }

        $user = User::where('api_token', $token)->first();

        if (!$user) {
            return new ErrorResource(message: 'Пользователь не найден или токен невалиден', statusCode: HttpStatus::UNAUTHORIZED);
        }

        $request->setUserResolver(fn() => $user);

        return $next($request);
    }
}
