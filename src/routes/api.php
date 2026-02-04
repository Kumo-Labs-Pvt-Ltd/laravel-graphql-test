<?php

use App\Http\Controllers\Api\v1\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('v1/login', [AuthController::class, 'login'])->name('api.v1.login')->middleware('throttle:5,1');


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
