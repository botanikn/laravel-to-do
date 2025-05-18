<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Constants\HttpStatus;
use App\Http\Resources\UserResource;
use App\Http\Resources\ErrorResource;
use App\Http\Requests\RegisterRequest;

class RegisterController extends Controller
{
    public function register(RegisterRequest $request)
        {
            try {
                $api_token =  Hash::make(Str::random(60));
            
                $user = User::create([
                    'name'      => $request->name,
                    'email'     => $request->email,
                    'password'  => Hash::make($request->password),
                    'api_token' => $api_token,
                ]);
            
                return new UserResource($user, message: 'Регистрация успешна', statusCode: HttpStatus::CREATED);
            }
            catch (\Exception $e) {
                return new ErrorResource(message: $e->getMessage(), statusCode: HttpStatus::INTERNAL_SERVER_ERROR);
            }
            
        }
}
