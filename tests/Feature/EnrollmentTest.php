<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Foundation\Testing	ests\RefreshDatabase;
use Tests\TestCase;

class EnrollmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_enroll_in_published_course()
    {
        $student = User::factory()->student()->create();
        $course = Course::factory()->published()->create();

        $this->actingAs($student)
            ->post(route('enrollments.store'), ['course_id' => $course->id])
            ->assertRedirect();

        $this->assertDatabaseHas('enrollments', [
            'user_id' => $student->id,
            'course_id' => $course->id,
        ]);
    }

    public function test_student_cannot_enroll_twice()
    {
        $student = User::factory()->student()->create();
        $course = Course::factory()->published()->create();

        Enrollment::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'enrolled_at' => now(),
        ]);

        $this->actingAs($student)
            ->post(route('enrollments.store'), ['course_id' => $course->id])
            ->assertSessionHasErrors();
    }

    public function test_progress_updates_correctly()
    {
        $enrollment = Enrollment::factory()->create();

        $this->assertEquals(0, $enrollment->progress_percent);

        $enrollment->update(['progress_percent' => 50]);

        $this->assertEquals(50, $enrollment->fresh()->progress_percent);
    }
}
