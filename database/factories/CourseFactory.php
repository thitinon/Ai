<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CourseFactory extends Factory
{
    protected $model = Course::class;

    public function definition(): array
    {
        $title = $this->faker->sentence(4);
        return [
            'instructor_id' => User::factory()->instructor(),
            'category_id' => Category::factory(),
            'title' => $title,
            'slug' => Str::slug($title),
            'subtitle' => $this->faker->sentence(),
            'description' => $this->faker->paragraphs(3, true),
            'requirements' => [
                'Basic understanding of ' . $this->faker->word(),
                'Familiarity with ' . $this->faker->word(),
            ],
            'objectives' => [
                'Learn ' . $this->faker->sentence(),
                'Master ' . $this->faker->sentence(),
                'Apply ' . $this->faker->sentence(),
            ],
            'target_audience' => $this->faker->paragraph(),
            'level' => $this->faker->randomElement(['beginner', 'intermediate', 'advanced', 'all']),
            'language' => 'en',
            'price' => $this->faker->randomElement([0, 29.99, 49.99, 99.99, 199.99]),
            'discount_price' => null,
            'thumbnail' => $this->faker->imageUrl(400, 300, 'education'),
            'preview_video' => null,
            'status' => 'draft',
            'is_free' => false,
            'certificate_enabled' => $this->faker->boolean(70),
            'total_duration_seconds' => $this->faker->numberBetween(3600, 36000),
            'total_lessons' => $this->faker->numberBetween(5, 50),
            'enrolled_count' => $this->faker->numberBetween(0, 1000),
            'rating_avg' => $this->faker->randomFloat(1, 1, 5),
            'rating_count' => $this->faker->numberBetween(0, 500),
            'published_at' => null,
        ];
    }

    public function published(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'published',
            'published_at' => now()->subDays($this->faker->numberBetween(1, 90)),
        ]);
    }

    public function free(): self
    {
        return $this->state(fn (array $attributes) => [
            'is_free' => true,
            'price' => 0,
        ]);
    }
}
