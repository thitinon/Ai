<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instructor Dashboard - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <nav class="bg-white shadow">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold">{{ config('app.name') }} - Instructor</h1>
            <div>
                <span>{{ auth()->user()->name }}</span>
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="ml-4 text-blue-600">Logout</a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8">
        <h2 class="text-3xl font-bold mb-8">Your Dashboard</h2>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-white p-6 rounded shadow">
                <h3 class="text-gray-500 text-sm font-semibold">COURSES</h3>
                <p class="text-3xl font-bold">{{ $stats['total_courses'] }}</p>
            </div>
            <div class="bg-white p-6 rounded shadow">
                <h3 class="text-gray-500 text-sm font-semibold">PUBLISHED</h3>
                <p class="text-3xl font-bold">{{ $stats['published_courses'] }}</p>
            </div>
            <div class="bg-white p-6 rounded shadow">
                <h3 class="text-gray-500 text-sm font-semibold">TOTAL STUDENTS</h3>
                <p class="text-3xl font-bold">{{ $stats['total_students'] }}</p>
            </div>
            <div class="bg-white p-6 rounded shadow">
                <h3 class="text-gray-500 text-sm font-semibold">TOTAL REVENUE</h3>
                <p class="text-3xl font-bold">฿{{ number_format($stats['total_revenue'], 2) }}</p>
            </div>
        </div>

        <!-- Revenue Chart -->
        <div class="bg-white p-6 rounded shadow mb-8">
            <h3 class="text-lg font-semibold mb-4">Revenue Last 30 Days</h3>
            <canvas id="revenueChart" height="80"></canvas>
        </div>

        <!-- My Courses -->
        <div class="bg-white p-6 rounded shadow">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">My Courses</h3>
                <a href="#" class="bg-blue-600 text-white px-4 py-2 rounded">Create Course</a>
            </div>
            <table class="w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left">Course</th>
                        <th class="px-4 py-2 text-left">Status</th>
                        <th class="px-4 py-2 text-left">Students</th>
                        <th class="px-4 py-2 text-left">Rating</th>
                        <th class="px-4 py-2 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($courses as $course)
                    <tr class="border-t">
                        <td class="px-4 py-2">{{ $course->title }}</td>
                        <td class="px-4 py-2"><span class="px-2 py-1 bg-blue-100 text-blue-800 rounded">{{ ucfirst($course->status) }}</span></td>
                        <td class="px-4 py-2">{{ $course->enrollments_count }}</td>
                        <td class="px-4 py-2">⭐ {{ $course->rating_avg }}/5</td>
                        <td class="px-4 py-2"><a href="#" class="text-blue-600">Edit</a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('revenueChart').getContext('2d');
        const data = @json($revenueChart);
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.map(d => d.date),
                datasets: [{
                    label: 'Revenue (฿)',
                    data: data.map(d => d.amount),
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: true }
                }
            }
        });
    </script>
</body>
</html>
