<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ModuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {

        $user = auth()->user();
        if (!$user || !in_array($user->role ?? null, ['admin', 'instructor'])) {
            abort(403, 'Only admins and instructors can create course runs.');
        }
        return [
            'course_version_id' => 'required|uuid|exists:course_versions,id',
            'title' => 'required|string|max:255',
            'order' => 'nullable|integer|min:1',
        ]; 
    }
}
