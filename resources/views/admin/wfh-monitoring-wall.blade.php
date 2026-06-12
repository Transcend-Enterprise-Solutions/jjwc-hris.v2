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
        }
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-inter bg-slate-100 text-slate-800 dark:bg-slate-950 dark:text-slate-200">
    <main class="min-h-screen p-3 sm:p-5">
        <div
            id="wfh-monitoring-wall"
            data-api-base="{{ url('/wfh-monitoring/api') }}"
            data-initial-date="{{ now()->toDateString() }}"
            data-wall-url="{{ route('wfh-monitoring.wall') }}"
            data-ice-servers='@json(config('wfh_monitoring.ice_servers'))'
        ></div>
    </main>
</body>
</html>
