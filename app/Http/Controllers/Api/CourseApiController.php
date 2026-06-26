<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CourseResource;
use App\Services\CourseService;
use Illuminate\Http\Request;

class CourseApiController extends Controller
{
    public function __construct(protected CourseService $courseService)
    {
    }

    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 12);
        $search = $request->query('search');
        $categoryId = $request->query('category_id');

        if ($search) {
            $courses = $this->courseService->search($search, $perPage);
        } elseif ($categoryId) {
            $courses = $this->courseService->listByCategory($categoryId, $perPage);
        } else {
            $courses = $this->courseService->listPublished($perPage);
        }

        return CourseResource::collection($courses);
    }

    public function show($id)
    {
        $course = $this->courseService->findById($id);

        if (! $course) {
            return response()->json(['message' => 'Course not found'], 404);
        }

        $this->authorize('view', $course);

        return new CourseResource($course->load('instructor', 'category', 'sections.lessons'));
    }

    public function featured()
    {
        $courses = $this->courseService->getFeaturedCourses(6);
        return CourseResource::collection($courses);
    }
}
