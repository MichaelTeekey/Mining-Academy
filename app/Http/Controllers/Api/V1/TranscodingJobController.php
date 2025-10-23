<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Routing\Controller as Controller;
use App\Http\Requests\TranscodingJobRequest;
use App\Models\TranscodingJob;
use App\Services\TranscodingJobService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class TranscodingJobController extends Controller
{
    protected TranscodingJobService $service;

    public function __construct(TranscodingJobService $service)
    {
        $this->service = $service;
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    /**
     * Get all transcoding jobs
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $jobs = $this->service->all($request->all());

            Log::info('Fetched transcoding jobs', [
                'count' => count($jobs),
                'filters' => $request->all()
            ]);

            return response()->json([
                'status' => true,
                'data' => $jobs
            ]);
        } catch (Throwable $e) {
            Log::error('Error fetching transcoding jobs', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch transcoding jobs'
            ], 500);
        }
    }

    /**
     * Create a new transcoding job
     */
    public function store(TranscodingJobRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $job = $this->service->store($validated);

            Log::info('Transcoding job created successfully', [
                'job_id' => $job->id,
                'user_id' => $request->user()->id ?? null
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Transcoding job created successfully',
                'data' => $job
            ], 201);
        } catch (Throwable $e) {
            Log::error('Error creating transcoding job', [
                'error' => $e->getMessage(),
                'data' => $request->all(),
                'user_id' => $request->user()->id ?? null
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Failed to create transcoding job'
            ], 500);
        }
    }

    /**
     * Get a single transcoding job
     */
    public function show(string $id): JsonResponse
    {
        try {
            $job = TranscodingJob::with('video')->findOrFail($id);

            Log::info('Fetched transcoding job', [
                'job_id' => $id
            ]);

            return response()->json([
                'status' => true,
                'data' => $job
            ]);
        } catch (Throwable $e) {
            Log::warning('Transcoding job not found', [
                'job_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Transcoding job not found'
            ], 404);
        }
    }

    /**
     * Update a transcoding job
     */
    public function update(TranscodingJobRequest $request, string $id): JsonResponse
    {
        try {
            $job = TranscodingJob::findOrFail($id);
            $updated = $this->service->update($job, $request->validated());

            Log::info('Transcoding job updated successfully', [
                'job_id' => $id,
                'user_id' => $request->user()->id ?? null
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Transcoding job updated successfully',
                'data' => $updated
            ]);
        } catch (Throwable $e) {
            Log::error('Error updating transcoding job', [
                'job_id' => $id,
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Failed to update transcoding job'
            ], 500);
        }
    }

    /**
     * Delete a transcoding job
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $job = TranscodingJob::findOrFail($id);
            $this->service->delete($job);

            Log::info('Transcoding job deleted successfully', [
                'job_id' => $id,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Transcoding job deleted successfully'
            ]);
        } catch (Throwable $e) {
            Log::error('Error deleting transcoding job', [
                'job_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Failed to delete transcoding job'
            ], 500);
        }
    }
}
