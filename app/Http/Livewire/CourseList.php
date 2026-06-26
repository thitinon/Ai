<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Services\CourseService;

class CourseList extends Component
{
    use WithPagination;

    public string $search = '';
    public int $perPage = 12;
    public ?int $categoryId = null;

    protected $queryString = ['search', 'categoryId'];

    public function __construct(protected CourseService $courseService)
    {
        parent::__construct();
    }

    public function updated($property)
    {
        if ($property === 'search' || $property === 'categoryId') {
            $this->resetPage();
        }
    }

    public function render()
    {
        $courses = $this->search
            ? $this->courseService->search($this->search, $this->perPage)
            : ($this->categoryId
                ? $this->courseService->listByCategory($this->categoryId, $this->perPage)
                : $this->courseService->listPublished($this->perPage));

        return view('livewire.course-list', [
            'courses' => $courses,
        ]);
    }
}
