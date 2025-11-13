<?php

namespace App\Http\Requests;

class TagRequest extends CustomFromRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|min:3|max:20',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Поле title обязательно для заполнения.',
            'title.string' => 'Поле title должно быть string.',
            'title.min' => 'Поле title не может содержать меньше 3 символов.',
            'title.max' => 'Поле title не может содержать больше 20 символов.',
        ];
    }
}
