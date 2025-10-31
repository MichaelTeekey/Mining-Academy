<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $user = $this->user();
        if (!$user || !in_array($user->role ?? null, ['admin', 'instructor'], true)) {
            abort(403, 'Only admins and instructors can perform this action.');
        }
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'is_free' => 'boolean',
            'status' => 'in:draft,published,archived',
        ];
    }
}
