<?php

namespace App\Models;


class MediaFile extends BaseModel
{
    protected $fillable = ['file_name','file_path','mime_type','size','lesson_id'];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
}

