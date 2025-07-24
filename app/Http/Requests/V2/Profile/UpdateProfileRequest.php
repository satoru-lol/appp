<?php

namespace App\Http\Requests\V2\Profile;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20|regex:/^\+?[1-9]\d{1,14}$/',
            'email' => 'required|email|max:255|unique:users,email,' . auth()->id(),
        ];
    }

    public function messages(): array
    {
        return [
            'firstname.required' => 'Имя обязательно для заполнения',
            'lastname.required' => 'Фамилия обязательна для заполнения',
            'phone.regex' => 'Неверный формат номера телефона',
            'email.required' => 'Email обязателен для заполнения',
            'email.email' => 'Неверный формат email',
            'email.unique' => 'Этот email уже используется',
        ];
    }
}