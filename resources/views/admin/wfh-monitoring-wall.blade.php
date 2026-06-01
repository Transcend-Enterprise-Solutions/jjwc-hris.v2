<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#07111f">
    <link rel="icon" href="/images/logo.png" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title>JJWC HRIS - WFH Monitor Wall</title>
    <script>
        if (localStorage.getItem('dark-mode') === 'true') {
            document.documentElement.classList.add('dark');
            document.documentElement.style.colorScheme = 'dark';
        } else {
            document.documentElement.style.colorScheme = 'light';
        }
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-inter bg-slate-100 text-slate-800 dark:bg-slate-950 dark:text-slate-200">
    <button
        id="wfh-wall-theme-toggle"
        type="button"
        class="fixed right-4 top-4 z-50 inline-flex h-10 items-center gap-2 rounded-lg border border-slate-200 bg-white/90 px-3 text-sm font-bold text-slate-700 shadow-lg backdrop-blur transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900/90 dark:text-slate-100 dark:hover:bg-slate-800"
        aria-label="Toggle dark mode"
    >
        <i class="bi bi-moon-stars dark:hidden"></i>
        <i class="bi bi-brightness-high hidden dark:inline"></i>
        <span class="hidden sm:inline">Theme</span>
    </button>
    <main class="min-h-screen p-3 sm:p-5">
        <div
            id="wfh-monitoring-wall"
            data-api-base="{{ url('/wfh-monitoring/api') }}"
            data-initial-date="{{ now()->toDateString() }}"
            data-wall-url="{{ route('wfh-monitoring.wall') }}"
            data-ice-servers='@json(config('wfh_monitoring.ice_servers'))'
        ></div>
    </main>
    <script>
        (() => {
            const button = document.getElementById('wfh-wall-theme-toggle');
            if (!button) return;

            button.addEventListener('click', () => {
                const nextDarkMode = !document.documentElement.classList.contains('dark');
                document.documentElement.classList.toggle('dark', nextDarkMode);
                document.documentElement.style.colorScheme = nextDarkMode ? 'dark' : 'light';
                localStorage.setItem('dark-mode', nextDarkMode ? 'true' : 'false');
                document.dispatchEvent(new CustomEvent('darkMode', { detail: { mode: nextDarkMode ? 'on' : 'off' } }));
            });
        })();
    </script>
</body>
</html>
