<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Routing\Controller as Controller;
use App\Http\Requests\CourseRequest;
use App\Models\Course;
use App\Services\CourseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class CourseController extends Controller
{
    protected CourseService $service;

    public function __construct(CourseService $service)
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
        $this->service = $service;
    }

    /**
     * List all courses
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $courses = $this->service->all($request->all());
            return response()->json([
                'status' => true,
                'data' => $courses
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch courses',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a new course
     */
    public function store(CourseRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $data['instructor_id'] = $request->user()->id;
            $course = $this->service->store($data);

            return response()->json([
                'status' => true,
                'message' => 'Course created successfully',
                'data' => $course
            ], 201);
        } catch (Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to create course',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show a single course
     */
    public function show(string $id): JsonResponse
    {
        try {
            $course = Course::with(['instructor', 'courseVersions', 'courseRuns', 'modules'])
                ->findOrFail($id);

            return response()->json([
                'status' => true,
                'data' => $course
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Course not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update an existing course
     */
    public function update(CourseRequest $request, string $id): JsonResponse
    {
        try {
            $course = Course::findOrFail($id);
            $updated = $this->service->update($course, $request->validated());

            return response()->json([
                'status' => true,
                'message' => 'Course updated successfully',
                'data' => $updated
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to update course',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a course
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $course = Course::findOrFail($id);
            $this->service->delete($course);

            return response()->json([
                'status' => true,
                'message' => 'Course deleted successfully'
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete course',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
