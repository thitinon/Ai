<?php

namespace App\DTOs;

class CourseDTO
{
    public function __construct(
        public string $title,
        public string $slug,
        public int $instructorId,
        public ?int $categoryId = null,
        public ?string $subtitle = null,
        public ?string $description = null,
        public ?array $requirements = null,
        public ?array $objectives = null,
        public ?string $targetAudience = null,
        public string $level = 'all',
        public ?string $language = 'en',
        public float $price = 0.0,
        public ?float $discountPrice = null,
        public ?string $thumbnail = null,
        public ?string $previewVideo = null,
        public string $status = 'draft',
        public bool $isFree = false,
        public bool $certificateEnabled = false,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['title'],
            slug: $data['slug'],
            instructorId: $data['instructor_id'],
            categoryId: $data['category_id'] ?? null,
            subtitle: $data['subtitle'] ?? null,
            description: $data['description'] ?? null,
            requirements: $data['requirements'] ?? null,
            objectives: $data['objectives'] ?? null,
            targetAudience: $data['target_audience'] ?? null,
            level: $data['level'] ?? 'all',
            language: $data['language'] ?? 'en',
            price: (float) ($data['price'] ?? 0),
            discountPrice: isset($data['discount_price']) ? (float) $data['discount_price'] : null,
            thumbnail: $data['thumbnail'] ?? null,
            previewVideo: $data['preview_video'] ?? null,
            status: $data['status'] ?? 'draft',
            isFree: (bool) ($data['is_free'] ?? false),
            certificateEnabled: (bool) ($data['certificate_enabled'] ?? false),
        );
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'slug' => $this->slug,
            'instructor_id' => $this->instructorId,
            'category_id' => $this->categoryId,
            'subtitle' => $this->subtitle,
            'description' => $this->description,
            'requirements' => $this->requirements,
            'objectives' => $this->objectives,
            'target_audience' => $this->targetAudience,
            'level' => $this->level,
            'language' => $this->language,
            'price' => $this->price,
            'discount_price' => $this->discountPrice,
            'thumbnail' => $this->thumbnail,
            'preview_video' => $this->previewVideo,
            'status' => $this->status,
            'is_free' => $this->isFree,
            'certificate_enabled' => $this->certificateEnabled,
        ];
    }
}
