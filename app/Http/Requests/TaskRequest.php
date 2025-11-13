<?php

namespace App\Http\Requests;

class TaskRequest extends CustomFromRequest
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
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Поле title обязательно для заполнения.',
            'title.string' => 'Поле title должно быть string.',
            'title.min' => 'Поле title не может содержать меньше 3 символов.',
            'title.max' => 'Поле title не может содержать больше 20 символов.',
            'text.required' => 'Поле text обязательно для заполнения.',
            'text.max' => 'Поле text не может содержать больше 200 символов.',
            'text.string' => 'Поле text должно быть string.',
            'tags.*.exists' => 'Среди тэгов имеются несуществующие.'
        ];
    }
}
