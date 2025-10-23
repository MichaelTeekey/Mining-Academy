<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Http\Requests\EnrollmentRequest;
use App\Services\EnrollmentService;
use Exception;

class EnrollmentController extends BaseController
{
    protected EnrollmentService $service;

    public function __construct(EnrollmentService $service)
    {
        $this->service = $service;
        $this->middleware('auth:sanctum');
    }

    /**
     * Enroll authenticated user into a course run
     */
    public function store(EnrollmentRequest $request): JsonResponse
    {
        try {
            $userId = $request->user()->id;
            $enrollment = $this->service->enrollUser($userId, $request->course_run_id);

            return response()->json([
                'message' => 'Enrollment successful',
                'data' => $enrollment
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all courses enrolled by the authenticated user
     */
    public function myCourses(Request $request): JsonResponse
    {
        try {
            $userId = $request->user()->id;
            $courses = $this->service->getUserCourses($userId);

            return response()->json([
                'data' => $courses
            ]);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Optional: Delete an enrollment
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $this->service->delete($id);
            return response()->json(['message' => 'Enrollment removed successfully']);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
