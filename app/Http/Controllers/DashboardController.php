<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Order;
use App\Models\User;
use App\Models\Enrollment;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();

        if ($user->role === 'admin') {
            return $this->adminDashboard();
        } elseif ($user->role === 'instructor') {
            return $this->instructorDashboard();
        } else {
            return $this->studentDashboard();
        }
    }

    protected function adminDashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'total_instructors' => User::where('role', 'instructor')->count(),
            'total_students' => User::where('role', 'student')->count(),
            'total_courses' => Course::count(),
            'published_courses' => Course::published()->count(),
            'total_revenue' => Order::paid()->sum('final_amount'),
            'this_month_revenue' => Order::paid()
                ->whereBetween('paid_at', [
                    Carbon::now()->startOfMonth(),
                    Carbon::now()->endOfMonth(),
                ])
                ->sum('final_amount'),
        ];

        // Revenue chart data
        $revenueChart = Order::paid()
            ->selectRaw('DATE(paid_at) as date, SUM(final_amount) as amount')
            ->whereBetween('paid_at', [
                Carbon::now()->subDays(30),
                Carbon::now(),
            ])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Top courses
        $topCourses = Course::orderByDesc('enrolled_count')
            ->limit(5)
            ->get();

        // Recent orders
        $recentOrders = Order::paid()
            ->with('user', 'items')
            ->latest('paid_at')
            ->limit(10)
            ->get();

        return view('dashboard.admin', compact(
            'stats',
            'revenueChart',
            'topCourses',
            'recentOrders'
        ));
    }

    protected function instructorDashboard()
    {
        $user = auth()->user();

        $stats = [
            'total_courses' => $user->courses()->count(),
            'published_courses' => $user->courses()->where('status', 'published')->count(),
            'total_students' => Enrollment::whereHas('course', fn ($q) => $q->where('instructor_id', $user->id))->distinct('user_id')->count('user_id'),
            'total_revenue' => Order::paid()
                ->whereHas('items', fn ($q) => $q->whereHas('course', fn ($c) => $c->where('instructor_id', $user->id)))
                ->sum('final_amount'),
        ];

        // Courses with stats
        $courses = $user->courses()
            ->withCount('enrollments')
            ->with('enrollments')
            ->latest('created_at')
            ->paginate(10);

        // Revenue chart
        $revenueChart = Order::paid()
            ->whereHas('items', fn ($q) => $q->whereHas('course', fn ($c) => $c->where('instructor_id', $user->id)))
            ->selectRaw('DATE(paid_at) as date, SUM(final_amount) as amount')
            ->whereBetween('paid_at', [
                Carbon::now()->subDays(30),
                Carbon::now(),
            ])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('dashboard.instructor', compact(
            'stats',
            'courses',
            'revenueChart'
        ));
    }

    protected function studentDashboard()
    {
        $user = auth()->user();

        $stats = [
            'total_enrolled' => $user->enrollments()->count(),
            'in_progress' => $user->enrollments()->active()->count(),
            'completed' => $user->enrollments()->whereNotNull('completed_at')->count(),
            'avg_progress' => $user->enrollments()->avg('progress_percent'),
        ];

        // In-progress courses
        $inProgress = $user->enrollments()
            ->active()
            ->with('course')
            ->orderByDesc('last_accessed_at')
            ->paginate(5);

        // Recently completed
        $completed = $user->enrollments()
            ->whereNotNull('completed_at')
            ->with('course')
            ->latest('completed_at')
            ->limit(5)
            ->get();

        // Recommendations based on completed courses
        $recommendedCourses = $this->getRecommendations($user);

        return view('dashboard.student', compact(
            'stats',
            'inProgress',
            'completed',
            'recommendedCourses'
        ));
    }

    private function getRecommendations(User $user)
    {
        $completedCourses = $user->enrollments()
            ->whereNotNull('completed_at')
            ->pluck('course_id')
            ->toArray();

        // Get categories from completed courses
        $categories = Course::whereIn('id', $completedCourses)
            ->pluck('category_id')
            ->toArray();

        // Find similar courses
        return Course::published()
            ->whereIn('category_id', $categories)
            ->whereNotIn('id', $completedCourses)
            ->inRandomOrder()
            ->limit(6)
            ->get();
    }
}
