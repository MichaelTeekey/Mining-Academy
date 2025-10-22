<?php

namespace App\Models;

class VideoRendition extends BaseModel
{
    protected $fillable = ['video_id','resolution','file_path','mime_type','size'];

    public function video()
    {
        return $this->belongsTo(Video::class);
    }
}
