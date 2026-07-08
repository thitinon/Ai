<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_instructor_can_view_own_draft_course()
    {
        $instructor = User::factory()->instructor()->create();
        $course = Course::factory()->create(['instructor_id' => $instructor->id, 'status' => 'draft']);

        $this->actingAs($instructor)
            ->get(route('courses.show', $course))
            ->assertOk();
    }

    public function test_student_cannot_view_draft_course()
    {
        $student = User::factory()->student()->create();
        $course = Course::factory()->create(['status' => 'draft']);

        $this->actingAs($student)
            ->get(route('courses.show', $course))
            ->assertForbidden();
    }

    public function test_anyone_can_view_published_course()
    {
        $course = Course::factory()->published()->create();

        $this->get(route('courses.show', $course))
            ->assertOk();
    }

    public function test_only_instructor_can_update_course()
    {
        $instructor = User::factory()->instructor()->create();
        $otherInstructor = User::factory()->instructor()->create();
        $course = Course::factory()->create(['instructor_id' => $instructor->id]);

        $this->actingAs($otherInstructor)
            ->patch(route('courses.update', $course), ['title' => 'New Title'])
            ->assertForbidden();

        $this->actingAs($instructor)
            ->patch(route('courses.update', $course), ['title' => 'New Title'])
            ->assertOk();
    }
}
