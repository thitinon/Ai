<?php

namespace Tests\Unit\Services;

use App\Services\CourseService;
use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseServiceTest extends TestCase
{
    use RefreshDatabase;

    protected CourseService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(CourseService::class);
    }

    public function test_list_published_courses()
    {
        Course::factory()->published()->count(5)->create();
        Course::factory()->create(['status' => 'draft']);

        $courses = $this->service->listPublished(10);

        $this->assertCount(5, $courses);
    }

    public function test_search_courses()
    {
        Course::factory()->published()->create(['title' => 'Laravel Basics']);
        Course::factory()->published()->create(['title' => 'Vue.js Advanced']);

        $results = $this->service->search('Laravel', 10);

        $this->assertGreaterThanOrEqual(1, $results->count());
    }

    public function test_get_featured_courses()
    {
        Course::factory()->published()->create(['enrolled_count' => 500]);
        Course::factory()->published()->create(['enrolled_count' => 50]);
        Course::factory()->published()->create(['enrolled_count' => 5]);

        $featured = $this->service->getFeaturedCourses(2);

        $this->assertCount(2, $featured);
        $this->assertEquals(500, $featured[0]->enrolled_count);
    }
}
