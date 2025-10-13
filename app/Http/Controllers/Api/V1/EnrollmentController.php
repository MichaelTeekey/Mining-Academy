<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Enrollment;
use App\Http\Requests\EnrollmentRequest;

class EnrollmentController extends Controller
{
    //
    public function store(EnrollmentRequest $request)
    {
        $enroll = Enrollment::firstOrCreate([
            'user_id' => $request->user()->id,
            'course_run_id' => $request->course_run_id,
        ]);

        return response()->json($enroll, 201);
    }

    public function myCourses(Request $request)
    {
        $courses = Enrollment::where('user_id', $request->user()->id)
            ->with('courseRun.course')
            ->get();

        return response()->json($courses);
    }
}
