<?php

namespace Database\Factories;

use App\Models\Enrollment;
use App\Models\User;
use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

class EnrollmentFactory extends Factory
{
    protected $model = Enrollment::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->student(),
            'course_id' => Course::factory()->published(),
            'enrolled_at' => now()->subDays($this->faker->numberBetween(1, 30)),
            'completed_at' => null,
            'last_accessed_at' => now()->subHours($this->faker->numberBetween(1, 168)),
            'progress_percent' => $this->faker->randomFloat(1, 0, 100),
        ];
    }

    public function completed(): self
    {
        return $this->state(fn (array $attributes) => [
            'completed_at' => now()->subDays($this->faker->numberBetween(1, 30)),
            'progress_percent' => 100,
        ]);
    }
}
