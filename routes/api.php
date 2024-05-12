<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\GeminiChatController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login'])->name('login');
Route::post('login/google', [AuthController::class, 'loginGoogle'])->name('login-google');
Route::post('register', [AuthController::class, 'register'])->name('register');
Route::post('send-otp', [AuthController::class, 'sendOtp'])->name('send-otp');
Route::post('forget-password', [AuthController::class, 'forgetPassword'])->name('forget-password');

Route::prefix('home')->group(function () {
    Route::get('', [HomeController::class, 'index'])->name('home.index');
    Route::prefix('courses')->group(function () {
        Route::get('{id}', [HomeController::class, 'courseDetail'])->name('home.courses.detail');
    });
    Route::prefix('exams')->group(function () {
        Route::get('', [HomeController::class, 'examList'])->name('home.exams.index');
        Route::get('overview/{id}', [HomeController::class, 'examOverview'])->name('home.exams.overview');
    });
});

Route::prefix('courses')->group(function () {
    Route::get('', [CourseController::class, 'index'])->name('courses.index');
});

Route::middleware(['auth.custom', 'api'])->group(function () {
    Route::get('logout', [AuthController::class, 'logout'])->name('auth-logout');
    Route::post('refresh', [AuthController::class, 'refresh'])->name('auth-refresh-token');
    Route::get('me', [AuthController::class, 'me'])->name('auth-me');
    Route::post('change-password', [AuthController::class, 'changePassword'])->name('auth-change-password');

    Route::prefix('home')->group(function () {
        Route::prefix('courses')->group(function () {
            Route::post('subscribe/{id}', [HomeController::class, 'subscribeCourse'])->name('home.courses.subscribe');
        });
        Route::prefix('lessons')->group(function () {
            Route::get('{id}', [HomeController::class, 'lessonDetail'])->name('home.lessons.detail');
            Route::post('select', [LessonController::class, 'selectChoice'])->name('home.lessons.select');
            Route::post('start-chat/{id}', [LessonController::class, 'startChat'])->name('home.lessons.select');
        });
        Route::prefix('exams')->group(function () {
            Route::get('review/{id}', [HomeController::class, 'examReview'])->name('home.exams.review');
            Route::get('{id}', [HomeController::class, 'examDetail'])->name('home.exams.detail');
            Route::post('submit/{id}', [HomeController::class, 'examSubmit'])->name('home.exams.submit');
        });
    });

    Route::prefix('gemini')->group(function () {
        Route::get('{uuid?}', [GeminiChatController::class, 'index'])->name('gemini.index');
        Route::post('send-message', [GeminiChatController::class, 'sendMessage'])->name('gemini.send-message');
    });
});

Route::middleware(['admin'])->group(function () {
    Route::apiResources([
        'users' => UserController::class,
    ]);

    Route::apiResource('courses', CourseController::class)->except(['index', 'show']);
});

Route::middleware(['teacher'])->group(function () {
    Route::prefix('courses')->group(function () {
        Route::get('/{course}', [CourseController::class, 'show'])->name('courses.show');
        Route::get('/get-name/{id}', [CourseController::class, 'getName'])->name('courses.get-name');
    });
    Route::prefix('lessons')->group(function () {
        Route::get('get-name/{id}', [LessonController::class, 'getName'])->name('lessons.get-name');
    });
    Route::prefix('exams')->group(function () {
        Route::get('get-name/{id}', [ExamController::class, 'getName'])->name('exams.get-name');
    });
    Route::apiResources([
        'lessons' => LessonController::class,
        'questions' => QuestionController::class,
        'exams' => ExamController::class,
    ]);
});

Route::get('hello', function () {
    return response()->json(['message' => 'Hello world']);
});
Route::get('', function () {
    echo 'Copyright © Trần Xuân Đức';
});
