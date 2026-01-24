<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InterviewAnswer extends Model
{
    protected $guarded = [];

    public function question()
    {
        return $this->belongsTo(InterviewQuestion::class, 'interview_question_id');
    }

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }
}