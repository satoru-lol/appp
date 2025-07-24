<?php

use Illuminate\Support\Facades\Route;

// Тестовый маршрут для проверки
Route::get('/test-home', function () {
    return 'Тестовая главная страница работает!';
})->name('test.home');

// Проверка доступности контроллера
Route::get('/test-controller', function () {
    try {
        $controller = new \App\Http\Controllers\V2\Refactored\HomeController();
        return 'Контроллер HomeController доступен!';
    } catch (\Exception $e) {
        return 'Ошибка контроллера: ' . $e->getMessage();
    }
})->name('test.controller');