<?php

namespace App\Http\Requests\V2\Profile;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAvatarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'avatar' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'avatar.required' => 'Файл аватара обязателен',
            'avatar.image' => 'Файл должен быть изображением',
            'avatar.mimes' => 'Допустимые форматы: jpeg, png, jpg, webp',
            'avatar.max' => 'Размер файла не должен превышать 2MB',
        ];
    }
}