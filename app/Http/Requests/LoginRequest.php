<?php

namespace App\Http\Requests;

class LoginRequest extends CustomFromRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email'     => 'required|string',
            'password' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'Поле email обязательно для заполнения.',
            'password.required' => 'Поле email обязательно для заполнения.',
        ];
    }
}
