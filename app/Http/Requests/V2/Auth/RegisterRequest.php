<?php

namespace App\Http\Requests\V2\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:20|unique:users,phone|regex:/^\+?[1-9]\d{1,14}$/',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required|string|min:6',
            'terms' => 'required|accepted',
        ];
    }

    public function messages(): array
    {
        return [
            'firstname.required' => 'Имя обязательно для заполнения',
            'lastname.required' => 'Фамилия обязательна для заполнения',
            'email.required' => 'Email обязателен для заполнения',
            'email.email' => 'Неверный формат email',
            'email.unique' => 'Пользователь с таким email уже существует',
            'phone.unique' => 'Пользователь с таким телефоном уже существует',
            'phone.regex' => 'Неверный формат номера телефона',
            'password.required' => 'Пароль обязателен для заполнения',
            'password.min' => 'Пароль должен содержать минимум 6 символов',
            'password.confirmed' => 'Пароли не совпадают',
            'password_confirmation.required' => 'Подтверждение пароля обязательно',
            'terms.required' => 'Необходимо согласиться с условиями использования',
            'terms.accepted' => 'Необходимо согласиться с условиями использования',
        ];
    }
}