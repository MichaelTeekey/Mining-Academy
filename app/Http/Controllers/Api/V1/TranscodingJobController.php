<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Routing\Controller as Controller;
use App\Http\Requests\TranscodingJobRequest;
use App\Models\TranscodingJob;
use App\Services\TranscodingJobService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TranscodingJobController extends Controller
{
    protected TranscodingJobService $service;

    public function __construct(TranscodingJobService $service)
    {
        $this->service = $service;
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    public function index(Request $request): JsonResponse
    {
        $jobs = $this->service->all($request->all());
        return response()->json($jobs);
    }

    public function store(TranscodingJobRequest $request): JsonResponse
    {
        $job = $this->service->store($request->validated());
        return response()->json($job, 201);
    }

    public function show(string $id): JsonResponse
    {
        $job = TranscodingJob::with('video')->findOrFail($id);
        return response()->json($job);
    }

    public function update(TranscodingJobRequest $request, string $id): JsonResponse
    {
        $job = TranscodingJob::findOrFail($id);
        $job = $this->service->update($job, $request->validated());
        return response()->json($job);
    }

    public function destroy(string $id): JsonResponse
    {
        $job = TranscodingJob::findOrFail($id);
        $this->service->delete($job);

        return response()->json(['message' => 'Transcoding job deleted successfully']);
    }
}
