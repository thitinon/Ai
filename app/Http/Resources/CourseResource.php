<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'subtitle' => $this->subtitle,
            'description' => $this->description,
            'level' => $this->level,
            'language' => $this->language,
            'price' => $this->price,
            'discount_price' => $this->discount_price,
            'effective_price' => $this->effective_price,
            'thumbnail' => $this->thumbnail,
            'status' => $this->status,
            'is_free' => $this->is_free,
            'certificate_enabled' => $this->certificate_enabled,
            'total_duration_seconds' => $this->total_duration_seconds,
            'total_lessons' => $this->total_lessons,
            'enrolled_count' => $this->enrolled_count,
            'rating_avg' => $this->rating_avg,
            'rating_count' => $this->rating_count,
            'published_at' => $this->published_at?->toIso8601String(),
            'instructor' => new UserResource($this->whenLoaded('instructor')),
            'category' => $this->whenLoaded('category', function () {
                return [
                    'id' => $this->category->id,
                    'name' => $this->category->name,
                    'slug' => $this->category->slug,
                ];
            }),
            'sections' => SectionResource::collection($this->whenLoaded('sections')),
        ];
    }
}
