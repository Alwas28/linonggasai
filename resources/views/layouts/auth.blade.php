<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') — {{ $settings['site_name'] }}</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-cream text-forest-700">
<div class="relative min-h-screen overflow-hidden">
    <div class="absolute -right-24 -top-24 h-96 w-96 rounded-full bg-harvest-100 opacity-60 blur-3xl"></div>
    <div class="absolute -bottom-32 -left-24 h-96 w-96 rounded-full bg-forest-100 opacity-60 blur-3xl"></div>

    <div class="relative mx-auto flex min-h-screen w-full {{ ($width ?? 'md') === '2xl' ? 'max-w-2xl' : 'max-w-md' }} flex-col px-4 py-8 sm:px-6">
        <div class="flex items-center justify-between">
            <x-logo />
            <a href="{{ route('home') }}" class="text-xs font-bold text-forest-500 hover:text-harvest-600">← Beranda</a>
        </div>

        <div class="my-auto pt-8">
            <div class="rounded-3xl border border-forest-100 bg-white p-6 shadow-xl shadow-forest-900/5 sm:p-8">
                @yield('content')
            </div>
        </div>
    </div>
</div>
<x-flash />
</body>
</html>
