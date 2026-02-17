<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">

        @php
        $settings = App\Models\SiteSetting::first();
        @endphp

        <title>{{ $settings->site_name ?? 'SI MBEK' }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased bg-[#FFF7EC]">
        <div class="min-h-screen flex flex-col justify-center items-center px-4 sm:px-6 lg:px-8 py-6">
            <!-- Logo -->
            <div class="mb-6">
                <a href="/">
                    <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                </a>
            </div>

            <!-- Slot content -->
            <div class="w-full max-w-md bg-white shadow-md rounded-lg p-6 sm:p-8">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
