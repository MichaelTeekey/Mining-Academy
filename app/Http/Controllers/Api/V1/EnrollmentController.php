<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Models\Enrollment;
use App\Http\Requests\EnrollmentRequest;
use Illuminate\Http\JsonResponse;
use Throwable;

class EnrollmentController extends BaseController
{
    /**
     * Enroll a user in a course run
     */
    public function store(EnrollmentRequest $request): JsonResponse
    {
        try {
            $userId = $request->user()->id;
            $courseRunId = $request->validated()['course_run_id'];

            $enroll = Enrollment::firstOrCreate([
                'user_id' => $userId,
                'course_run_id' => $courseRunId,
            ], [
                'status' => 'enrolled',
                'enrolled_at' => now()
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Enrollment successful',
                'data' => $enroll
            ], 201);
        } catch (Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to enroll in course',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * List all courses a user is enrolled in
     */
    public function myCourses(Request $request): JsonResponse
    {
        try {
            $courses = Enrollment::where('user_id', $request->user()->id)
                ->with('courseRun.course')
                ->get();

            return response()->json([
                'status' => true,
                'data' => $courses
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch enrolled courses',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
