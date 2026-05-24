<div x-data="initChart()" x-init="init()" class="relative p-6 bg-gradient-to-br h-full from-indigo-100 to-white dark:from-gray-800 dark:to-gray-900 rounded-xl shadow-md">
    <div>
        <h2 class="text-xl sm:text-2xl font-semibold mb-6 text-gray-900 dark:text-gray-100">
            Employee's Status
        </h2>
    </div>

    <!-- Text representation of the status -->
    <div class="mb-6">
        <ul class="pl-5 text-gray-700 dark:text-gray-300">
            <li>
                <span class="inline-block w-3 h-3 mr-2 rounded-full" style="background-color: rgba(75, 192, 192, 1);"></span>
                Active: <span id="activeCount">{{ $statusCounts[1] ?? 0 }}</span>
            </li>
            <li>
                <span class="inline-block w-3 h-3 mr-2 rounded-full" style="background-color: rgba(255, 99, 132, 1);"></span>
                Inactive: <span id="inactiveCount">{{ $statusCounts[0] ?? 0 }}</span>
            </li>
            <li>
                <span class="inline-block w-3 h-3 mr-2 rounded-full" style="background-color: rgba(54, 162, 235, 1);"></span>
                Resigned: <span id="resignedCount">{{ $statusCounts[2] ?? 0 }}</span>
            </li>
            <li>
                <span class="inline-block w-3 h-3 mr-2 rounded-full" style="background-color: rgba(153, 102, 255, 1);"></span>
                Retired: <span id="retiredCount">{{ $statusCounts[3] ?? 0 }}</span>
            </li>
        </ul>
    </div>

    <!-- Chart.js graph -->
    <div class="relative sm:absolute bottom-1 -ml-3">
        <canvas id="statusChart" class="w-full h-80 sm:h-auto"></canvas>
    </div>
</div>

<!-- Load Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.0.6/dist/alpine.min.js" defer></script>
<script>
    function initChart() {
        const ctx = document.getElementById('statusChart').getContext('2d');

        // Retrieve data from the Blade view
        const statusCounts = @json($statusCounts);
        const labels = ['Inactive', 'Active', 'Resigned', 'Retired'];
        const data = labels.map((label, index) => statusCounts[index] || 0);

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Number of Employee by Status',
                    data: data,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)', // Inactive (Red)
                        'rgba(75, 192, 192, 0.2)', // Active (Green)
                        'rgba(54, 162, 235, 0.2)', // Resigned (Blue)
                        'rgba(153, 102, 255, 0.2)' // Retired (Violet)
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)', // Inactive (Red)
                        'rgba(75, 192, 192, 1)', // Active (Green)
                        'rgba(54, 162, 235, 1)', // Resigned (Blue)
                        'rgba(153, 102, 255, 1)' // Retired (Violet)
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.raw !== null) {
                                    label += context.raw;
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
</script>
