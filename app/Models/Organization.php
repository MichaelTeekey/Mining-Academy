<?php

namespace App\Models;


class Organization extends BaseModel
{
    protected $fillable = ['name','website'];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
