@extends('layouts.app')

@section('content')
    <div class="flex min-h-screen bg-gray-100">
        <div class="flex-1 ml-64 p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-semibold text-gray-700">Events Dashboard</h1>
            </div>

            <!-- Stats Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
                <!-- Active Events -->
                <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-green-500 hover:-translate-y-1 transition-transform">
                    <h3 class="text-gray-500 text-sm font-medium">Active Events</h3>
                    <div class="text-green-500 text-4xl font-bold my-2">{{ $activeEventsCount }}</div>
                    <p class="text-gray-500 text-sm">Currently happening now</p>
                </div>

                <!-- Upcoming Events -->
                <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-blue-500 hover:-translate-y-1 transition-transform">
                    <h3 class="text-gray-500 text-sm font-medium">Upcoming Events</h3>
                    <div class="text-blue-500 text-4xl font-bold my-2">{{ $upcomingEventsCount }}</div>
                    <p class="text-gray-500 text-sm">Scheduled for the future</p>
                </div>

                <!-- Passed Events -->
                <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-gray-400 hover:-translate-y-1 transition-transform">
                    <h3 class="text-gray-500 text-sm font-medium">Passed Events</h3>
                    <div class="text-gray-400 text-4xl font-bold my-2">{{ $passedEventsCount }}</div>
                    <p class="text-gray-500 text-sm">Completed events</p>
                </div>

                <!-- Delayed Events -->
                <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-red-500 hover:-translate-y-1 transition-transform">
                    <h3 class="text-gray-500 text-sm font-medium">Delayed Events</h3>
                    <div class="text-red-500 text-4xl font-bold my-2">{{ $delayedEventsCount }}</div>
                    <p class="text-gray-500 text-sm">Events behind schedule</p>
                </div>

                <!-- Due Events -->
                <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-yellow-500 hover:-translate-y-1 transition-transform">
                    <h3 class="text-gray-500 text-sm font-medium">Due Events</h3>
                    <div class="text-yellow-500 text-4xl font-bold my-2">{{ $dueEventsCount }}</div>
                    <p class="text-gray-500 text-sm">Approaching deadlines</p>
                </div>
            </div>

            <!-- Chart and Summary Row -->
            <div class="flex flex-col md:flex-row gap-6 mb-8">
                <!-- Chart (80% width) -->
                <div class="bg-white shadow-md rounded-lg overflow-hidden w-full md:w-4/5 p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Events Distribution</h2>
                    <div id="eventsChart" class="w-full"></div>
                </div>

                <!-- Summary (20% width) -->
                <div class="bg-white shadow-md rounded-lg overflow-hidden w-full md:w-1/5 p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Summary</h2>
                    <div class="space-y-3 text-[16px] text-gray-700 font-medium">
                        <div class="flex justify-between">
                            <span>Total Events:</span>
                            <span>{{ $activeEventsCount + $upcomingEventsCount + $passedEventsCount + $delayedEventsCount + $dueEventsCount }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-green-500">Active:</span>
                            <span>{{ $activeEventsCount }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-blue-500">Upcoming:</span>
                            <span>{{ $upcomingEventsCount }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Passed:</span>
                            <span>{{ $passedEventsCount }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-red-500">Delayed:</span>
                            <span>{{ $delayedEventsCount }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-yellow-500">Due:</span>
                            <span>{{ $dueEventsCount }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const options = {
                series: [{
                    name: 'Events',
                    data: [
                        {{ $activeEventsCount }},
                        {{ $upcomingEventsCount }},
                        {{ $passedEventsCount }},
                        {{ $delayedEventsCount }},
                        {{ $dueEventsCount }}
                    ]
                }],
                chart: {
                    type: 'bar',
                    height: 380, // increased height
                    toolbar: { show: false },
                    animations: {
                        enabled: true,
                        easing: 'easeout',
                        speed: 800
                    }
                },
                colors: ['#2ecc71', '#3498db', '#95a5a6', '#e74c3c', '#f39c12'],
                plotOptions: {
                    bar: {
                        distributed: true,
                        borderRadius: 6,
                        columnWidth: '70%',
                        dataLabels: {
                            position: 'top'
                        }
                    }
                },
                dataLabels: {
                    enabled: true,
                    formatter: function (val) {
                        return val;
                    },
                    offsetY: -20,
                    style: {
                        fontSize: '13px',
                        colors: ['#333']
                    }
                },
                xaxis: {
                    categories: ['Active', 'Upcoming', 'Passed', 'Delayed', 'Due'],
                    axisBorder: { show: false },
                    axisTicks: { show: false }
                },
                yaxis: {
                    show: false,
                    max: function (max) { return max * 1.2; }
                },
                grid: {
                    show: false,
                    padding: {
                        top: -30,
                        right: 0,
                        bottom: -10,
                        left: 0
                    }
                },
                tooltip: {
                    enabled: false
                }
            };

            const chart = new ApexCharts(document.querySelector("#eventsChart"), options);
            chart.render();
        });
    </script>
@endsection
