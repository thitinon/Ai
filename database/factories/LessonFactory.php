<?php

namespace Database\Factories;

use App\Models\Lesson;
use App\Models\Section;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class LessonFactory extends Factory
{
    protected $model = Lesson::class;

    public function definition(): array
    {
        $title = $this->faker->sentence(4);
        return [
            'section_id' => Section::factory(),
            'title' => $title,
            'slug' => Str::slug($title),
            'type' => $this->faker->randomElement(['video', 'text', 'quiz']),
            'content' => $this->faker->paragraphs(5, true),
            'video_url' => null,
            'video_duration_seconds' => $this->faker->numberBetween(300, 3600),
            'is_free_preview' => $this->faker->boolean(10),
            'sort_order' => 0,
            'is_published' => true,
        ];
    }

    public function video(): self
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'video',
            'video_url' => 'https://example.com/videos/' . Str::random(10) . '/master.m3u8',
        ]);
    }
}
