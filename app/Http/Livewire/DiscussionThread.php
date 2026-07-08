<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Discussion;

class DiscussionThread extends Component
{
    public Discussion $discussion;
    public string $replyContent = '';
    public bool $isSubmitting = false;

    protected $rules = [
        'replyContent' => 'required|string|min:5|max:2000',
    ];

    public function addReply()
    {
        if (!auth()->check()) {
            return redirect(route('login'));
        }

        $this->validate();
        $this->isSubmitting = true;

        try {
            $this->discussion->replies()->create([
                'user_id' => auth()->id(),
                'content' => $this->replyContent,
                'is_instructor' => auth()->user()->id === $this->discussion->lesson->section->course->instructor_id,
            ]);

            $this->discussion->increment('reply_count');
            $this->replyContent = '';
            $this->discussion->refresh();
            session()->flash('success', 'Reply posted successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to post reply.');
        } finally {
            $this->isSubmitting = false;
        }
    }

    public function render()
    {
        return view('livewire.discussion-thread', [
            'discussion' => $this->discussion->load('replies.user', 'user'),
        ]);
    }
}
