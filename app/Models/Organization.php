<?php

namespace App\Models;


class Organization extends BaseModel
{
    protected $fillable = ['name','website'];

    public function users()
    {
        return $this->hasMany(User::class);
    }
    public function courses()
    {
        return $this->hasManyThrough(Course::class, User::class, 'organization_id', 'instructor_id');
    }

}
