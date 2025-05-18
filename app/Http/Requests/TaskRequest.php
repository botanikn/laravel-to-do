<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|min:3|max:20',
            'text' => 'required|string|max:200',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Поле title обязательно для заполнения.',
            'text.required' => 'Поле text обязательно для заполнения.',
            'title.min' => 'Поле title не может содержать меньше 3 символов.',
            'title.max' => 'Поле title не может содержать больше 20 символов.',
            'text.max' => 'Поле text не может содержать больше 200 символов.',
        ];
    }
}
