<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CourseVersionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = $this->user();
        if (!$user || !in_array($user->role ?? null, ['admin', 'instructor'], true)) {
            abort(403, 'Only admins and instructors can perform this action.');
        }
        return [
            'course_id' => 'required|uuid|exists:courses,id',
            'version_number' => 'required|string|max:20',
            'snapshot' => 'nullable|string',
        ];
    }
}
