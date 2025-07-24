<?php

namespace App\Http\Requests\V2\Meetings;

use Illuminate\Foundation\Http\FormRequest;

class StoreMeetingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:200',
            'date' => 'required|date|after:now',
            'fio' => 'required|string|max:200',
            'format_id' => 'required|exists:meeting_format,id',
            'feedback' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'text' => 'nullable|string',
            'amount' => 'nullable|string|max:100',
            'quantity' => 'nullable|integer|min:1|max:1000',
            'place' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Название встречи обязательно',
            'name.max' => 'Название не должно превышать 200 символов',
            'date.required' => 'Дата встречи обязательна',
            'date.after' => 'Дата встречи должна быть в будущем',
            'fio.required' => 'ФИО спикера обязательно',
            'format_id.required' => 'Формат встречи обязателен',
            'format_id.exists' => 'Выбранный формат не существует',
            'image.image' => 'Файл должен быть изображением',
            'image.mimes' => 'Допустимые форматы: jpeg, png, jpg, gif',
            'image.max' => 'Размер изображения не должен превышать 2MB',
            'quantity.integer' => 'Количество участников должно быть числом',
            'quantity.min' => 'Минимальное количество участников: 1',
            'quantity.max' => 'Максимальное количество участников: 1000',
        ];
    }
}