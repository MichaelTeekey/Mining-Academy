<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Routing\Controller as BaseController;
use App\Http\Requests\CourseVersionRequest;
use App\Models\CourseVersion;
use App\Services\CourseVersionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class CourseVersionController extends BaseController
{
    protected CourseVersionService $service;

    public function __construct(CourseVersionService $service)
    {
        $this->service = $service;
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $versions = $this->service->all($request->all());
            return response()->json(['status'=>true,'data'=>$versions]);
        } catch (Throwable $e) {
            return response()->json(['status'=>false,'message'=>'Failed to fetch course versions','error'=>$e->getMessage()],500);
        }
    }

    public function store(CourseVersionRequest $request): JsonResponse
    {
        try {
            $version = $this->service->store($request->validated());
            return response()->json(['status'=>true,'message'=>'Course version created successfully','data'=>$version],201);
        } catch (Throwable $e) {
            return response()->json(['status'=>false,'message'=>'Failed to create course version','error'=>$e->getMessage()],500);
        }
    }

    public function show(string $id): JsonResponse
    {
        try {
            $version = CourseVersion::with('modules')->findOrFail($id);
            return response()->json(['status'=>true,'data'=>$version]);
        } catch (Throwable $e) {
            return response()->json(['status'=>false,'message'=>'Course version not found','error'=>$e->getMessage()],404);
        }
    }

    public function update(CourseVersionRequest $request, string $id): JsonResponse
    {
        try {
            $version = CourseVersion::findOrFail($id);
            $version = $this->service->update($version, $request->validated());
            return response()->json(['status'=>true,'message'=>'Course version updated successfully','data'=>$version]);
        } catch (Throwable $e) {
            return response()->json(['status'=>false,'message'=>'Failed to update course version','error'=>$e->getMessage()],500);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $version = CourseVersion::findOrFail($id);
            $this->service->delete($version);
            return response()->json(['status'=>true,'message'=>'Course version deleted successfully']);
        } catch (Throwable $e) {
            return response()->json(['status'=>false,'message'=>'Failed to delete course version','error'=>$e->getMessage()],500);
        }
    }
}
