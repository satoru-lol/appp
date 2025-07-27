<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ExternalController;

Route::prefix('v1')->group(function () {
    
     Route::post('/external-users', [ExternalController::class, 'getUsers']);
});
