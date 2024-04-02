<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::post('forget-password/request', [AuthController::class, 'requestForgetPassword']);

Route::middleware(['auth.custom', 'api'])->group(function () {
    Route::get('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::get('me', [AuthController::class, 'me']);

});

Route::get('hello', function () {
    return response()->json(['message' => 'Toi da sua text nay lan thu 6']);
});
