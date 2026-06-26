<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Course;

class ReviewList extends Component
{
    use WithPagination;

    public Course $course;
    public int $perPage = 10;

    public function render()
    {
        $reviews = $this->course->reviews()
            ->with('user')
            ->latest('created_at')
            ->paginate($this->perPage);

        return view('livewire.review-list', [
            'reviews' => $reviews,
        ]);
    }
}
