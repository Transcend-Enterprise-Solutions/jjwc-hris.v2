<div class="flex flex-col sm:flex-row gap-4 mt-4">

    <div
        class="p-6 flex-1 bg-gradient-to-br from-indigo-100 to-white dark:from-gray-800 dark:to-gray-900 rounded-xl shadow-md border border-slate-200 dark:border-slate-700">
        <div>
            <h2 class="text-sm text-gray-900 dark:text-gray-100">
                Vacation Leave <br><span class="text-xs opacity-70">Credits:</span>
            </h2>
            <p class="text-xl font-bold text-blue-600 dark:text-gray-200" style="margin-top: -5px">
                {{ number_format($vlClaimableCredits, 3) }}
            </p>
        </div>
    </div>

    <div
        class="p-6 flex-1 bg-gradient-to-br from-indigo-100 to-white dark:from-gray-800 dark:to-gray-900 rounded-xl shadow-md border border-slate-200 dark:border-slate-700">
        <div>
            <h2 class="text-sm text-gray-900 dark:text-gray-100">
                Forced Leave <br><span class="text-xs opacity-70">Credits:</span>
            </h2>
            <p class="text-xl font-bold text-blue-600 dark:text-gray-200" style="margin-top: -5px">
                {{ number_format($flClaimableCredits, 3) }}
            </p>
        </div>
    </div>

    <div
        class="p-6 flex-1 bg-gradient-to-br from-indigo-100 to-white dark:from-gray-800 dark:to-gray-900 rounded-xl shadow-md border border-slate-200 dark:border-slate-700">
        <div>
            <h2 class="text-sm text-gray-900 dark:text-gray-100">
                Sick Leave <br><span class="text-xs opacity-70">Credits:</span>
            </h2>
            <p class="text-xl font-bold text-blue-600 dark:text-gray-200" style="margin-top: -5px">
                {{ number_format($slClaimableCredits, 3) }}
            </p>
        </div>
    </div>

    <div
        class="p-6 flex-1 bg-gradient-to-br from-indigo-100 to-white dark:from-gray-800 dark:to-gray-900 rounded-xl shadow-md border border-slate-200 dark:border-slate-700">
        <div>
            <h2 class="text-sm text-gray-900 dark:text-gray-100">
                Special Privilege Leave <br><span class="text-xs opacity-70">Credits:</span>
            </h2>
            <p class="text-xl font-bold text-blue-600 dark:text-gray-200" style="margin-top: -5px">
                {{ number_format($splClaimableCredits, 3) }}
            </p>
        </div>
    </div>

    {{-- <div
        class="p-6 flex-1 bg-gradient-to-br from-indigo-100 to-white dark:from-gray-800 dark:to-gray-900 rounded-xl shadow-md border border-slate-200 dark:border-slate-700">
        <div>
            <h2 class="text-xl sm:text-2xl mb-6 text-gray-900 dark:text-gray-100">
                CTO Credits
            </h2>
            <p class="text-xl font-semi-bold text-blue-600 dark:text-gray-200">
                {{ $ctoCredits }}
            </p>
        </div>
    </div> --}}

</div>
