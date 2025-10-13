<?php

namespace App\Models;


class CourseRun extends BaseModel
{
    protected $fillable = ['course_id','name','start_date','end_date'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}

