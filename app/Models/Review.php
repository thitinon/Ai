<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = ['user_id','course_id','rating','comment','is_verified_purchase','helpful_count'];

    protected $casts = [
        'rating' => 'integer',
        'is_verified_purchase' => 'boolean',
        'helpful_count' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function helpfulBy()
    {
        return $this->belongsToMany(User::class, 'review_helpful', 'review_id', 'user_id')->withTimestamps();
    }
}
