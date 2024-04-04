<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login'])->name('login');
Route::post('register', [AuthController::class, 'register'])->name('register');
Route::post('forget-password/request', [AuthController::class, 'requestForgetPassword'])
    ->name('request-forget-password');
Route::post('forget-password', [AuthController::class, 'forgetPassword'])->name('forget-password');

Route::middleware(['auth.custom', 'api'])->group(function () {
    Route::get('logout', [AuthController::class, 'logout'])->name('auth-logout');
    Route::post('refresh', [AuthController::class, 'refresh'])->name('auth-refresh-token');
    Route::get('me', [AuthController::class, 'me'])->name('auth-me');
    Route::post('change-password', [AuthController::class, 'changePassword'])->name('auth-change-password');
});

Route::get('hello', function () {
    return response()->json(['message' => 'Toi da sua text nay lan thu 6']);
});
