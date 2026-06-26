<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'avatar' => $this->faker->imageUrl(200, 200, 'people'),
            'role' => $this->faker->randomElement(['student', 'instructor', 'admin']),
            'bio' => $this->faker->paragraph(),
            'headline' => $this->faker->sentence(5),
            'social_links' => [
                'twitter' => 'https://twitter.com/' . Str::slug($this->faker->userName()),
                'linkedin' => 'https://linkedin.com/in/' . Str::slug($this->faker->userName()),
            ],
            'remember_token' => Str::random(10),
        ];
    }

    public function student(): self
    {
        return $this->state(fn (array $attributes) => ['role' => 'student']);
    }

    public function instructor(): self
    {
        return $this->state(fn (array $attributes) => ['role' => 'instructor']);
    }

    public function admin(): self
    {
        return $this->state(fn (array $attributes) => ['role' => 'admin']);
    }

    public function unverified(): self
    {
        return $this->state(fn (array $attributes) => ['email_verified_at' => null]);
    }
}
