<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $pageTitle = ($title ?? 'Portal') . ' — ' . ($siteSettings->app_name ?? config('app.name'));
    @endphp
    <title>{{ $pageTitle }}</title>
    <meta name="robots" content="noindex,nofollow">
    {{-- Favicon --}}
    @if($siteSettings->app_favicon ?? null)
    <link rel="icon" href="{{ Storage::url($siteSettings->app_favicon) }}">
    @endif

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="min-h-screen bg-gray-950 text-gray-100">

    <div class="flex h-screen overflow-hidden">
        {{-- ── Sidebar ── --}}
        <aside class="hidden md:flex md:flex-col md:w-64 bg-gray-900 border-r border-gray-800 shrink-0">
            {{-- Logo --}}
            <div class="flex h-16 items-center gap-2.5 px-4 border-b border-gray-800">
                <a href="{{ route('portal.dashboard') }}" class="flex items-center gap-2.5 font-bold text-white">
                    <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-brand-500 text-white text-xs font-black">H</span>
                    {{ $siteSettings->app_name ?? config('app.name') }}
                </a>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 overflow-y-auto p-4 space-y-1">
                <p class="px-2 py-1 text-xs font-semibold uppercase tracking-widest text-gray-500 mb-2">Portal</p>

                <a href="{{ route('portal.dashboard') }}"
                   class="nav-item {{ request()->routeIs('portal.dashboard') ? 'active' : '' }}">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Dashboard
                </a>

                <a href="{{ route('portal.containers.index') }}"
                   class="nav-item {{ request()->routeIs('portal.containers.*') ? 'active' : '' }}">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2"/></svg>
                    Container
                </a>

                <a href="{{ route('portal.orders.index') }}"
                   class="nav-item {{ request()->routeIs('portal.orders.*') ? 'active' : '' }}">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Pesanan
                </a>

                <a href="{{ route('portal.billing.index') }}"
                   class="nav-item {{ request()->routeIs('portal.billing.*') ? 'active' : '' }}">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    Billing
                </a>

                <div class="pt-4 border-t border-gray-800 mt-4">
                    <a href="{{ route('portal.profile.edit') }}"
                       class="nav-item {{ request()->routeIs('portal.profile.*') ? 'active' : '' }}">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        Profil
                    </a>
                </div>
            </nav>

            {{-- User footer --}}
            <div class="p-4 border-t border-gray-800">
                <div class="flex items-center gap-3">
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-brand-500/20 text-brand-400 text-sm font-semibold">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-200 truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="mt-3">
                    @csrf
                    <button type="submit" class="btn-ghost btn-sm w-full justify-start text-red-400 hover:text-red-300 hover:bg-red-950/30">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        Keluar
                    </button>
                </form>
            </div>
        </aside>

        {{-- ── Main content ── --}}
        <div class="flex flex-col flex-1 overflow-hidden">
            {{-- Top bar --}}
            <header class="flex h-16 items-center justify-between px-6 border-b border-gray-800 bg-gray-900 shrink-0">
                <h1 class="text-lg font-semibold text-gray-100">{{ $title ?? 'Portal' }}</h1>
                <div class="flex items-center gap-3">
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="badge-blue text-xs">Admin Panel</a>
                    @endif
                    <a href="{{ route('portal.orders.create') }}" class="btn-primary btn-sm">+ Order Baru</a>
                </div>
            </header>

            {{-- Flash --}}
            @if(session('success'))
                <div role="alert" class="alert-success mx-6 mt-4">
                    <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>{{ session('success') }}</span>
                    <button data-flash-dismiss class="ml-auto">&times;</button>
                </div>
            @endif
            @if(session('error'))
                <div role="alert" class="alert-error mx-6 mt-4">
                    <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"/></svg>
                    <span>{{ session('error') }}</span>
                    <button data-flash-dismiss class="ml-auto">&times;</button>
                </div>
            @endif

            {{-- Page content --}}
            <main class="flex-1 overflow-y-auto p-6">
                {{ $slot }}
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
