<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

class Course extends Model
{
    use SoftDeletes, Searchable;

    protected $fillable = [
        'instructor_id','category_id','title','slug','subtitle','description',
        'requirements','objectives','target_audience','level','language','price',
        'discount_price','thumbnail','preview_video','status','is_free','certificate_enabled',
        'total_duration_seconds','total_lessons','enrolled_count','rating_avg','rating_count','published_at'
    ];

    protected $casts = [
        'requirements' => 'array',
        'objectives' => 'array',
        'is_free' => 'boolean',
        'certificate_enabled' => 'boolean',
        'published_at' => 'datetime',
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'rating_avg' => 'float',
    ];

    // relationships
    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function tags()
    {
        return $this->hasMany(CourseTag::class);
    }

    public function sections()
    {
        return $this->hasMany(Section::class)->orderBy('sort_order');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published')->whereNotNull('published_at');
    }

    public function scopeFree($query)
    {
        return $query->where('is_free', true);
    }

    public function scopePriced($query)
    {
        return $query->where('price', '>', 0);
    }

    // accessor
    public function getEffectivePriceAttribute()
    {
        return $this->discount_price ?? $this->price;
    }

    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'description' => strip_tags($this->description),
            'instructor' => $this->instructor?->name,
        ];
    }
}
