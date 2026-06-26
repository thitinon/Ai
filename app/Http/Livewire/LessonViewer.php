<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Actions\MarkLessonCompleteAction;
use App\Models\Lesson;

class LessonViewer extends Component
{
    public Lesson $lesson;
    public bool $isCompleted = false;
    public int $watchedSeconds = 0;

    protected $listeners = ['videoEnded' => 'completeLesson'];

    public function __construct(protected MarkLessonCompleteAction $markCompleteAction)
    {
        parent::__construct();
    }

    public function mount()
    {
        if (auth()->check()) {
            $progress = $this->lesson->progress()
                ->where('user_id', auth()->id())
                ->first();
            $this->isCompleted = $progress?->is_completed ?? false;
            $this->watchedSeconds = $progress?->watch_seconds ?? 0;
        }
    }

    public function completeLesson()
    {
        if (! auth()->check()) {
            return;
        }

        $this->markCompleteAction->execute(auth()->id(), $this->lesson->id);
        $this->isCompleted = true;
        $this->dispatch('lessonCompleted', lessonId: $this->lesson->id);
    }

    public function updateWatchTime(int $seconds)
    {
        if (! auth()->check()) {
            return;
        }

        $this->watchedSeconds = $seconds;
        $this->lesson->progress()
            ->where('user_id', auth()->id())
            ->update(['watch_seconds' => $seconds]);
    }

    public function render()
    {
        return view('livewire.lesson-viewer', [
            'lesson' => $this->lesson,
            'isCompleted' => $this->isCompleted,
        ]);
    }
}
