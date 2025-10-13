<?php

namespace App\Models;


class Enrollment extends BaseModel
{
    protected $fillable = ['user_id','course_run_id','status','enrolled_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function courseRun()
    {
        return $this->belongsTo(CourseRun::class);
    }
}

