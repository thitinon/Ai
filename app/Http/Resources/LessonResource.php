<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LessonResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'section_id' => $this->section_id,
            'title' => $this->title,
            'slug' => $this->slug,
            'type' => $this->type,
            'content' => $this->when($request->user()?->id === $this->section->course->instructor_id || $request->user()?->role === 'admin', $this->content),
            'video_url' => $this->video_url,
            'video_duration_seconds' => $this->video_duration_seconds,
            'is_free_preview' => $this->is_free_preview,
            'sort_order' => $this->sort_order,
            'is_published' => $this->is_published,
        ];
    }
}
