<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- SEO --}}
    @php
        $pageTitle =
            ($title ?? ($siteSettings->app_name ?? config('app.name'))) . ' - ' . ($siteSettings->app_tagline ?? '');
        $pageDesc = $description ?? ($siteSettings->meta_description ?? '');
        $ogImage = isset($siteSettings->app_logo) ? Storage::url($siteSettings->app_logo) : '';
        $canonical = request()->url();
    @endphp
    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $pageDesc }}">
    @if (!empty($siteSettings->meta_keywords))
        <meta name="keywords" content="{{ $siteSettings->meta_keywords }}">
    @endif
    <link rel="canonical" href="{{ $canonical }}">

    {{-- Favicon --}}
    @if ($siteSettings->app_favicon ?? null)
        <link rel="icon" href="{{ Storage::url($siteSettings->app_favicon) }}">
    @endif

    {{-- Open Graph --}}
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ $canonical }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDesc }}">
    @if ($ogImage)
        <meta property="og:image" content="{{ $ogImage }}">
    @endif
    <meta property="og:site_name" content="{{ $siteSettings->app_name ?? config('app.name') }}">
    <meta property="og:locale" content="{{ str_replace('-', '_', app()->getLocale()) }}">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $pageDesc }}">
    @if ($ogImage)
        <meta name="twitter:image" content="{{ $ogImage }}">
    @endif
    @if ($siteSettings->social_twitter ?? null)
        <meta name="twitter:site" content="{{ $siteSettings->social_twitter }}">
    @endif

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>

<body class="min-h-screen flex flex-col bg-gray-950 text-gray-100">

    {{-- ── Navigation ── --}}
    <header class="sticky top-0 z-50 border-b border-gray-800/80 bg-gray-950/90 backdrop-blur-sm">
        <nav class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between">
                {{-- Logo --}}
                <a href="{{ route('home') }}" class="flex items-center gap-2.5 font-bold text-lg text-white">
                    <span
                        class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-500 text-white text-sm font-black">A</span>
                    {{ $siteSettings->app_name ?? config('app.name') }}
                </a>

                {{-- Desktop links --}}
                <div class="hidden md:flex items-center gap-1">
                    <a href="{{ route('home') }}"
                        class="px-3 py-2 text-sm text-gray-400 hover:text-white rounded-lg hover:bg-gray-800 transition-colors {{ request()->routeIs('home') ? 'text-white' : '' }}">Beranda</a>
                    <a href="{{ route('about') }}"
                        class="px-3 py-2 text-sm text-gray-400 hover:text-white rounded-lg hover:bg-gray-800 transition-colors">Tentang</a>
                    <a href="{{ route('products') }}"
                        class="px-3 py-2 text-sm text-gray-400 hover:text-white rounded-lg hover:bg-gray-800 transition-colors">Produk</a>
                    <a href="{{ route('pricing') }}"
                        class="px-3 py-2 text-sm text-gray-400 hover:text-white rounded-lg hover:bg-gray-800 transition-colors">Harga</a>
                    <a href="{{ route('contact') }}"
                        class="px-3 py-2 text-sm text-gray-400 hover:text-white rounded-lg hover:bg-gray-800 transition-colors">Kontak</a>
                </div>

                {{-- Auth buttons --}}
                <div class="flex items-center gap-3">
                    @auth
                        <a href="{{ route('portal.dashboard') }}" class="btn-primary btn-sm">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}"
                            class="text-sm text-gray-400 hover:text-white transition-colors">Masuk</a>
                        <a href="{{ route('register') }}" class="btn-primary btn-sm">Daftar Gratis</a>
                    @endauth

                    {{-- Mobile toggle --}}
                    <button id="mobile-menu-toggle" aria-expanded="false"
                        class="md:hidden p-2 text-gray-400 hover:text-white rounded-lg hover:bg-gray-800 transition-colors">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Mobile menu --}}
            <div id="mobile-menu" class="hidden md:hidden pb-4 border-t border-gray-800 mt-2 pt-4 space-y-1">
                <a href="{{ route('home') }}"
                    class="block px-3 py-2 text-sm text-gray-400 hover:text-white rounded-lg hover:bg-gray-800">Beranda</a>
                <a href="{{ route('about') }}"
                    class="block px-3 py-2 text-sm text-gray-400 hover:text-white rounded-lg hover:bg-gray-800">Tentang</a>
                <a href="{{ route('products') }}"
                    class="block px-3 py-2 text-sm text-gray-400 hover:text-white rounded-lg hover:bg-gray-800">Produk</a>
                <a href="{{ route('pricing') }}"
                    class="block px-3 py-2 text-sm text-gray-400 hover:text-white rounded-lg hover:bg-gray-800">Harga</a>
                <a href="{{ route('contact') }}"
                    class="block px-3 py-2 text-sm text-gray-400 hover:text-white rounded-lg hover:bg-gray-800">Kontak</a>
            </div>
        </nav>
    </header>

    {{-- Flash messages --}}
    @if (session('success'))
        <div role="alert" class="alert-success mx-4 mt-4 rounded-lg">
            <svg class="h-4 w-4 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span>{{ session('success') }}</span>
            <button data-flash-dismiss class="ml-auto text-green-400 hover:text-green-200">&times;</button>
        </div>
    @endif
    @if (session('error'))
        <div role="alert" class="alert-error mx-4 mt-4 rounded-lg">
            <svg class="h-4 w-4 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            <span>{{ session('error') }}</span>
            <button data-flash-dismiss class="ml-auto text-red-400 hover:text-red-200">&times;</button>
        </div>
    @endif

    {{-- Content --}}
    <main class="flex-1">
        {{ $slot }}
    </main>

    {{-- Footer --}}
    <footer class="border-t border-gray-800 bg-gray-900 py-12 mt-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-8 md:grid-cols-4">
                <div class="md:col-span-2">
                    <div class="flex items-center gap-2.5 font-bold text-lg text-white mb-3">
                        <span
                            class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-500 text-white text-sm font-black">H</span>
                        {{ $siteSettings->app_name ?? config('app.name') }}
                    </div>
                    <p class="text-sm text-gray-400 max-w-xs">{{ $siteSettings->app_description ?? '' }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-200 mb-3">Layanan</h3>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="{{ route('products') }}" class="hover:text-white transition-colors">Produk</a>
                        </li>
                        <li><a href="{{ route('pricing') }}" class="hover:text-white transition-colors">Harga</a>
                        </li>
                        <li><a href="{{ route('about') }}" class="hover:text-white transition-colors">Tentang
                                Kami</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-200 mb-3">Kontak</h3>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li>{{ $siteSettings->contact_email ?? '' }}</li>
                        @if ($siteSettings->contact_phone ?? null)
                            <li>{{ $siteSettings->contact_phone }}</li>
                        @endif
                    </ul>
                </div>
            </div>
            <div class="mt-8 border-t border-gray-800 pt-8 text-center text-xs text-gray-500">
                &copy; {{ date('Y') }} {{ $siteSettings->app_name ?? config('app.name') }}. All rights reserved.
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>

</html>
