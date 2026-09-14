<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard - MINDSIA')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,1,0" />
    <style>
        .font-jakarta { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-inter { font-family: 'Inter', sans-serif; }
        /* Custom thin scrollbar for sidebar */
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #E2E8F0; border-radius: 4px; }
        .custom-scrollbar:hover::-webkit-scrollbar-thumb { background: #CBD5E1; }
    </style>
</head>
<body class="bg-[#F8FAFC] text-[#111827] font-inter antialiased flex h-[100dvh] overflow-hidden" x-data="{ sidebarOpen: false }">

    <!-- Mobile sidebar backdrop -->
    <div x-show="sidebarOpen" class="fixed inset-0 z-20 bg-gray-900 bg-opacity-50 transition-opacity md:hidden" @click="sidebarOpen = false"></div>

    <!-- Sidebar -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-30 w-64 overflow-hidden bg-white border-r border-gray-200 flex flex-col transition-transform duration-300 md:translate-x-0 md:sticky md:top-0 md:h-screen md:z-auto md:shrink-0">
        <!-- Logo -->
        <div class="h-16 flex items-center justify-center border-b border-gray-100 shrink-0">
            <a href="/">
                <img src="/img/logo/mindsia-logo.webp" alt="MINDSIA" class="h-8">
            </a>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 overflow-y-auto min-h-0 py-4 px-3 space-y-1 custom-scrollbar">
            @if (Auth::guard('web')->check())
                @include('layouts.sidebar.employee')
            @elseif (Auth::guard('member')->check())
                @include('layouts.sidebar.member')
            @elseif (Auth::guard('applicant')->check())
                @include('layouts.sidebar.applicant')
            @endif
        </nav>

        <!-- Sidebar Footer (User Info / Logout) -->
        <div class="p-4 border-t border-gray-100 shrink-0">
            @include('layouts.sidebar.footer')
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden relative w-full">
        <!-- Top Header -->
        <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-4 sm:px-6 z-10 shrink-0">
            <div class="flex items-center">
                <!-- Mobile menu button -->
                <button @click="sidebarOpen = true" class="md:hidden w-11 h-11 -ml-2 mr-2 flex items-center justify-center rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 active:bg-gray-200 focus:outline-none">
                    <span class="material-symbols-outlined">menu</span>
                </button>
                <h1 class="text-lg sm:text-xl font-bold font-jakarta text-[#1E293B]">@yield('header_title', 'Dashboard')</h1>
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

</body>
</html>
