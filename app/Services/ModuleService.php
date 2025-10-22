<?php

namespace App\Services;

use App\Models\Module;
use Illuminate\Support\Facades\DB;

class ModuleService
{
    public function all(array $filters = [])
    {
        $query = Module::query()->with('courseVersion');

        if (isset($filters['course_version_id'])) {
            $query->where('course_version_id', $filters['course_version_id']);
        }

        return $query->orderBy('order')->paginate($filters['per_page'] ?? 10);
    }

    public function store(array $data): Module
    {
        return DB::transaction(fn() => Module::create($data));
    }

    public function update(Module $module, array $data): Module
    {
        $module->update($data);
        return $module->fresh('courseVersion');
    }

    public function delete(Module $module): void
    {
        $module->delete();
    }
}
