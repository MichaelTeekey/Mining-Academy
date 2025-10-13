<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;

class CourseController extends Controller
{
    //
    public function index()
    {
        return Course::with('instructor')->where('status','published')->get();
    }

    public function show($id)
    {
        $course = Course::with(['instructor','courseVersions.modules.lessons'])
            ->findOrFail($id);
        return response()->json($course);
    }

    public function store(StoreCourseRequest $request)
    {
        $course = Course::create([
            'title' => $request->title,
            'description' => $request->description,
            'price' => $request->price,
            'is_free' => $request->is_free,
            'instructor_id' => $request->user()->id,
        ]);
        return response()->json($course, 201);
    }

    public function update(UpdateCourseRequest $request, $id)
    {
        $course = Course::findOrFail($id);
        $course->update($request->validated());
        return response()->json($course);
    }

    public function destroy($id)
    {
        Course::findOrFail($id)->delete();
        return response()->json(['message' => 'Course deleted']);
    }
}
