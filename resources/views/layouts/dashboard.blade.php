<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard - MINDSIA')</title>
    <script>
        (function() {
            var theme = localStorage.getItem('dash_theme') || 'blue';
            var mode = localStorage.getItem('dash_mode') || 'light';
            document.documentElement.classList.add('theme-' + theme, 'mode-' + mode);
        })();
    </script>
    @stack('head-scripts')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,1,0" />
    <style>
        .font-jakarta { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-inter { font-family: 'Inter', sans-serif; }
        /* Custom thin scrollbar for sidebar */
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: var(--dash-scrollbar-thumb); border-radius: 4px; }
        .custom-scrollbar:hover::-webkit-scrollbar-thumb { background: var(--dash-scrollbar-hover); }
    </style>
</head>
<body class="font-inter antialiased flex h-[100dvh] overflow-hidden" style="background-color: var(--dash-page-bg); color: var(--dash-text-body);" x-data="{ sidebarOpen: false }">

    <!-- Mobile sidebar backdrop -->
    <div x-show="sidebarOpen" class="fixed inset-0 z-20 bg-gray-900 bg-opacity-50 transition-opacity md:hidden" @click="sidebarOpen = false"></div>

    <!-- Sidebar -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-30 w-64 overflow-hidden flex flex-col transition-transform duration-300 md:translate-x-0 md:sticky md:top-0 md:h-screen md:z-auto md:shrink-0" style="background-color: var(--dash-sidebar-bg); border-right: 1px solid var(--dash-sidebar-border);">
        <!-- Logo -->
        <div class="h-16 flex items-center justify-center shrink-0" style="border-bottom: 1px solid var(--dash-sidebar-border-subtle);">
            <a href="/">
                <img src="/img/logo/mindsia-logo.webp" alt="MINDSIA" class="h-14 w-auto">
            </a>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 overflow-y-auto min-h-0 py-4 px-3 space-y-1 custom-scrollbar">
            @if (request()->is('applicant*') && Auth::guard('applicant')->check())
                @include('layouts.sidebar.applicant')
            @elseif (request()->is('member*') && Auth::guard('member')->check())
                @include('layouts.sidebar.member')
            @elseif (Auth::guard('web')->check())
                @include('layouts.sidebar.employee')
            @elseif (Auth::guard('member')->check())
                @include('layouts.sidebar.member')
            @elseif (Auth::guard('applicant')->check())
                @include('layouts.sidebar.applicant')
            @endif
        </nav>

        <!-- Sidebar Footer (User Info / Logout) -->
        <div class="p-4 shrink-0" style="border-top: 1px solid var(--dash-sidebar-border-subtle);">
            @include('layouts.sidebar.footer')
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col relative min-w-0">
        <!-- Top Header -->
        <header class="h-16 flex items-center justify-between px-4 sm:px-6 z-10 shrink-0" style="background-color: var(--dash-topbar-bg); border-bottom: 1px solid var(--dash-topbar-border);">
            <div class="flex items-center">
                <!-- Mobile menu button -->
                <button @click="sidebarOpen = true" class="md:hidden w-11 h-11 -ml-2 mr-2 flex items-center justify-center rounded-lg transition-colors focus:outline-none" style="color: var(--dash-text-muted);" onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'" onmouseout="this.style.backgroundColor='transparent'">
                    <span class="material-symbols-outlined">menu</span>
                </button>
                <h1 class="text-lg sm:text-xl font-bold font-jakarta" style="color: var(--dash-text-heading);">@yield('header_title', 'Dashboard')</h1>
            </div>
            
            <div class="flex items-center gap-3 sm:gap-4">
                @yield('header_actions')
            </div>
        </header>

        <!-- Main scrollable area -->
        <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
            @yield('content')
        </main>
    </div>
    @stack('scripts')
</body>
</html>
