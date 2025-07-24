<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V2\Refactored\ProfileController;
use App\Http\Controllers\V2\Refactored\MeetingsController;

/*
|--------------------------------------------------------------------------
| V2 Refactored Routes
|--------------------------------------------------------------------------
| 
| Рефакторенные маршруты для V2 функционала с улучшенной архитектурой
|
*/

// Profile Routes (Refactored)
Route::middleware('auth')->prefix('v2/refactored/profile')->name('v2.refactored.profile.')->group(function () {
    Route::get('/', [ProfileController::class, 'index'])->name('index');
    Route::put('/update', [ProfileController::class, 'update'])->name('update');
    Route::post('/avatar', [ProfileController::class, 'updateAvatar'])->name('avatar.update');
    Route::delete('/avatar', [ProfileController::class, 'removeAvatar'])->name('avatar.remove');
    Route::get('/transactions', [ProfileController::class, 'fetchTransactions'])->name('transactions');
    Route::post('/generate-qr-link', [ProfileController::class, 'generateQrLink'])->name('generate-qr-link');
    Route::post('/subscription', [ProfileController::class, 'handleSubscription'])->name('subscription.handle');
    Route::post('/balance/add', [ProfileController::class, 'addBalance'])->name('balance.add');
});

// Avatar Route (Refactored)
Route::get('/v2/refactored/avatar/{userId}', [ProfileController::class, 'getAvatar'])->name('v2.refactored.avatar');

// Meetings Routes (Refactored)
Route::prefix('v2/refactored/meetings')->name('v2.refactored.meetings.')->group(function () {
    // Публичные маршруты
    Route::get('/', [MeetingsController::class, 'index'])->name('index');
    Route::get('/previous', [MeetingsController::class, 'previous'])->name('previous');
    Route::get('/{id}', [MeetingsController::class, 'show'])->name('show');
    
    // Маршруты требующие авторизации
    Route::middleware('auth')->group(function () {
        Route::get('/create', [MeetingsController::class, 'create'])->name('create');
        Route::post('/', [MeetingsController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [MeetingsController::class, 'edit'])->name('edit');
        Route::put('/{id}', [MeetingsController::class, 'update'])->name('update');
        Route::delete('/{id}', [MeetingsController::class, 'destroy'])->name('delete');
        
        // Участие во встречах
        Route::get('/{id}/take-part', [MeetingsController::class, 'takePart'])->name('takePart');
        Route::get('/{id}/cancel-part', [MeetingsController::class, 'cancelPart'])->name('cancelPart');
        
        // AJAX маршруты
        Route::post('/{id}/comment', [MeetingsController::class, 'addComment'])->name('addComment');
        Route::post('/{id}/like', [MeetingsController::class, 'addLike'])->name('addLike');
        Route::post('/{id}/dislike', [MeetingsController::class, 'addDislike'])->name('addDislike');
    });
});