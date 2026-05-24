<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data :class="{ 'dark': $store.darkMode }"
    x-init="$store.darkMode = localStorage.getItem('dark-mode') === 'true'">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#2563eb">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="JJWC HRIS">
    <link rel="icon" href="/images/logo.png" type="image/x-icon">
    <link rel="apple-touch-icon" href="/images/logo.png">
    <link rel="manifest" href="/manifest.webmanifest">

    <title>{{ isset($title) ? 'JJWC HRIS - ' . $title : 'JJWC HRIS' }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400..700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    @stack('styles')

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/@marcreichel/alpine-auto-animate@latest/dist/alpine-auto-animate.min.js"
        defer></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/focus@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/alpine.min.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <style>
        [x-cloak] {
            display: none !important;
        }

        html {
            scroll-behavior: smooth;
        }

        @media (min-width: 1024px){
            ::-webkit-scrollbar {
                width: 8px;
            }
            ::-webkit-scrollbar-track {
                background: #f1f5f9;
            }
            ::-webkit-scrollbar-thumb {
                background: linear-gradient(to bottom, #3b82f6, #8b5cf6);
            }
            ::-webkit-scrollbar-thumb:hover {
                background: linear-gradient(to bottom, #2563eb, #7c3aed);
            }

            /* Custom scrollbar */
            ::-webkit-scrollbar {
                width: 10px;
            }
            ::-webkit-scrollbar-track {
                background: #f1f5f9;
            }
            ::-webkit-scrollbar-thumb {
                background: linear-gradient(to bottom, #3b82f6, #8b5cf6);
            }
            ::-webkit-scrollbar-thumb:hover {
                background: linear-gradient(to bottom, #2563eb, #7c3aed);
            }
        }

        .scrollbar-thin1::-webkit-scrollbar {
            width: 5px;
        }

        .scrollbar-thin1::-webkit-scrollbar-thumb {
            background-color: #1a1a1a4b;
        }

        .scrollbar-thin1::-webkit-scrollbar-track {
            background-color: #ffffff23;
        }

        @keyframes spinner-border {
            to {
                transform: rotate(360deg);
            }
        }

        .spinner-border {
            display: inline-block;
            width: 1rem;
            height: 1rem;
            vertical-align: text-bottom;
            border: 2px solid currentColor;
            border-right-color: transparent;
            border-radius: 50%;
            -webkit-animation: spinner-border .75s linear infinite;
            animation: spinner-border .75s linear infinite;
            color: rgb(255, 255, 255);
        }

        @media (max-width: 1024px){
            .custom-d{
                display: block;
            }
        }

        @media (max-width: 768px){
            .m-scrollable{
                width: 100%;
                overflow-x: scroll;
            }
        }

        @media (min-width:1024px){
            .custom-p{
                padding-bottom: 14px !important;
            }
        }
    </style>
    @livewireStyles
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('darkMode', localStorage.getItem('dark-mode') === 'true');

            Alpine.effect(() => {
                document.documentElement.classList.toggle('dark', Alpine.store('darkMode'));
                localStorage.setItem('dark-mode', Alpine.store('darkMode'));
            })
        });
    </script>
</head>

<body class="font-inter antialiased bg-slate-100 dark:bg-slate-900 text-slate-600 dark:text-slate-400"
    :class="{ 'sidebar-expanded': sidebarExpanded }" x-data="{ sidebarOpen: false, sidebarExpanded: localStorage.getItem('sidebar-expanded') === 'true' }" x-init="$watch('sidebarExpanded', value => localStorage.setItem('sidebar-expanded', value))">

    <!-- Page wrapper -->
    <div class="flex h-[100dvh] overflow-hidden">

        <x-app.sidebar />

        <x-toast />

        <!-- Content area -->
        <div class="relative flex flex-col flex-1 overflow-y-auto overflow-x-hidden @if ($attributes['background']) {{ $attributes['background'] }} @endif"
            x-ref="contentarea">

            <x-app.header />

            <main class="grow">
                <div class="px-4 sm:px-6 lg:px-8 py-8 w-full mx-auto flex justify-center items-center mb-8">
                    {{ $slot }}
                </div>
            </main>

        </div>

    </div>

    <script>
        document.addEventListener('livewire:navigating', () => {
            Alpine.store('darkMode', localStorage.getItem('dark-mode') === 'true');
        });

        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                const isLocalhost = ['localhost', '127.0.0.1', '::1'].includes(window.location.hostname);

                if (isLocalhost) {
                    navigator.serviceWorker.getRegistrations()
                        .then((registrations) => registrations.forEach((registration) => registration.unregister()))
                        .catch((error) => console.warn('Service worker unregister failed:', error));
                    return;
                }

                navigator.serviceWorker.register('/service-worker.js')
                    .catch((error) => console.warn('Service worker registration failed:', error));
            });
        }
    </script>

    <script>
        function initLocationHandling() {
            const currentPath = window.location.pathname;
            if (window.ReactNativeWebView) {
                window.ReactNativeWebView.postMessage(JSON.stringify({
                    type: 'routeInfo',
                    route: currentPath
                }));
            } else {
                if(currentPath == '/home' || currentPath == '/daily-time-record/official-business'){
                    getLocationForBrowser();
                }
            }
        }

        function getLocationForBrowser() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        sendLocationToApp(position.coords.latitude, position.coords.longitude);
                    },
                    (error) => {
                        console.error('Error getting location:', error);
                        alert('Unable to retrieve location. Please enable location services in your browser settings and try again.');
                    },
                    { enableHighAccuracy: true }
                );
            } else {
                console.error('Geolocation is not supported by this browser.');
                alert('Geolocation is not supported by this browser.');
            }
        }

        function sendLocationToApp(latitude, longitude) {
            const locationData = {
                latitude: latitude,
                longitude: longitude,
                formattedTime: new Date().toLocaleTimeString(),
            };

            Livewire.dispatch('locationUpdated', { locationData });
        }

        document.addEventListener('DOMContentLoaded', () => {
            initLocationHandling();
        });
        document.addEventListener('livewire:navigated', initLocationHandling);
        window.addEventListener('popstate', initLocationHandling);
    </script>

    @stack('scripts')
    @livewireScripts
</body>

</html>
