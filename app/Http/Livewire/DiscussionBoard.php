<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Lesson;
use App\Models\Discussion;

class DiscussionBoard extends Component
{
    use WithPagination;

    public Lesson $lesson;
    public string $search = '';
    public int $perPage = 10;

    protected $queryString = ['search'];

    public function updated($property)
    {
        if ($property === 'search') {
            $this->resetPage();
        }
    }

    public function render()
    {
        $discussions = Discussion::where('lesson_id', $this->lesson->id)
            ->when($this->search, fn ($q) => $q->where('title', 'like', '%' . $this->search . '%'))
            ->orderByDesc('is_pinned')
            ->latest('updated_at')
            ->paginate($this->perPage);

        return view('livewire.discussion-board', [
            'discussions' => $discussions,
        ]);
    }
}
