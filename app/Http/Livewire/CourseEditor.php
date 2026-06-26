<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Services\CourseService;
use App\DTOs\CourseDTO;
use Illuminate\Support\Str;

class CourseEditor extends Component
{
    use WithFileUploads;

    public ?int $courseId = null;
    public string $title = '';
    public string $slug = '';
    public string $subtitle = '';
    public string $description = '';
    public float $price = 0;
    public ?float $discountPrice = null;
    public string $level = 'beginner';
    public ?int $categoryId = null;
    public bool $isFree = false;
    public bool $certificateEnabled = false;

    public $thumbnail;

    protected $rules = [
        'title' => 'required|string|max:255',
        'slug' => 'required|string|max:255|unique:courses',
        'subtitle' => 'nullable|string|max:255',
        'description' => 'required|string|max:5000',
        'price' => 'required|numeric|min:0',
        'level' => 'required|in:beginner,intermediate,advanced,all',
        'categoryId' => 'nullable|exists:categories,id',
    ];

    public function __construct(protected CourseService $courseService)
    {
        parent::__construct();
    }

    public function mount(?int $courseId = null)
    {
        if ($courseId) {
            $this->courseId = $courseId;
            $course = $this->courseService->findById($courseId);
            $this->fill($course->toArray());
        }
    }

    public function generateSlug()
    {
        $this->slug = Str::slug($this->title);
    }

    public function save()
    {
        $this->validate();

        $dto = CourseDTO::fromArray([
            'title' => $this->title,
            'slug' => $this->slug,
            'instructor_id' => auth()->id(),
            'subtitle' => $this->subtitle,
            'description' => $this->description,
            'price' => $this->price,
            'discount_price' => $this->discountPrice,
            'level' => $this->level,
            'category_id' => $this->categoryId,
            'is_free' => $this->isFree,
            'certificate_enabled' => $this->certificateEnabled,
        ]);

        if ($this->courseId) {
            $this->courseService->updateCourse($this->courseId, $dto);
            session()->flash('success', 'Course updated successfully!');
        } else {
            $this->courseService->createCourse($dto);
            session()->flash('success', 'Course created successfully!');
        }

        $this->redirect(route('courses.index'));
    }

    public function render()
    {
        return view('livewire.course-editor');
    }
}
