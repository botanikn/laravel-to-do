<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Constants\HttpStatus;

class RegisterController extends Controller
{
    public function register(Request $request)
        {
            try {
                $request->validate([
                    'name'     => 'required|string|unique:users,name',
                    'email'    => 'required|string|email|unique:users,email',
                    'password' => 'required|string|min:6',
                ]);

                $api_token =  Hash::make(Str::random(60));
            
                $user = User::create([
                    'name'      => $request->name,
                    'email'     => $request->email,
                    'password'  => Hash::make($request->password),
                    'api_token' => $api_token,
                ]);
            
                return response()->json([
                    'success' => true,
                    'token'   => $api_token,
                    'message' => 'Авторизация успешна',
                ], HttpStatus::CREATED);
            }
            catch (\Exception $e) {
                return response()->json([
                    'message' => $e->getMessage(),
                ], HttpStatus::INTERNAL_SERVER_ERROR);
            }
            
        }
}
