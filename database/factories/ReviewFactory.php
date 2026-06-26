<?php

namespace Database\Factories;

use App\Models\Review;
use App\Models\User;
use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewFactory extends Factory
{
    protected $model = Review::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->student(),
            'course_id' => Course::factory()->published(),
            'rating' => $this->faker->numberBetween(1, 5),
            'comment' => $this->faker->paragraph(),
            'is_verified_purchase' => $this->faker->boolean(80),
            'helpful_count' => $this->faker->numberBetween(0, 100),
        ];
    }
}
