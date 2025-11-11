<?php

namespace App\Repositories\Auth;

use App\Models\User;

class AuthRepository
{
    public function getUserByEmail(string $email): User
    {
        return User::where('email', $email)->first();
    }
}
