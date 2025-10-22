<?php

namespace App\Models;

class Video extends BaseModel
{
    protected $fillable = ['media_file_id','title','description'];

    public function mediaFile()
    {
        return $this->belongsTo(MediaFile::class);
    }

    public function renditions()
    {
        return $this->hasMany(VideoRendition::class);
    }
}
