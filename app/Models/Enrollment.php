<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    protected $fillable = [
        'user_id','course_id','enrolled_at','completed_at','last_accessed_at','progress_percent','payment_id'
    ];

    protected $casts = [
        'enrolled_at' => 'datetime',
        'completed_at' => 'datetime',
        'last_accessed_at' => 'datetime',
        'progress_percent' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function payment()
    {
        return $this->belongsTo(Order::class, 'payment_id');
    }

    public function scopeActive($query)
    {
        return $query->whereNull('completed_at');
    }
}
