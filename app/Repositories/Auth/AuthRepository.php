<?php

namespace App\Repositories\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthRepository
{
    public function getUserByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function createUser(mixed $data): User
    {
        return User::create([
            'name'      => $data->name,
            'email'     => $data->email,
            'password'  => Hash::make($data->password),
            'api_token' => $data->api_token,
        ]);
    }
}
