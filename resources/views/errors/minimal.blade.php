<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="/images/logo.png" type="image/x-icon">

    <title>JJWC HRIS</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400..700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />

    <!-- Scripts -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('build/assets/app-JvcQ0qKH.css') }}">

    @livewireStyles
    <style>
        html, body {
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        .right-side-login {
            height: 100%;
            width: 100%;
            overflow: visible;
            position: absolute;
            top: 0;
            right: 0;
        }

        .right-side-login img {
            position: absolute;
            right: 0;
            bottom: 0;
            z-index: 1;
        }

        .login-logo {
            position: relative;
            z-index: 1;
        }

        .main-container {
            overflow-x: hidden;
        }

        .right-side-content {
            transform: translateX(100%);
        }
    </style>
</head>

<body class="font-inter antialiased bg-slate-100 dark:bg-slate-900 text-slate-600 dark:text-slate-400">

    <main class="bg-white main-container">
        <div class="relative flex overflow-hidden justify-center lg:justify-between" style="z-index: 99">

            <!-- Content -->
            <div class="w-full">
                <div class="min-h-[100dvh] h-full flex flex-col after:flex-1">

                    <!-- Header -->
                    <div class="flex-1">
                        <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
                            <!-- Logo -->
                            <a class="block" href="{{ route('/dashboard') }}">
                                <img src="/images/logo.png" alt="logo" class="h-12">
                            </a>
                        </div>
                    </div>

                    <div class="flex flex-col justify-center items-center w-full">
                        <span class="font-bold text-gray-500" style="font-size: 72px">
                            @yield('code')
                            @yield('message')
                        </span>
                        <br>
                        <span class="text-gray-400 text-lg">
                            @yield('sub-message')
                        </span>

                        <a href="/" class="mt-4 bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition duration-200">
                            Go Back
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="right-side-login">
            <img src="/images/Vector.png" alt="login bg" >
        </div>
    </main>
    @livewireScripts
</body>

</html>