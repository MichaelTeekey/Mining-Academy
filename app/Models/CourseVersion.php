<?php

namespace App\Models;


class CourseVersion extends BaseModel
{
    protected $fillable = ['course_id','version_number','snapshot'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function modules()
    {
        return $this->hasMany(Module::class);
    }
}