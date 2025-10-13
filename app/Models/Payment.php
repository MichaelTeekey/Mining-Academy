<?php

namespace App\Models;


class Payment extends BaseModel
{
    protected $fillable = ['user_id','course_run_id','payment_method','amount','currency','transaction_id','status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function courseRun()
    {
        return $this->belongsTo(CourseRun::class);
    }
}

