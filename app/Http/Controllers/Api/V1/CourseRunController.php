<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Routing\Controller as BaseController;
use App\Http\Requests\CourseRunRequest;
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
            return response()->json(['status'=>false,'message'=>'Failed to fetch course runs','error'=>$e->getMessage()],500);
        }
    }

    public function store(CourseRunRequest $request): JsonResponse
    {
        try {
            $run = $this->service->store($request->validated());
            return response()->json(['status'=>true,'message'=>'Course run created successfully','data'=>$run],201);
        } catch (Throwable $e) {
            return response()->json(['status'=>false,'message'=>'Failed to create course run','error'=>$e->getMessage()],500);
        }
    }

    public function show(string $id): JsonResponse
    {
        try {
            $run = CourseRun::with('course')->findOrFail($id);
            return response()->json(['status'=>true,'data'=>$run]);
        } catch (Throwable $e) {
            return response()->json(['status'=>false,'message'=>'Course run not found','error'=>$e->getMessage()],404);
        }
    }

    public function update(CourseRunRequest $request, string $id): JsonResponse
    {
        try {
            $run = CourseRun::findOrFail($id);
            $run = $this->service->update($run, $request->validated());
            return response()->json(['status'=>true,'message'=>'Course run updated successfully','data'=>$run]);
        } catch (Throwable $e) {
            return response()->json(['status'=>false,'message'=>'Failed to update course run','error'=>$e->getMessage()],500);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $run = CourseRun::findOrFail($id);
            $this->service->delete($run);
            return response()->json(['status'=>true,'message'=>'Course run deleted successfully']);
        } catch (Throwable $e) {
            return response()->json(['status'=>false,'message'=>'Failed to delete course run','error'=>$e->getMessage()],500);
        }
    }
}
