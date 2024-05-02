<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login'])->name('login');
Route::post('register', [AuthController::class, 'register'])->name('register');
Route::post('send-otp', [AuthController::class, 'sendOtp'])->name('send-otp');
Route::post('forget-password', [AuthController::class, 'forgetPassword'])->name('forget-password');

Route::middleware(['auth.custom', 'api'])->group(function () {
    Route::get('logout', [AuthController::class, 'logout'])->name('auth-logout');
    Route::post('refresh', [AuthController::class, 'refresh'])->name('auth-refresh-token');
    Route::get('me', [AuthController::class, 'me'])->name('auth-me');
    Route::post('change-password', [AuthController::class, 'changePassword'])->name('auth-change-password');
});

Route::middleware(['admin'])->group(function () {
    Route::apiResources([
        'users' => UserController::class,
    ]);

    Route::apiResource('courses', CourseController::class)->except(['index', 'show']);
});

Route::middleware(['teacher'])->group(function () {
    Route::prefix('courses')->group(function () {
        Route::get('', [CourseController::class, 'index'])->name('courses.index');
        Route::get('/{course}', [CourseController::class, 'show'])->name('courses.show');
        Route::get('/get-name/{id}', [CourseController::class, 'getName'])->name('courses.get-name');
    });

    Route::prefix('lessons')->group(function () {
        Route::get('get-name/{id}', [LessonController::class, 'getName'])->name('lessons.get-name');
    });
    Route::apiResources([
        'lessons' => LessonController::class,
    ]);
});

Route::get('hello', function () {
    return response()->json(['message' => 'Hello world']);
});
