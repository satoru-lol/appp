<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V2\Refactored\ProfileController;
use App\Http\Controllers\V2\Refactored\MeetingsController;
use App\Http\Controllers\V2\Refactored\CoursesController;
use App\Http\Controllers\V2\Refactored\VideoController;
use App\Http\Controllers\V2\Refactored\AuthController;
use App\Http\Controllers\V2\Refactored\HomeController as RefactoredHomeController;

/*
|--------------------------------------------------------------------------
| V2 Refactored Routes
|--------------------------------------------------------------------------
| 
| Рефакторенные маршруты для V2 функционала с улучшенной архитектурой
|
*/

// Home Routes (Refactored)
Route::get('/', [RefactoredHomeController::class, 'index'])->name('v2.refactored.home.index');
Route::get('/search', [RefactoredHomeController::class, 'search'])->name('v2.refactored.home.search');
Route::get('/about', [RefactoredHomeController::class, 'about'])->name('v2.refactored.home.about');
Route::get('/contacts', [RefactoredHomeController::class, 'contacts'])->name('v2.refactored.home.contacts');

// Profile Routes (Refactored)
Route::middleware('auth')->prefix('v2/refactored/profile')->name('v2.refactored.profile.')->group(function () {
    Route::get('/', [ProfileController::class, 'index'])->name('index');
    Route::put('/update', [ProfileController::class, 'update'])->name('update');
    Route::post('/avatar', [ProfileController::class, 'updateAvatar'])->name('avatar');
    Route::get('/transactions', [ProfileController::class, 'fetchTransactions'])->name('transactions');
    Route::post('/subscribe', [ProfileController::class, 'subscribe'])->name('subscribe');
    Route::post('/add-balance', [ProfileController::class, 'addBalance'])->name('add-balance');
    Route::post('/cancel-subscription', [ProfileController::class, 'cancelMonthPay'])->name('cancel-subscription');
    Route::post('/renew', [ProfileController::class, 'subscribe'])->name('renew');
    Route::post('/use-bonus', [ProfileController::class, 'useBonus'])->name('use-bonus');
    Route::get('/payment/success', [ProfileController::class, 'paymentSuccess'])->name('payment.success');
    Route::get('/payment/fail', [ProfileController::class, 'paymentFail'])->name('payment.fail');
    Route::post('/qr-link', [ProfileController::class, 'createQrLink'])->name('qr-link');
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

// Video Routes (Refactored)
Route::prefix('v2/refactored/video')->name('v2.refactored.video.')->group(function () {
    Route::middleware('auth')->group(function () {
        Route::get('/', [VideoController::class, 'index'])->name('index');
        Route::get('/popular', [VideoController::class, 'popular'])->name('popular');
        Route::get('/recent', [VideoController::class, 'recent'])->name('recent');
        Route::get('/search', [VideoController::class, 'search'])->name('search');
        Route::get('/category/{id}', [VideoController::class, 'showCategory'])->name('category');
        Route::get('/{id}', [VideoController::class, 'showVideo'])->name('show');
        Route::post('/{id}/views', [VideoController::class, 'incrementViews'])->name('increment-views');
        
        // Админские маршруты
        Route::middleware('admin')->group(function () {
            Route::get('/create', [VideoController::class, 'create'])->name('create');
            Route::post('/', [VideoController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [VideoController::class, 'edit'])->name('edit');
            Route::put('/{id}', [VideoController::class, 'update'])->name('update');
            Route::delete('/{id}', [VideoController::class, 'destroy'])->name('destroy');
        });
        
        // API маршруты
        Route::prefix('api')->name('api.')->group(function () {
            Route::get('/categories/{id}/videos', [VideoController::class, 'getCategoryVideos'])->name('category.videos');
            Route::get('/search/videos', [VideoController::class, 'searchVideos'])->name('search.videos');
        });
    });
});

// Auth Routes (Refactored)
Route::prefix('v2/refactored/auth')->name('v2.refactored.auth.')->group(function () {
    // Публичные маршруты
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
    Route::get('/password/reset', [AuthController::class, 'showResetForm'])->name('password.request');
    Route::post('/password/email', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::post('/password/reset', [AuthController::class, 'resetPassword'])->name('password.reset');
    
    // AJAX проверки доступности
    Route::post('/check-email', [AuthController::class, 'checkEmailAvailability'])->name('check-email');
    Route::post('/check-phone', [AuthController::class, 'checkPhoneAvailability'])->name('check-phone');
    
    // Авторизованные маршруты
    Route::middleware('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::post('/password/change', [AuthController::class, 'changePassword'])->name('password.change');
        Route::post('/phone/verify', [AuthController::class, 'verifyPhone'])->name('phone.verify');
        Route::post('/phone/send-code', [AuthController::class, 'sendVerificationCode'])->name('phone.send-code');
        
        // Админские маршруты
        Route::middleware('admin')->group(function () {
            Route::get('/stats', [AuthController::class, 'getAuthStats'])->name('stats');
        });
    });
});