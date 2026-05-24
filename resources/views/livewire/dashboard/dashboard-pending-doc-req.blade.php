<div class="p-6 h-full bg-gradient-to-br from-indigo-100 to-white dark:from-gray-800 dark:to-gray-900 rounded-xl shadow-md">
    <h2 class="text-xl sm:text-2xl font-semibold mb-6 text-gray-900 dark:text-gray-100">
        Document Requests Analytics
    </h2>

    <div class="grid grid-cols-2 gap-6">
        <!-- Total Requests -->
        <div class="flex items-center justify-between bg-gray-50 dark:bg-gray-100 p-4 rounded-lg shadow-sm">
            <div class="flex flex-col">
                <div class="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-400 mr-2">
                    Total Requests
                </div>
                <div class="text-2xl sm:text-3xl font-bold text-gray-900">
                    {{ $totalRequests }}
                </div>
            </div>
            <div class="flex-shrink-0 text-gray-400 dark:text-gray-500">
                <i class="fas fa-file-alt fa-2x"></i>
            </div>
        </div>

        <!-- Pending Requests -->
        <div class="flex items-center justify-between bg-yellow-100 dark:bg-yellow-900 p-4 rounded-lg shadow-sm">
            <div class="flex flex-col">
                <div class="text-xs sm:text-sm font-medium text-yellow-800 dark:text-gray-300">
                    Pending Requests
                </div>
                <div class="text-2xl sm:text-3xl font-bold text-yellow-600 dark:text-yellow-400">
                    {{ $pendingRequests }}
                </div>
            </div>
            <div class="flex-shrink-0 text-yellow-400 dark:text-yellow-500">
                <i class="fas fa-clock fa-2x"></i>
            </div>
        </div>

        <!-- Completed Requests -->
        <div class="flex items-center justify-between bg-green-100 dark:bg-green-900 p-4 rounded-lg shadow-sm">
            <div class="flex flex-col">
                <div class="text-xs sm:text-sm font-medium text-green-800 dark:text-green-300">
                    Completed Requests
                </div>
                <div class="text-2xl sm:text-3xl font-bold text-green-600 dark:text-green-400">
                    {{ $completedRequests }}
                </div>
            </div>
            <div class="flex-shrink-0 text-green-400 dark:text-green-500">
                <i class="fas fa-check-circle fa-2x"></i>
            </div>
        </div>

        <!-- Average Completion Time -->
        <div class="flex items-center justify-between bg-blue-100 dark:bg-blue-900 p-4 rounded-lg shadow-sm">
            <div class="flex flex-col">
                <div class="text-xs sm:text-xs font-medium text-blue-800 dark:text-blue-300">
                    Avg. Completion Time
                </div>
                <div class="text-m sm:text-lg font-bold text-blue-600 dark:text-blue-400">
                    @if ($averageCompletionTimeDays > 0)
                        {{ $averageCompletionTimeDays }}D
                    @endif
                    {{ str_pad($averageCompletionTimeHours, 2, '0', STR_PAD_LEFT) }}H
                    {{ str_pad($averageCompletionTimeMinutes, 2, '0', STR_PAD_LEFT) }}M
                </div>
            </div>
            <div class="flex-shrink-0 text-blue-400 dark:text-blue-500">
                <i class="fas fa-hourglass-half fa-2x"></i>
            </div>
        </div>
    </div>
</div>
