<?php

namespace App\Models;


class Module extends BaseModel
{
    protected $fillable = ['course_version_id','title','order'];

    public function courseVersion()
    {
        return $this->belongsTo(CourseVersion::class);
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }
}

