<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Routing\Controller as BaseController;
use App\Http\Requests\CourseRunRequest;
use Illuminate\Support\Facades\Log;
use App\Models\CourseRun;
use App\Services\CourseRunService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class CourseRunController extends BaseController
{
    protected CourseRunService $service;

    public function __construct(CourseRunService $service)
    {
        $this->service = $service;
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $runs = $this->service->all($request->all());
            return response()->json(['status'=>true,'data'=>$runs]);
        } catch (Throwable $e) {

            log::error('Error fetching course runs', [
                'error' => $e->getMessage(),
                'ip' => $request->ip(),
            ]);

            return response()->json(['status'=>false,'message'=>'Failed to fetch course runs','error'=>$e->getMessage()],500);
        }
    }

    public function store(CourseRunRequest $request): JsonResponse
    {
        try {
            $run = $this->service->store($request->validated());
            log::info('Course run created successfully', [
                'course_run_id' => $run->id,
                'ip' => $request->ip(),
            ]);
            return response()->json(['status'=>true,'message'=>'Course run created successfully','data'=>$run],201);
        } catch (Throwable $e) {
            log::error('Error creating course run', [
                'error' => $e->getMessage(),
                'ip' => $request->ip(),
            ]);
            return response()->json(['status'=>false,'message'=>'Failed to create course run','error'=>$e->getMessage()],500);
        }
    }

    public function show(string $id): JsonResponse
    {
        try {
            $run = CourseRun::with('course')->findOrFail($id);

            return response()->json(['status'=>true,'data'=>$run]);
        } catch (Throwable $e) {
            log::error('Error fetching course run', [
                'course_run_id' => $id,
                'error' => $e->getMessage(),
                'ip' => request()->ip(),
            ]);
            return response()->json(['status'=>false,'message'=>'Course run not found','error'=>$e->getMessage()],404);
        }
    }

    public function update(CourseRunRequest $request, string $id): JsonResponse
    {
        try {
            $run = CourseRun::findOrFail($id);
            log::info('Updating course run', [
                'course_run_id' => $run->id,
                'ip' => request()->ip(),
            ]);
            $run = $this->service->update($run, $request->validated());
            return response()->json(['status'=>true,'message'=>'Course run updated successfully','data'=>$run]);
        } catch (Throwable $e) {
            log::error('Error updating course run', [
                'course_run_id' => $id,
                'error' => $e->getMessage(),
                'ip' => request()->ip(),
            ]);
            return response()->json(['status'=>false,'message'=>'Failed to update course run','error'=>$e->getMessage()],500);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $run = CourseRun::findOrFail($id);
            $this->service->delete($run);
            log::info('Course run deleted successfully', [
                'course_run_id' => $run->id,
                'ip' => request()->ip(),
            ]);
            return response()->json(['status'=>true,'message'=>'Course run deleted successfully']);
        } catch (Throwable $e) {
            log::error('Error deleting course run', [
                'course_run_id' => $id,
                'error' => $e->getMessage(),
                'ip' => request()->ip(),
            ]);
            return response()->json(['status'=>false,'message'=>'Failed to delete course run','error'=>$e->getMessage()],500);
        }
    }
}
