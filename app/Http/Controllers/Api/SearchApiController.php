<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class SearchApiController extends Controller
{
    public function search(Request $request)
    {
        $validated = $request->validate([
            'q' => 'required|string|min:2|max:100',
            'filters' => 'nullable|array',
            'filters.level' => 'nullable|in:beginner,intermediate,advanced',
            'filters.is_free' => 'nullable|boolean',
            'filters.min_price' => 'nullable|numeric|min:0',
            'filters.max_price' => 'nullable|numeric|min:0',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        $query = Course::published()
            ->search($validated['q']);

        // Apply filters
        if ($validated['filters'] ?? null) {
            $filters = $validated['filters'];

            if ($filters['level'] ?? null) {
                $query->where('level', $filters['level']);
            }

            if (isset($filters['is_free'])) {
                $query->where('is_free', $filters['is_free']);
            }

            if ($filters['min_price'] ?? null) {
                $query->whereRaw('COALESCE(discount_price, price) >= ?', [$filters['min_price']]);
            }

            if ($filters['max_price'] ?? null) {
                $query->whereRaw('COALESCE(discount_price, price) <= ?', [$filters['max_price']]);
            }
        }

        $perPage = $validated['per_page'] ?? 12;
        $courses = $query->paginate($perPage);

        return response()->json([
            'data' => $courses->items(),
            'total' => $courses->total(),
            'per_page' => $courses->perPage(),
            'current_page' => $courses->currentPage(),
            'last_page' => $courses->lastPage(),
        ]);
    }

    public function suggestions(Request $request)
    {
        $validated = $request->validate([
            'q' => 'required|string|min:2|max:50',
        ]);

        // Suggest course titles and instructors
        $suggestions = Course::published()
            ->where('title', 'like', '%' . $validated['q'] . '%')
            ->orWhereHas('instructor', fn ($q) => $q->where('name', 'like', '%' . $validated['q'] . '%'))
            ->limit(10)
            ->pluck('title')
            ->toArray();

        return response()->json(['suggestions' => $suggestions]);
    }
}
