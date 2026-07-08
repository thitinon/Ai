<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Discussion extends Model
{
    protected $fillable = [
        'lesson_id','user_id','title','content','is_pinned','is_closed'
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'is_closed' => 'boolean',
    ];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function replies()
    {
        return $this->hasMany(DiscussionReply::class)->orderByDesc('is_accepted_answer')->latest();
    }

    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    public function scopeOpen($query)
    {
        return $query->where('is_closed', false);
    }
}
