<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizAttempt extends Model
{
    protected $fillable = ['user_id','quiz_id','score','passed','answers','started_at','submitted_at'];

    protected $casts = [
        'score' => 'float',
        'passed' => 'boolean',
        'answers' => 'array',
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }
}
