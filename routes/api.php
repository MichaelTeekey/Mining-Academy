<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\{
    AuthController,
    CourseController,
    EnrollmentController,
    PaymentController,
    ProfileController,
    ModuleController,
    LessonController,
    MediaFileController,
    VideoController,
    VideoRenditionController
};

Route::prefix('v1')->group(function () {

    /**
     * -------------------------
     * Public Routes
     * -------------------------
     */
    Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login');

    // Public course listing (index & show only)
    Route::apiResource('courses', CourseController::class)->only(['index', 'show']);
    // Public module listing (index & show only)
    Route::apiResource('modules', ModuleController::class)->only(['index', 'show']);
    // Public lesson listing (index & show only)
    Route::apiResource('lessons', LessonController::class)->only(['index', 'show']);
    // Public media file listing (index & show only)
    Route::apiResource('media-files', MediaFileController::class)->only(['index', 'show']);
    // Public video listing (index & show only)
    Route::apiResource('videos', VideoController::class)->only(['index', 'show']);
    // Public video rendition listing (index & show only)
    Route::apiResource('video-renditions', VideoRenditionController::class)->only(['index', 'show']);
    /**
     * -------------------------
     * Protected Routes
     * -------------------------
     */
    Route::middleware('auth:sanctum')->group(function () {

        // Profile
        Route::get('/me', [ProfileController::class, 'me'])->name('profile.me');
        Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');

        // Instructor: Course Management
        Route::apiResource('courses', CourseController::class)->only([
            'store', 'update', 'destroy'
        ]);
        // Instructor: Module Management
        Route::apiResource('modules', ModuleController::class)->only([
            'store', 'update', 'destroy'
        ]);

        // Instructor: Lesson Management
        Route::apiResource('lessons', LessonController::class)->only(['store', 'update', 'destroy']);

        // Instructor: Media File Management
        Route::apiResource('media-files', MediaFileController::class)->only(['store', 'update', 'destroy']);    
        // Instructor: Video Management
        Route::apiResource('videos', VideoController::class)->only(['store', 'update', 'destroy']);
        // Instructor: Video Rendition Management
        Route::apiResource('video-renditions', VideoRenditionController::class)->only(['store', 'update', 'destroy']);
        // Student: Enrollment & Payments
        Route::post('/enroll', [EnrollmentController::class, 'store'])->name('enrollments.store');
        Route::get('/my-courses', [EnrollmentController::class, 'myCourses'])->name('enrollments.myCourses');

        Route::post('/pay', [PaymentController::class, 'store'])->name('payments.store');
        Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    });
});
