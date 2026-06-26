<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Services\EnrollmentService;
use App\Models\Course;

class EnrollButton extends Component
{
    public Course $course;
    public bool $isEnrolled = false;
    public bool $isLoading = false;

    protected $listeners = ['courseEnrolled' => 'handleEnrolled'];

    public function __construct(protected EnrollmentService $enrollmentService)
    {
        parent::__construct();
    }

    public function mount()
    {
        if (auth()->check()) {
            $this->isEnrolled = (bool) $this->enrollmentService->getEnrollment(
                auth()->id(),
                $this->course->id
            );
        }
    }

    public function enroll()
    {
        if (! auth()->check()) {
            return redirect(route('login'));
        }

        $this->isLoading = true;

        try {
            $this->enrollmentService->enrollUser(auth()->id(), $this->course->id);
            $this->isEnrolled = true;
            session()->flash('success', 'Enrolled in course successfully!');
            $this->dispatch('courseEnrolled', courseId: $this->course->id);
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to enroll in course.');
        } finally {
            $this->isLoading = false;
        }
    }

    public function render()
    {
        return view('livewire.enroll-button');
    }
}
