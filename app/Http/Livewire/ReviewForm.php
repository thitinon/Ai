<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Course;
use App\Models\Review;

class ReviewForm extends Component
{
    public Course $course;
    public int $rating = 5;
    public string $comment = '';
    public bool $isSubmitting = false;

    protected $rules = [
        'rating' => 'required|integer|between:1,5',
        'comment' => 'required|string|min:10|max:1000',
    ];

    public function submit()
    {
        if (! auth()->check()) {
            return redirect(route('login'));
        }

        $this->validate();

        $this->isSubmitting = true;

        try {
            Review::create([
                'user_id' => auth()->id(),
                'course_id' => $this->course->id,
                'rating' => $this->rating,
                'comment' => $this->comment,
                'is_verified_purchase' => $this->isVerifiedPurchase(),
            ]);

            // Update course rating
            $this->updateCourseRating();

            session()->flash('success', 'Review submitted successfully!');
            $this->reset(['rating', 'comment']);
            $this->dispatch('reviewSubmitted');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to submit review.');
        } finally {
            $this->isSubmitting = false;
        }
    }

    private function isVerifiedPurchase(): bool
    {
        return $this->course->enrollments()
            ->where('user_id', auth()->id())
            ->where('payment_id', '!=', null)
            ->exists();
    }

    private function updateCourseRating(): void
    {
        $avgRating = $this->course->reviews()->avg('rating');
        $ratingCount = $this->course->reviews()->count();

        $this->course->update([
            'rating_avg' => round($avgRating, 1),
            'rating_count' => $ratingCount,
        ]);
    }

    public function render()
    {
        return view('livewire.review-form');
    }
}
