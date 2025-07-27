<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CoursesV2Controller;

Route::get('/courses-v2', [CoursesV2Controller::class, 'index'])->name('courses-v2.index');
Route::get('/courses-v2/category/{id}', [CoursesV2Controller::class, 'category'])->name('courses-v2.category');
Route::get('/courses-v2/{id}', [CoursesV2Controller::class, 'show'])->name('courses-v2.show');

// Маршрут для записи на курс (требуется авторизация)
Route::get('/courses-v2/{id}/subscribe', [CoursesV2Controller::class, 'subscribeToCourse'])
    ->middleware('auth')
    ->name('courses-v2.subscribe'); 