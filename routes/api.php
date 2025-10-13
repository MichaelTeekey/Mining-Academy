<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\{
    AuthController, CourseController, EnrollmentController, PaymentController, ProfileController
};

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->group(function () {

    // --- Public ---
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/courses', [CourseController::class, 'index']);
    Route::get('/courses/{id}', [CourseController::class, 'show']);

    // --- Protected ---
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [ProfileController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);

        // Instructor actions
        Route::post('/courses', [CourseController::class, 'store']);
        Route::put('/courses/{id}', [CourseController::class, 'update']);
        Route::delete('/courses/{id}', [CourseController::class, 'destroy']);

        // Student actions
        Route::post('/enroll', [EnrollmentController::class, 'store']);
        Route::get('/my-courses', [EnrollmentController::class, 'myCourses']);
        Route::post('/pay', [PaymentController::class, 'store']);
        Route::get('/payments', [PaymentController::class, 'index']);
    });
});