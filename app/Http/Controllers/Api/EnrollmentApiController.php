<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EnrollmentResource;
use App\Services\EnrollmentService;
use Illuminate\Http\Request;

class EnrollmentApiController extends Controller
{
    public function __construct(protected EnrollmentService $enrollmentService)
    {
    }

    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 12);
        $enrollments = $this->enrollmentService->getUserEnrollments(auth()->id(), $perPage);
        return EnrollmentResource::collection($enrollments);
    }

    public function show($id)
    {
        $enrollment = $this->enrollmentService->getEnrollment(auth()->id(), $id);

        if (! $enrollment) {
            return response()->json(['message' => 'Enrollment not found'], 404);
        }

        return new EnrollmentResource($enrollment->load('course'));
    }

    public function updateProgress(Request $request, $enrollmentId)
    {
        $validated = $request->validate([
            'progress_percent' => 'required|numeric|between:0,100',
        ]);

        $enrollment = $this->enrollmentService->updateProgress(
            $enrollmentId,
            $validated['progress_percent']
        );

        return new EnrollmentResource($enrollment);
    }
}
