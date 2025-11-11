<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Repositories\Auth\AuthRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthService
{
    public function __construct(
        private readonly AuthRepository $authRepository,
    ) {
    }

    public function getUser(string $email): ?User
    {
        return $this->authRepository->getUserByEmail($email);
    }

    public function createUser(mixed $data): User
    {
        $api_token =  Hash::make(Str::random(60));
        $data['api_token'] = $api_token;

        return $this->authRepository->createUser($data);
    }
}
