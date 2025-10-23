<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Routing\Controller as Controller;
use App\Http\Requests\ModuleRequest;
use App\Models\Module;
use App\Services\ModuleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use Throwable;

class ModuleController extends Controller
{
    protected ModuleService $service;

    public function __construct(ModuleService $service)
    {
        $this->service = $service;
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $modules = $this->service->all($request->all());
            Log::info('Fetched modules', ['count' => count($modules)]);
            return response()->json([
                'status' => true,
                'data' => $modules
            ]);
        } catch (Throwable $e) {
            log::error('Failed to fetch modules', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch modules',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(ModuleRequest $request): JsonResponse
    {
        try {
            $module = $this->service->store($request->validated());
            Log::info('Module created', ['module_id' => $module->id]);
            return response()->json([
                'status' => true,
                'message' => 'Module created successfully',
                'data' => $module
            ], 201);
        } catch (Throwable $e) {
            Log::error('Failed to create module', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => false,
                'message' => 'Failed to create module',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(string $id): JsonResponse
    {
        try {
            $module = Module::with('lessons', 'courseVersion')->findOrFail($id);
            return response()->json([
                'status' => true,
                'data' => $module
            ]);
        } catch (Throwable $e) {
            Log::error('Module not found', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => false,
                'message' => 'Module not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(ModuleRequest $request, string $id): JsonResponse
    {
        try {
            $module = Module::findOrFail($id);
            $module = $this->service->update($module, $request->validated());

            return response()->json([
                'status' => true,
                'message' => 'Module updated successfully',
                'data' => $module
            ]);
        } catch (Throwable $e) {
            Log::error('Failed to update module', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => false,
                'message' => 'Failed to update module',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $module = Module::findOrFail($id);
            $this->service->delete($module);

            return response()->json([
                'status' => true,
                'message' => 'Module deleted successfully'
            ]);
        } catch (Throwable $e) {
            Log::error('Failed to delete module', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete module',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
