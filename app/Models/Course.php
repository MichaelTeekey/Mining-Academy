<?php

namespace App\Models;


class Course extends BaseModel
{
    protected $fillable = ['title','description','price','is_free','status','instructor_id'];

    public function instructor()
    {
        return $this->belongsTo(User::class,'instructor_id');
    }

    public function courseVersions()
    {
        return $this->hasMany(CourseVersion::class);
    }

    public function courseRuns()
    {
        return $this->hasMany(CourseRun::class);
    }
}
