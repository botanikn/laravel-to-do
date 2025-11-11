<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Constants\HttpStatus;
use Illuminate\Contracts\Validation\Validator;

class CustomFromRequest extends FormRequest
{
    protected function failedValidation(Validator $validator)
    {
        $message = 'Ошибка валидации';

        throw new HttpResponseException(
            response()->json([
                'message' => $message,
                'errors' => $validator->errors()
            ], HttpStatus::UNPROCESSABLE_ENTITY)
        );
    }
}
