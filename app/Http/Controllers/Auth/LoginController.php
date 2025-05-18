<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Constants\HttpStatus;
use App\Http\Resources\UserResource;
use App\Http\Resources\ErrorResource;
use App\Http\Requests\LoginRequest;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $user = User::where('name', $request->name)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return new ErrorResource(message: 'Неверный логин или пароль', statusCode: HttpStatus::UNAUTHORIZED);
        }

        return new UserResource($user, message: 'Авторизация успешна', statusCode: HttpStatus::OK);
    }
}
