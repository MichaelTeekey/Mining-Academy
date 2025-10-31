<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\{
    AuthController,
    CourseController,
    CourseVersionController,
    CourseRunController,
    EnrollmentController,
    PaymentController,
    ProfileController,
    ModuleController,
    LessonController,
    MediaFileController,
    VideoController,
    VideoRenditionController,
    TranscodingJobController,
    WalletController
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
    Route::get('/course-versions', [CourseVersionController::class, 'index']);
    Route::get('/course-versions/{id}', [CourseVersionController::class, 'show']);
    Route::get('/course-runs', [CourseRunController::class, 'index']);
    Route::get('/course-runs/{id}', [CourseRunController::class, 'show']);

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
    // 
    Route::apiResource('transcoding-jobs', TranscodingJobController::class)->only(['index','show']);

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
        Route::apiResource('course-runs', CourseRunController::class)->only([
            'store', 'update', 'destroy'
        ]);
        Route::apiResource('course-versions', CourseVersionController::class)->only([
            'store', 'update', 'destroy'
        ]);
        // Instructor: Module Management
        Route::apiResource('modules', ModuleController::class)->only([
            'store', 'update', 'destroy'
        ]);
        //wallet
        Route::get('/wallet', [WalletController::class, 'show'])->name('wallet.show');
        Route::post('/wallet/deposit', [WalletController::class, 'deposit'])->name('wallet.deposit');
        Route::post('/wallet/withdraw', [WalletController::class, 'withdraw'])->name('wallet.withdraw');
        Route::get('/wallet/transactions', [WalletController::class, 'transactions'])->name('wallet.transactions');

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
        Route::apiResource('transcoding-jobs', TranscodingJobController::class)->only(['store','update','destroy']);

        Route::post('/pay', [PaymentController::class, 'store'])->name('payments.store');
        Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    });
});
