<?php

use App\Http\Controllers\Api\v1\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Authentication
    Route::post('login', [AuthController::class, 'login'])->name('api.v1.login')->middleware('throttle:5,1');
    Route::post('register', [AuthController::class, 'register'])->name('api.v1.register')->middleware('throttle:5,1');
    Route::post('forgot-password', [AuthController::class, 'forgotPassword'])->name('api.v1.password.forgot')->middleware('throttle:3,1');
    Route::post('reset-password', [AuthController::class, 'resetPassword'])->name('api.v1.password.reset')->middleware('throttle:5,1');
    Route::get('verify-token', [AuthController::class, 'verifyToken'])->name('api.v1.token.verify')->middleware('throttle:5,1');

    Route::middleware('auth:sanctum')->group(function () {
        // Authentication
        Route::post('logout', [AuthController::class, 'logout'])->name('api.v1.logout')->middleware('throttle:5,1');
        Route::get('profile', [AuthController::class, 'profile'])->name('api.v1.user')->middleware('throttle:5,1');
    });
});
