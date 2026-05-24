<div x-data="initEmployeeChart()" x-init="init()" class="p-6 bg-gradient-to-br h-full from-indigo-100 to-white dark:from-gray-800 dark:to-gray-900 rounded-xl shadow-md">
    <div>
        <h2 class="text-xl sm:text-2xl font-semibold mb-6 text-gray-900 dark:text-gray-100">
            Total Employees
        </h2>
        <p class="text-3xl font-bold text-blue-600 dark:text-gray-200">{{ $totalEmployees }}</p>
    </div>

    <!-- Chart.js graph -->
    <div class="w-full flex justify-center items-center">
        <div class="relative w-full h-64">
            <canvas id="employeeChart" class="w-full h-full"></canvas>
        </div>
    </div>
</div>

<!-- Load Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.0.6/dist/alpine.min.js" defer></script>
<script>
    function initEmployeeChart() {
        return {
            init() {
                const ctx = document.getElementById('employeeChart').getContext('2d');

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: @json($months),
                        datasets: [{
                            label: 'Users Created per Month',
                            data: @json($monthlyCreations),
                            backgroundColor: 'rgba(54, 162, 235, 0.5)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function (context) {
                                        return `Users created: ${context.raw}`;
                                    }
                                }
                            }
                        }
                    }
                });
            }
        }
    }
</script>