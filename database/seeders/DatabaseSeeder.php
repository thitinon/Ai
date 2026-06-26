<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Course;
use App\Models\Section;
use App\Models\Lesson;
use App\Models\Enrollment;
use App\Models\Order;
use App\Models\Review;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin
        User::factory()->admin()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);

        // Create instructors
        $instructors = User::factory()->instructor()->count(5)->create();

        // Create students
        $students = User::factory()->student()->count(20)->create();

        // Create categories
        $categories = Category::factory()->count(8)->create();

        // Create courses per instructor
        $instructors->each(function ($instructor) use ($categories) {
            $courses = Course::factory()
                ->published()
                ->count($this->faker->numberBetween(2, 4))
                ->for($instructor, 'instructor')
                ->recycle($categories)
                ->create();

            // Create sections and lessons per course
            $courses->each(function ($course) {
                $sections = Section::factory()
                    ->count($this->faker->numberBetween(3, 6))
                    ->for($course)
                    ->create();

                $sections->each(function ($section, $idx) {
                    Lesson::factory()
                        ->video()
                        ->count($this->faker->numberBetween(3, 8))
                        ->for($section)
                        ->create([
                            'sort_order' => $idx * 10,
                        ]);
                });
            });
        });

        // Create enrollments
        $students->each(function ($student) {
            $courses = Course::where('status', 'published')
                ->inRandomOrder()
                ->limit($this->faker->numberBetween(1, 5))
                ->get();

            $courses->each(function ($course) use ($student) {
                Enrollment::factory()
                    ->for($student, 'user')
                    ->for($course)
                    ->create();
            });
        });

        // Create reviews
        Review::factory()->count(50)->create();
    }
}
