<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Routing\Controller as Controller;
use App\Http\Requests\CourseRequest;
use App\Models\Course;
use App\Services\CourseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    protected CourseService $service;

    public function __construct(CourseService $service)
    {
     
        $this->middleware('auth:sanctum')->except(['index', 'show']);
        $this->service = $service;

    }

    public function index(Request $request): JsonResponse
    {
        $courses = $this->service->all($request->all());
        return response()->json($courses);
    }

    public function store(CourseRequest $request): JsonResponse
    {
        $course = $this->service->store($request->validated());
        return response()->json($course, 201);
    }

    public function show(string $id): JsonResponse
    {
        $course = Course::with(['instructor', 'courseVersions', 'courseRuns', 'modules'])
            ->findOrFail($id);

        return response()->json($course);
    }

    public function update(CourseRequest $request, string $id): JsonResponse
    {
        $course = Course::findOrFail($id);
        $course = $this->service->update($course, $request->validated());
        return response()->json($course);
    }

    public function destroy(string $id): JsonResponse
    {
        $course = Course::findOrFail($id);
        $this->service->delete($course);

        return response()->json(['message' => 'Course deleted successfully']);
    }
}
