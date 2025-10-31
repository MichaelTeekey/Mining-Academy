<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CourseRunRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        
        $user = auth()->user();
        if (!$user || !in_array($user->role ?? null, ['admin', 'instructor'])) {
            abort(403, 'Only admins and instructors can create course runs.');
        }
        return [
            'course_id' => 'required|uuid|exists:courses,id',
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ];
    }
}
