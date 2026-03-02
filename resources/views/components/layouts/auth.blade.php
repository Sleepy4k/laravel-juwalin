<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Auth' }} — {{ $siteSettings->app_name ?? config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col items-center justify-center bg-gray-950 text-gray-100 px-4">

    <div class="w-full max-w-md">
        {{-- Logo --}}
        <a href="{{ route('home') }}" class="flex items-center justify-center gap-2.5 font-bold text-xl text-white mb-8">
            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-brand-500 text-white font-black">H</span>
            {{ $siteSettings->app_name ?? config('app.name') }}
        </a>

        {{-- Card --}}
        <div class="card">
            <div class="card-body">
                {{ $slot }}
            </div>
        </div>

        {{-- Footer link --}}
        @isset($footer)
            <div class="mt-6 text-center text-sm text-gray-400">
                {{ $footer }}
            </div>
        @endisset
    </div>
</body>
</html>
