<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $pageTitle = ($title ?? 'Admin') . ' — ' . ($siteSettings->app_name ?? config('app.name')) . ' Admin';
    @endphp
    <title>{{ $pageTitle }}</title>
    <meta name="robots" content="noindex,nofollow">
    {{-- Favicon --}}
    @if($siteSettings->app_favicon ?? null)
    <link rel="icon" href="{{ Storage::url($siteSettings->app_favicon) }}">
    @endif

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/components/confirm.js'])
    @stack('head')
</head>
<body class="min-h-screen bg-gray-950 text-gray-100">

    <div class="flex h-screen overflow-hidden">
        {{-- ── Admin Sidebar ── --}}
        <aside class="hidden md:flex md:flex-col md:w-60 bg-gray-900 border-r border-gray-800 shrink-0">
            {{-- Logo --}}
            <div class="flex h-16 items-center gap-2 px-4 border-b border-gray-800">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-brand-500 text-white text-xs font-black">H</span>
                <span class="font-bold text-white text-sm">Admin Panel</span>
            </div>

            <nav class="flex-1 overflow-y-auto p-3 space-y-1">
                <p class="px-2 py-1 text-xs font-semibold uppercase tracking-widest text-gray-500 mb-1">Manajemen</p>

                @php
                    $adminLinks = [
                        ['route' => 'admin.dashboard',        'label' => 'Dashboard',  'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6', 'match' => 'admin.dashboard'],
                        ['route' => 'admin.packages.index',   'label' => 'Paket',      'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4', 'match' => 'admin.packages.*'],
                        ['route' => 'admin.orders.index',     'label' => 'Pesanan',    'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'match' => 'admin.orders.*'],
                        ['route' => 'admin.users.index',      'label' => 'Pengguna',   'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'match' => 'admin.users.*'],
                        ['route' => 'admin.containers.index', 'label' => 'Container',  'icon' => 'M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2', 'match' => 'admin.containers.*'],
                        ['route' => 'admin.payments.index',   'label' => 'Pembayaran', 'icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z', 'match' => 'admin.payments.*'],
                        ['route' => 'admin.settings.index',   'label' => 'Pengaturan', 'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z', 'match' => 'admin.settings.*'],
                    ];
                @endphp

                @foreach($adminLinks as $link)
                <a href="{{ route($link['route']) }}"
                   class="nav-item {{ request()->routeIs($link['match']) ? 'active' : '' }}">
                    <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $link['icon'] }}"/>
                    </svg>
                    {{ $link['label'] }}
                </a>
                @endforeach

                <div class="pt-4 border-t border-gray-800 mt-4">
                    <a href="{{ route('portal.dashboard') }}" class="nav-item text-gray-500">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        Portal
                    </a>
                </div>
            </nav>

            <div class="p-3 border-t border-gray-800">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn-ghost btn-sm w-full justify-start text-red-400 hover:text-red-300 hover:bg-red-950/30">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        Keluar
                    </button>
                </form>
            </div>
        </aside>

        {{-- ── Main area ── --}}
        <div class="flex flex-col flex-1 overflow-hidden">
            <header class="flex h-16 items-center justify-between px-6 border-b border-gray-800 bg-gray-900 shrink-0">
                <h1 class="text-base font-semibold text-gray-100">{{ $title ?? 'Admin' }}</h1>
                <div class="flex items-center gap-2 text-sm text-gray-400">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    {{ auth()->user()->name }}
                </div>
            </header>

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

            <main class="flex-1 overflow-y-auto p-6">
                {{ $slot }}
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
