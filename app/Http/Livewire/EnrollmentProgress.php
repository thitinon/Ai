<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Services\EnrollmentService;
use App\Models\Enrollment;

class EnrollmentProgress extends Component
{
    public Enrollment $enrollment;
    public float $progressPercent;

    protected $listeners = ['lessonCompleted' => 'refreshProgress'];

    public function __construct(protected EnrollmentService $enrollmentService)
    {
        parent::__construct();
    }

    public function mount()
    {
        $this->progressPercent = $this->enrollment->progress_percent;
    }

    public function refreshProgress()
    {
        $this->enrollment = $this->enrollment->refresh();
        $this->progressPercent = $this->enrollment->progress_percent;
    }

    public function render()
    {
        return view('livewire.enrollment-progress', [
            'progressPercent' => $this->progressPercent,
            'enrollment' => $this->enrollment,
        ]);
    }
}
