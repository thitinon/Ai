<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        $name = $this->faker->word();
        return [
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
            'icon' => $this->faker->randomElement(['📚', '💻', '🎨', '🎬', '🎵', '🏋️']),
            'parent_id' => null,
            'sort_order' => 0,
        ];
    }
}
