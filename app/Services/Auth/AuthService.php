<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Repositories\Auth\AuthRepository;

class AuthService
{
    public function __construct(
        private readonly AuthRepository $authRepository,
    ) {
    }

    public function getUser(string $email): User
    {
        return $this->authRepository->getUserByEmail($email);
    }
}
