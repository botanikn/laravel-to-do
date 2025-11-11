<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\AuthService;
use App\Constants\HttpStatus;
use App\Http\Resources\UserResource;
use App\Http\Resources\ErrorResource;
use App\Http\Requests\RegisterRequest;

class RegisterController extends Controller
{
    public function __construct(
        private AuthService $authService
    )
    {
    }
    public function register(RegisterRequest $request)
    {
        try {
            $user = $this->authService->createUser($request);

            return new UserResource($user, message: 'Регистрация успешна', statusCode: HttpStatus::CREATED);
        }
        catch (\Exception $e) {
            return new ErrorResource(message: $e->getMessage(), statusCode: HttpStatus::INTERNAL_SERVER_ERROR);
        }

    }
}
