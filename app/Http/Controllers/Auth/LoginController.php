<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Constants\HttpStatus;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'name'    => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('name', $request->name)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Неверный логин или пароль'
            ], HttpStatus::UNAUTHORIZED);
        }

        return response()->json([
            'success' => true,
            'token'   => $user->api_token,
            'message' => 'Авторизация успешна',
        ], HttpStatus::OK);
    }
}
