<div x-data="dashboardDtr()" x-init="init()" class="p-6 bg-gradient-to-br from-indigo-100 to-white dark:from-gray-800 dark:to-gray-900 rounded-xl shadow-xl">
    <h2 class="text-2xl font-bold mb-4 text-gray-800 dark:text-gray-200">Employee DTR For the last 30 days</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Attendance Chart -->
        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300">
            <h3 class="text-lg font-semibold mb-2 text-gray-800 dark:text-gray-200">Attendance Overview</h3>
            <canvas id="attendanceChart"></canvas>
        </div>

        <!-- Overtime Chart -->
        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300">
            <h3 class="text-lg font-semibold mb-2 text-gray-800 dark:text-gray-200">Overtime Trends</h3>
            <canvas id="overtimeChart"></canvas>
        </div>

        <!-- Late Chart -->
        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300">
            <h3 class="text-lg font-semibold mb-2 text-gray-800 dark:text-gray-200">Late Arrivals</h3>
            <canvas id="lateChart"></canvas>
        </div>

        <!-- Summary Stats -->
        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-md">
            <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-200">30-Day Summary</h3>
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-green-400 p-4 rounded-lg shadow text-white">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-user-check text-2xl mr-2"></i>
                        <p class="text-sm font-medium">Total Present</p>
                    </div>
                    <p class="text-3xl font-bold">{{ $totalPresent }}</p>
                </div>
                <div class="bg-red-500 p-4 rounded-lg shadow text-white">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-user-times text-2xl mr-2"></i>
                        <p class="text-sm font-medium">Total Absent</p>
                    </div>
                    <p class="text-3xl font-bold">{{ $totalAbsent }}</p>
                </div>
                <div class="bg-yellow-300 p-4 rounded-lg shadow text-white">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-user-clock text-2xl mr-2"></i>
                        <p class="text-sm font-medium">Total Late</p>
                    </div>
                    <p class="text-3xl font-bold">{{ $totalLate }}</p>
                </div>
                <div class="bg-blue-400 p-4 rounded-lg shadow text-white">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-clock text-2xl mr-2"></i>
                        <p class="text-sm font-medium">Avg. Overtime</p>
                    </div>
                    <p class="text-3xl font-bold">{{ $avgOvertime }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.0.6/dist/alpine.min.js" defer></script>
<script>
    function dashboardDtr() {
        return {
            charts: {
                attendance: null,
                overtime: null,
                late: null
            },
            init() {
                this.renderCharts();
            },
            getChartOptions(title) {
                return {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                color: '#6B7280',
                                font: {
                                    weight: 'bold'
                                }
                            }
                        },
                        title: {
                            display: true,
                            text: title,
                            color: '#374151',
                            font: {
                                size: 16,
                                weight: 'bold'
                            }
                        }
                    },
                    scales: {
                        x: {
                            ticks: {
                                color: '#6B7280'
                            },
                            grid: {
                                color: 'rgba(107, 114, 128, 0.1)'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: '#6B7280'
                            },
                            grid: {
                                color: 'rgba(107, 114, 128, 0.1)'
                            }
                        }
                    }
                };
            },
            renderCharts() {
                this.renderAttendanceChart();
                this.renderOvertimeChart();
                this.renderLateChart();
            },
            renderAttendanceChart() {
                const ctx = document.getElementById('attendanceChart').getContext('2d');
                this.charts.attendance = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: @json($attendanceData->pluck('date')),
                        datasets: [
                            {
                                label: 'Present',
                                data: @json($attendanceData->pluck('present_count')),
                                backgroundColor: 'rgba(52, 211, 153, 0.8)',
                            },
                            {
                                label: 'Absent',
                                data: @json($attendanceData->pluck('absent_count')),
                                backgroundColor: 'rgba(248, 113, 113, 0.8)',
                            },
                            {
                                label: 'Late',
                                data: @json($attendanceData->pluck('late_count')),
                                backgroundColor: 'rgba(251, 191, 36, 0.8)',
                            },
                        ],
                    },
                    options: this.getChartOptions('Attendance Overview')
                });
            },
            renderOvertimeChart() {
                const ctx = document.getElementById('overtimeChart').getContext('2d');
                this.charts.overtime = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: @json($overtimeData->pluck('date')),
                        datasets: [{
                            label: 'Overtime',
                            data: @json($overtimeData->map(function($day) {
                                $parts = explode(':', $day['total_overtime']);
                                return floatval($parts[0]) + floatval($parts[1]) / 60;
                            })),
                            borderColor: 'rgba(59, 130, 246, 1)',
                            backgroundColor: 'rgba(59, 130, 246, 0.2)',
                            fill: true,
                            tension: 0.4
                        }],
                    },
                    options: this.getChartOptions('Overtime Trends')
                });
            },
            renderLateChart() {
                const ctx = document.getElementById('lateChart').getContext('2d');
                this.charts.late = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: @json($lateData->pluck('date')),
                        datasets: [{
                            label: 'Late',
                            data: @json($lateData->map(function($day) {
                                $parts = explode(':', $day['total_late']);
                                return floatval($parts[0]) + floatval($parts[1]) / 60;
                            })),
                            borderColor: 'rgba(234, 88, 12, 1)',
                            backgroundColor: 'rgba(234, 88, 12, 0.2)',
                            fill: true,
                            tension: 0.4
                        }],
                    },
                    options: this.getChartOptions('Late Arrivals')
                });
            }
        }
    }
</script>
