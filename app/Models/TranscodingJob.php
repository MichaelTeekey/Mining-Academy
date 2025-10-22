<?php

namespace App\Models;

class TranscodingJob extends BaseModel
{
    protected $fillable = ['video_id','status','settings','error_message'];

    protected $casts = [
        'settings' => 'array',
    ];

    public function video()
    {
        return $this->belongsTo(Video::class);
    }
}
