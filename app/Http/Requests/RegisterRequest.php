<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Constants\HttpStatus;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'     => 'required|string|unique:users,name',
            'email'    => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:6',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Поле name обязательно для заполнения.',
            'name.unique' => 'Пользователь с таким именем уже существует.',
            'email.required' => 'Поле email обязательно для заполнения.',
            'email.email' => 'Введите корректный email.',
            'email.unique' => 'Пользователь с таким email уже существует.',
            'password.required' => 'Поле password обязательно для заполнения.',
            'password.min' => 'Пароль должен быть не менее 6 символов.',
        ];
    }
}
