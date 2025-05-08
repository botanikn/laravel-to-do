<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use App\Constants\HttpStatus;

class CheckApiToken
{
    public function handle($request, Closure $next)
    {
        $token = $request->header('Authorization');

        if (!$token) {
            return response()->json(
                ['message' => 'Не предоставлен токен'],
                HttpStatus::UNAUTHORIZED
            );
        }

        $token = trim($token);

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
