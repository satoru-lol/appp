<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V2\VideoController;

Route::group(['prefix' => 'v2', 'middleware' => 'auth'], function () {
    // Главная страница видеотеки
    Route::get('/videostream', [VideoController::class, 'index'])->name('v2.video.index');
    
    // Просмотр категории
    Route::get('/video/category/{id}', [VideoController::class, 'showCategory'])->name('v2.video.category');
    
    // Просмотр отдельного видео
    Route::get('/video/{id}', [VideoController::class, 'showVideo'])->name('v2.video.show');
    
    // API для получения видео по категории
    Route::get('/api/categories/{id}/videos', [VideoController::class, 'getCategoryVideos'])->name('v2.api.category.videos');
    
    // API для поиска видео
    Route::get('/api/search/videos', [VideoController::class, 'searchVideos'])->name('v2.api.search.videos');
}); 