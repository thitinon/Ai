<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    protected $fillable = [
        'section_id','title','slug','type','content','video_url',
        'video_duration_seconds','is_free_preview','sort_order','is_published'
    ];

    protected $casts = [
        'video_duration_seconds' => 'integer',
        'is_free_preview' => 'boolean',
        'is_published' => 'boolean',
    ];

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function quiz()
    {
        return $this->hasOne(Quiz::class);
    }

    public function progress()
    {
        return $this->hasMany(LessonProgress::class);
    }

    // scope
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }
}
