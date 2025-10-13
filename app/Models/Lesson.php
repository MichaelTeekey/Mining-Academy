<?php

namespace App\Models;


class Lesson extends BaseModel
{
    protected $fillable = ['module_id','title','content','order'];

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function mediaFiles()
    {
        return $this->hasMany(MediaFile::class);
    }
}

