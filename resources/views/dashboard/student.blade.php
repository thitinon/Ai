<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <nav class="bg-white shadow">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold">{{ config('app.name') }}</h1>
            <div>
                <span>{{ auth()->user()->name }}</span>
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="ml-4 text-blue-600">Logout</a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8">
        <h2 class="text-3xl font-bold mb-8">My Learning Dashboard</h2>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-white p-6 rounded shadow">
                <h3 class="text-gray-500 text-sm font-semibold">ENROLLED</h3>
                <p class="text-3xl font-bold">{{ $stats['total_enrolled'] }}</p>
            </div>
            <div class="bg-white p-6 rounded shadow">
                <h3 class="text-gray-500 text-sm font-semibold">IN PROGRESS</h3>
                <p class="text-3xl font-bold">{{ $stats['in_progress'] }}</p>
            </div>
            <div class="bg-white p-6 rounded shadow">
                <h3 class="text-gray-500 text-sm font-semibold">COMPLETED</h3>
                <p class="text-3xl font-bold">{{ $stats['completed'] }}</p>
            </div>
            <div class="bg-white p-6 rounded shadow">
                <h3 class="text-gray-500 text-sm font-semibold">AVG PROGRESS</h3>
                <p class="text-3xl font-bold">{{ round($stats['avg_progress'], 0) }}%</p>
            </div>
        </div>

        <!-- In Progress -->
        <div class="mb-8">
            <h3 class="text-2xl font-semibold mb-4">Continue Learning</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($inProgress as $enrollment)
                <div class="bg-white rounded shadow overflow-hidden">
                    <img src="{{ $enrollment->course->thumbnail }}" class="w-full h-40 object-cover" alt="{{ $enrollment->course->title }}">
                    <div class="p-4">
                        <h4 class="font-semibold truncate">{{ $enrollment->course->title }}</h4>
                        <p class="text-sm text-gray-600">{{ $enrollment->course->instructor->name }}</p>
                        <div class="mt-3 bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $enrollment->progress_percent }}%"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">{{ round($enrollment->progress_percent, 0) }}% Complete</p>
                        <a href="#" class="mt-3 block bg-blue-600 text-white text-center py-2 rounded">Continue</a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Recommendations -->
        @if($recommendedCourses->count())
        <div>
            <h3 class="text-2xl font-semibold mb-4">Recommended For You</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($recommendedCourses as $course)
                <div class="bg-white rounded shadow overflow-hidden">
                    <img src="{{ $course->thumbnail }}" class="w-full h-40 object-cover" alt="{{ $course->title }}">
                    <div class="p-4">
                        <h4 class="font-semibold truncate">{{ $course->title }}</h4>
                        <p class="text-sm text-gray-600">{{ $course->instructor->name }}</p>
                        <p class="text-lg font-bold mt-2">฿{{ $course->effective_price }}</p>
                        <button class="mt-3 w-full bg-green-600 text-white py-2 rounded">Enroll Now</button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</body>
</html>
