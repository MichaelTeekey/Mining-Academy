<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Routing\Controller as Controller;
use App\Http\Requests\ModuleRequest;
use App\Models\Module;
use App\Services\ModuleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
        $modules = $this->service->all($request->all());
        return response()->json($modules);
    }

    public function store(ModuleRequest $request): JsonResponse
    {
        $module = $this->service->store($request->validated());
        return response()->json($module, 201);
    }

    public function show(string $id): JsonResponse
    {
        $module = Module::with('courseVersion')->findOrFail($id);
        return response()->json($module);
    }

    public function update(ModuleRequest $request, string $id): JsonResponse
    {
        $module = Module::findOrFail($id);
        $module = $this->service->update($module, $request->validated());
        return response()->json($module);
    }

    public function destroy(string $id): JsonResponse
    {
        $module = Module::findOrFail($id);
        $this->service->delete($module);

        return response()->json(['message' => 'Module deleted successfully']);
    }
}
