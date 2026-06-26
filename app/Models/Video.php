<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $fillable = [
        'lesson_id',
        'original_path',
        'hls_path',
        'poster_path',
        'duration_seconds',
        'size_bytes',
        'bitrate',
        'status',
        'metadata',
    ];

    protected $casts = [
        'duration_seconds' => 'integer',
        'size_bytes' => 'integer',
        'metadata' => 'array',
    ];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    public function scopeReady($query)
    {
        return $query->where('status', 'ready');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }
}
