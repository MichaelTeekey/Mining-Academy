<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Models\CourseRun;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class EnrollmentService
{
    protected WalletService $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    /**
     * Enroll a user into a course run.
     * Handles both free and paid courses.
     */
    public function enrollUser(string $userId, string $courseRunId)
    {
        return DB::transaction(function () use ($userId, $courseRunId) {
            try {
                $courseRun = CourseRun::with('course')->findOrFail($courseRunId);
                $course = $courseRun->course;

                if (!$course) {
                    throw new Exception('Course not found for this run.');
                }

                // Prevent duplicate enrollment
                $existing = Enrollment::where('user_id', $userId)
                    ->where('course_run_id', $courseRunId)
                    ->first();

                if ($existing) {
                    throw new Exception('You are already enrolled in this course.');
                }

                // Payment handling
                if (!$course->is_free && $course->price > 0) {
                    Log::info('Processing paid enrollment', [
                        'user_id' => $userId,
                        'course_run_id' => $courseRunId,
                        'amount' => $course->price,
                    ]);

                    $this->walletService->purchaseCourse(
                        $userId,
                        $course->price,
                        $course->title
                    );
                } else {
                    Log::info('Processing free enrollment', [
                        'user_id' => $userId,
                        'course_run_id' => $courseRunId,
                    ]);
                }

                // Create enrollment
                $enrollment = Enrollment::create([
                    'user_id' => $userId,
                    'course_run_id' => $courseRunId,
                    'status' => 'active',
                    'enrolled_at' => now(),
                ]);

                Log::info('User successfully enrolled', [
                    'user_id' => $userId,
                    'course_run_id' => $courseRunId,
                    'course_free' => $course->is_free,
                ]);

                return $enrollment;
            } catch (Exception $e) {
                Log::error('Enrollment failed', [
                    'user_id' => $userId,
                    'course_run_id' => $courseRunId,
                    'error' => $e->getMessage(),
                ]);
                throw new Exception('Enrollment failed: ' . $e->getMessage());
            }
        });
    }

    /**
     * Get all courses enrolled by a user.
     */
    public function getUserCourses(string $userId)
    {
       
        try {
            return Enrollment::where('user_id', $userId)
                ->with([
                    'courseRun.course' => function ($query) {
                        $query->with([
                            'instructor:id,name,email',
                            'modules.lessons.mediaFiles'
                        ]);
                    },
                    'courseRun' 
                ])
                ->orderBy('created_at', 'desc')
                ->get();
        } catch (Exception $e) {
            Log::error('Failed to fetch user courses', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            throw new Exception('Unable to fetch enrolled courses at this time.');
        }
    }

    /**
     * Cancel (delete) an enrollment.
     * - Refunds wallet if it was a paid course.
     * - Removes enrollment record.
     */
    public function delete(string $id)
    {
        return DB::transaction(function () use ($id) {
            try {
                $enrollment = Enrollment::with('courseRun.course')->findOrFail($id);
                $courseRun = $enrollment->courseRun;
                $course = $courseRun->course;

                if (!$course) {
                    throw new Exception('Course not found for this enrollment.');
                }

                $userId = $enrollment->user_id;
                $amount = $course->price ?? 0;
                $isPaidCourse = !$course->is_free && $amount > 0;

                if ($isPaidCourse) {
                    // Find the payment made for this course (if any)
                    $payment = Payment::where('user_id', $userId)
                        ->where('status', 'success')
                        ->where('payment_method', 'wallet')
                        ->whereJsonContains('meta->course', $course->title)
                        ->latest()
                        ->first();

                    if ($payment) {
                        // Refund to wallet
                        $this->walletService->refund(
                            $userId,
                            $amount,
                            "Refund for cancelled course: {$course->title}",
                            'REF_' . $payment->transaction_id
                        );

                        // Update payment status
                        $payment->update(['status' => 'refunded']);

                        Log::info('Refund processed for enrollment cancellation', [
                            'user_id' => $userId,
                            'course' => $course->title,
                            'amount' => $amount,
                        ]);
                    } else {
                        Log::warning('No matching payment found for refund', [
                            'user_id' => $userId,
                            'course' => $course->title,
                        ]);
                    }
                }

                // Delete enrollment
                $enrollment->delete();

                Log::info('Enrollment deleted', [
                    'user_id' => $userId,
                    'course' => $course->title,
                    'refunded' => $isPaidCourse,
                ]);

                return true;
            } catch (Exception $e) {
                Log::error('Failed to delete enrollment', [
                    'enrollment_id' => $id,
                    'error' => $e->getMessage(),
                ]);
                throw new Exception('Unable to delete enrollment: ' . $e->getMessage());
            }
        });
    }
}
