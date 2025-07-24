<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V2\Refactored\ProfileController;
use App\Http\Controllers\V2\Refactored\MeetingsController;
use App\Http\Controllers\V2\Refactored\CoursesController;
use App\Http\Controllers\V2\Refactored\VideoController;
use App\Http\Controllers\V2\Refactored\AuthController;

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

// Courses Routes (Refactored)
Route::prefix('v2/refactored/courses')->name('v2.refactored.courses.')->group(function () {
    // Публичные маршруты
    Route::get('/', [CoursesController::class, 'index'])->name('index');
    Route::get('/category/{id}', [CoursesController::class, 'category'])->name('category');
    Route::get('/{id}', [CoursesController::class, 'show'])->name('show');
    Route::get('/search', [CoursesController::class, 'search'])->name('search');
    
    // Маршруты требующие авторизации
    Route::middleware('auth')->group(function () {
        Route::get('/{id}/subscribe', [CoursesController::class, 'subscribe'])->name('subscribe');
        
        // Админские маршруты
        Route::middleware('admin')->group(function () {
            Route::get('/create', [CoursesController::class, 'create'])->name('create');
            Route::post('/', [CoursesController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [CoursesController::class, 'edit'])->name('edit');
            Route::put('/{id}', [CoursesController::class, 'update'])->name('update');
            Route::delete('/{id}', [CoursesController::class, 'destroy'])->name('destroy');
        });
        
        // API маршруты
        Route::prefix('api')->name('api.')->group(function () {
            Route::get('/', [CoursesController::class, 'apiIndex'])->name('index');
            Route::post('/{id}/subscribe', [CoursesController::class, 'apiSubscribe'])->name('subscribe');
        });
    });
});

// Video Routes (Refactored) - будет создан позже
Route::prefix('v2/refactored/video')->name('v2.refactored.video.')->group(function () {
    Route::middleware('auth')->group(function () {
        Route::get('/', [VideoController::class, 'index'])->name('index');
        Route::get('/category/{id}', [VideoController::class, 'showCategory'])->name('category');
        Route::get('/{id}', [VideoController::class, 'showVideo'])->name('show');
        Route::get('/search', [VideoController::class, 'search'])->name('search');
        
        // API маршруты
        Route::prefix('api')->name('api.')->group(function () {
            Route::get('/categories/{id}/videos', [VideoController::class, 'getCategoryVideos'])->name('category.videos');
            Route::get('/search/videos', [VideoController::class, 'searchVideos'])->name('search.videos');
        });
    });
});

// Auth Routes (Refactored) - будет создан позже
Route::prefix('v2/refactored/auth')->name('v2.refactored.auth.')->group(function () {
    // Публичные маршруты
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
    Route::get('/password/reset', [AuthController::class, 'showResetForm'])->name('password.request');
    Route::post('/password/email', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::post('/password/reset', [AuthController::class, 'resetPassword'])->name('password.reset');
    
    // Авторизованные маршруты
    Route::middleware('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::post('/password/change', [AuthController::class, 'changePassword'])->name('password.change');
        Route::post('/phone/verify', [AuthController::class, 'verifyPhone'])->name('phone.verify');
        Route::post('/phone/send-code', [AuthController::class, 'sendVerificationCode'])->name('phone.send-code');
    });
});