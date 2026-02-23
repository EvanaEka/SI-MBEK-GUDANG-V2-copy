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

    {{-- <title>Si MBEK</title> --}}

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
    {{-- <div class="min-h-screen bg-gray-100">
            @include('admin.layouts.navigation') --}}

    <!-- Page Heading -->
    {{-- @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset --}}

    {{-- <!-- Page Content -->
            <main>
                {{ $slot }}


            </main>
        </div> --}}

    <!-- component -->
    <!-- This is an example component -->
    <div>
        <nav class="bg-white border-b border-gray-200 fixed z-30 w-full">
            <div class="px-3 py-3  lg:px-5 lg:pl-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center justify-start">
                        <button id="toggleSidebarMobile" aria-expanded="true" aria-controls="sidebar"
                            class="lg:hidden mr-2 text-gray-700 hover:text-gray-900 cursor-pointer p-2 hover:bg-gray-100 focus:bg-gray-100 focus:ring-2 focus:ring-gray-100 rounded">
                            <svg id="toggleSidebarMobileHamburger" class="w-6 h-6" fill="currentColor"
                                viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h6a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <svg id="toggleSidebarMobileClose" class="w-6 h-6 hidden" fill="currentColor"
                                viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </button>
                        <div class="flex items-center space-x-3">
                
                <!-- Dynamic Logo -->
                <img class="w-12 h-12" 
                     src="{{ $settings->site_logo ? asset('storage/'.$settings->site_logo) : asset('logo/logosiembek.png') }}" 
                     alt="Site Logo">
                
                <!-- Dynamic Site Name -->
                <span class="text-2xl font-bold text-gray-800">
                    {{ $settings->site_name ?? 'SI MBEK' }}
                </span>
            </div>

                        {{-- <form action="#" method="GET" class="hidden lg:block lg:pl-32">
                   <label for="topbar-search" class="sr-only">Search</label>
                   <div class="mt-1 relative lg:w-64">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                         <svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                         </svg>
                      </div>
                      <input type="text" name="email" id="topbar-search" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full pl-10 p-2.5" placeholder="Search">
                   </div>
                </form> --}}
                    </div>
                    <div class="flex items-center">
                        <button id="toggleSidebarMobileSearch" type="button"
                            class="hidden lg:hidden text-white hover:text-gray-900 hover:bg-gray-100 p-2 rounded-lg">


                        </button>
                        <nav x-data="{ open: false }" class=" border-sb bsorder-gray-100">
                            <!-- Primary Navigation Menu -->
                            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                                <div class="flex justify-between h-16">


                                    {{-- <div class="flex">
                                <!-- Logo -->
                                <div class="shrink-0 flex items-center">
                                    <a href="{{ route('admin.dashboard') }}">
                                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                                    </a>
                                </div>

                                <!-- Navigation Links -->
                                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                                    <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                                        {{ __('Admin Dashboard') }}
                                    </x-nav-link>
                                </div>
                            </div> --}}

                                    <!-- Settings Dropdown -->
                                    <div class="flex items-center">

                                        <span class="hidden sm:flex sm:items-center sm:ms-6 font-medium text-gray-700 px-2">{{ Auth::user()->name }}</span>
                                         <!-- User Dropdown -->
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open"
                class="flex items-center justify-center w-10 h-10 rounded-full bg-brand-orange text-white focus:outline-none focus:ring-2 focus:ring-orange-300"
                id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.655 6.879 1.804M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </button>
            <!-- Dropdown menu -->
            <div x-show="open" @click.away="open = false" x-transition
                class="absolute right-0 mt-2 w-44 bg-white rounded-md shadow-lg py-2 z-50 border border-gray-100"
                style="display: none;">
                <a href="{{ route('admin.profile.edit') }}"
                    class="block px-4 py-2 text-gray-700 hover:bg-gray-100 transition">Profil</a>
                <form method="POST" action="{{ route('admin.logout') }}" id="admin-logout-form">
    @csrf
    <button type="button"
        onclick="confirmAdminLogout(event)"
        class="w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100 transition">
        Keluar
    </button>
</form>

            </div>
        </div>


                                </div>
                            </div>

                            <!-- Responsive Navigation Menu -->
                            {{-- <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
                        <div class="pt-2 pb-3 space-y-1">
                            <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                                {{ __('Dashboard') }}
                            </x-responsive-nav-link>
                        </div>

                        <!-- Responsive Settings Options -->
                        <div class="pt-4 pb-1 border-t border-gray-200">
                            <div class="px-4">
                                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                            </div>

                            <div class="mt-3 space-y-1">
                                <x-responsive-nav-link :href="route('admin.profile.edit')">
                                    {{ __('Profile') }}
                                </x-responsive-nav-link>

                                <!-- Authentication -->
                                <form method="POST" action="{{ route('admin.logout') }}">
                                    @csrf

                                    <x-responsive-nav-link :href="route('admin.logout')"
                                            onclick="event.preventDefault();
                                                        this.closest('form').submit();">
                                        {{ __('Log Out') }}
                                    </x-responsive-nav-link>
                                </form>
                            </div>
                        </div>
                    </div> --}}
                        </nav>

                    </div>
                </div>
            </div>
        </nav>
        <div class="flex overflow-hidden bg-white pt-16">
            <aside id="sidebar"
                class="fixed hidden z-20 h-full top-0 left-0 pt-16 flex lg:flex flex-shrink-0 flex-col w-64 transition-width duration-75"
                aria-label="Sidebar">
                <div class="relative flex-1 flex flex-col min-h-0 border-r border-gray-200 bg-white pt-0">
                    <div class="flex-1 flex flex-col pt-5 pb-4 overflow-y-auto">
                        <div class="flex-1 px-3 bg-white divide-y space-y-1">
                            <ul class="space-y-2 pb-2 py-2">
                                {{-- <li>
                                    <form action="#" method="GET" class="lg:hidden mt-10">
                                        <label for="mobile-search" class="sr-only">Search</label>
                                        <div class="relative">
                                            <div
                                                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <svg class="w-5 h-5 text-gray-500" fill="currentColor"
                                                    viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z">
                                                    </path>
                                                </svg>
                                            </div>
                                            <input type="text" name="email" id="mobile-search"
                                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-cyan-600 focus:ring-cyan-600 block w-full pl-10 p-2.5"
                                                placeholder="Search">
                                        </div>
                                    </form>
                                </li> --}}
                                <li class="flex ">
                                    <a href="#"
                                        class="text-base text-gray-900 font-normal rounded-lg flex items-center p-2 hover:bg-gray-100 group">
                                        {{-- <svg class="w-6 h-6 text-brand-orange group-hover:text-gray-900 transition duration-75"
                                            fill="currentColor" viewBox="0 0 25 25" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd"
                                                d="M11.293 3.293a1 1 0 0 1 1.414 0l6 6 2 2a1 1 0 0 1-1.414 1.414L19 12.414V19a2 2 0 0 1-2 2h-3a1 1 0 0 1-1-1v-3h-2v3a1 1 0 0 1-1 1H7a2 2 0 0 1-2-2v-6.586l-.293.293a1 1 0 0 1-1.414-1.414l2-2 6-6Z"
                                                clip-rule="evenodd" />
                                        </svg> --}}
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                            class="w-5 h-5 text-brand-orange group-hover:text-gray-900 transition duration-75"
                                            viewBox="0 0 16 16">
                                            <path
                                                d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L2 8.207V13.5A1.5 1.5 0 0 0 3.5 15h9a1.5 1.5 0 0 0 1.5-1.5V8.207l.646.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293zM13 7.207V13.5a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5V7.207l5-5z" />
                                        </svg>
                                        <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')" >
                                            {{ __('Dashboard') }}
                                        </x-responsive-nav-link>
                                    </a>
                                </li>

                                <li class="flex">
                                    <a href="#"
                                        class="text-base text-gray-900 font-normal rounded-lg flex items-center p-2 hover:bg-gray-100 group">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                            class="bi bi-file-earmark-person w-5 h-5 text-brand-orange group-hover:text-gray-900 transition duration-75"
                                            viewBox="0 0 16 16">
                                            <path d="M11 8a3 3 0 1 1-6 0 3 3 0 0 1 6 0" />
                                            <path
                                                d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2M9.5 3A1.5 1.5 0 0 0 11 4.5h2v9.255S12 12 8 12s-5 1.755-5 1.755V2a1 1 0 0 1 1-1h5.5z" />
                                        </svg>

                                        <x-responsive-nav-link :href="route('admin.penitip')" :active="request()->routeIs('admin.penitip')">
                                            {{ __('Pengguna') }}
                                        </x-responsive-nav-link>
                                    </a>
                                </li>

                                <li class="relative" x-data="{ open: {{ request()->routeIs('admin.tambahkambing') || request()->routeIs('admin.tambahdomba') ? 'true' : 'false' }} }">
                                    <!-- Main Button -->
                                    <button @click="open = !open"
                                        class="w-full text-gray-600 font-medium rounded-lg flex items-center justify-between p-2 hover:bg-gray-100 group {{ request()->routeIs('admin.tambahkambing') || request()->routeIs('admin.tambahdomba') ? 'border-l-4 border-orange-400 bg-orange-50 text-orange-700' : '' }}">
                                        <span class="flex items-center">
                                            <span
                                                class="border-l-4 border-transparent {{ request()->routeIs('admin.tambahkambing') || request()->routeIs('admin.tambahdomba') ? 'border-indigo-400' : '' }} pr-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                    class="w-5 h-5 text-brand-orange group-hover:text-gray-900 transition duration-75"
                                                    viewBox="0 0 16 16">
                                                    <path
                                                        d="m.5 3 .04.87a2 2 0 0 0-.342 1.311l.637 7A2 2 0 0 0 2.826 14H9v-1H2.826a1 1 0 0 1-.995-.91l-.637-7A1 1 0 0 1 2.19 4h11.62a1 1 0 0 1 .996 1.09L14.54 8h1.005l.256-2.819A2 2 0 0 0 13.81 3H9.828a2 2 0 0 1-1.414-.586l-.828-.828A2 2 0 0 0 6.172 1H2.5a2 2 0 0 0-2 2m5.672-1a1 1 0 0 1 .707.293L7.586 3H2.19q-.362.002-.683.12L1.5 2.98a1 1 0 0 1 1-.98z" />
                                                    <path
                                                        d="M13.5 9a.5.5 0 0 1 .5.5V11h1.5a.5.5 0 1 1 0 1H14v1.5a.5.5 0 1 1-1 0V12h-1.5a.5.5 0 0 1 0-1H13V9.5a.5.5 0 0 1 .5-.5" />
                                                </svg>
                                            </span>
                                            <span class="ml-2 group-hover:text-gray-800">Tambah</span>
                                        </span>
                                        <svg class="w-4 h-4 text-gray-500 transition-transform duration-200 transform"
                                            :class="{ 'rotate-180': open }" fill="none" stroke="currentColor"
                                            stroke-width="2" viewBox="0 0 24 24">
                                            <path d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>

                                    <!-- Dropdown -->
                                    <ul x-show="open" @click.away="open = false" x-transition x-cloak
                                        class="mt-2 bg-white shadow-lg rounded-md w-full z-10">
                                        <li>
                                            <x-responsive-nav-link href="{{ route('admin.tambahkambing') }}"
                                                class="{{ request()->routeIs('admin.tambahkambing') ? 'block w-full ps-3 pe-4 py-2 border-indigo-400 text-start text-base font-medium text-orange-700 bg-orange-50 focus:outline-none focus:text-orange-800 focus:bg-orange-100 focus:border-orange-700 transition duration-150 ease-in-out' : 'block w-full ps-3 pe-4 py-2 border-transparent text-start text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out' }}"
                                                @click="if (!{{ request()->routeIs('admin.tambahkambing') }}) { open = false; }">
                                                Kambing
                                            </x-responsive-nav-link>
                                        </li>
                                        <li>
                                            <x-responsive-nav-link href="{{ route('admin.tambahdomba') }}"
                                                class="{{ request()->routeIs('admin.tambahdomba') ? 'block w-full ps-3 pe-4 py-2 border-indigo-400 text-start text-base font-medium text-orange-700 bg-orange-50 focus:outline-none focus:text-orange-800 focus:bg-orange-100 focus:border-orange-700 transition duration-150 ease-in-out' : 'block w-full ps-3 pe-4 py-2 border-transparent text-start text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out' }}"
                                                @click="if (!{{ request()->routeIs('admin.tambahdomba') }}) { open = false; }">
                                                Domba
                                            </x-responsive-nav-link>
                                        </li>
                                    </ul>
                                </li>

                                <li class="relative" x-data="{ open: {{ request()->routeIs('admin.listkambing') || request()->routeIs('admin.listdomba') || request()->routeIs('admin.kambing.show') || request()->routeIs('admin.domba.show') || request()->routeIs('admin.kambings.update') || request()->routeIs('admin.dombas.update') ? 'true' : 'false' }} }">
                                    <!-- Main Button -->
                                    <button @click="open = !open"
                                        class="w-full text-gray-600 font-medium rounded-lg flex items-center justify-between p-2 hover:bg-gray-100 group {{ request()->routeIs('admin.listkambing') || request()->routeIs('admin.listdomba') || request()->routeIs('admin.kambing.show') || request()->routeIs('admin.domba.show') || request()->routeIs('admin.kambings.update') || request()->routeIs('admin.dombas.update') ? 'border-l-4 border-orange-400 bg-orange-50 text-orange-700' : '' }}">
                                        <span class="flex items-center">
                                            <span
                                                class="border-l-4 border-transparent {{ request()->routeIs('admin.listkambing') || request()->routeIs('admin.listdomba') ? 'border-indigo-400' : '' }} pr-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor"
                                                    class="w-5 h-5 text-brand-orange group-hover:text-gray-900 transition duration-75"
                                                    viewBox="0 0 16 16">
                                                    <path
                                                        d="M.54 3.87.5 3a2 2 0 0 1 2-2h3.672a2 2 0 0 1 1.414.586l.828.828A2 2 0 0 0 9.828 3h3.982a2 2 0 0 1 1.992 2.181l-.637 7A2 2 0 0 1 13.174 14H2.826a2 2 0 0 1-1.991-1.819l-.637-7a2 2 0 0 1 .342-1.31zM2.19 4a1 1 0 0 0-.996 1.09l.637 7a1 1 0 0 0 .995.91h10.348a1 1 0 0 0 .995-.91l.637-7A1 1 0 0 0 13.81 4zm4.69-1.707A1 1 0 0 0 6.172 2H2.5a1 1 0 0 0-1 .981l.006.139q.323-.119.684-.12h5.396z" />
                                                </svg>
                                            </span>
                                            <span class="ml-2 group-hover:text-gray-800">Produk</span>
                                        </span>
                                        <svg class="w-4 h-4 text-gray-500 transition-transform duration-200 transform"
                                            :class="{ 'rotate-180': open }" fill="none" stroke="currentColor"
                                            stroke-width="2" viewBox="0 0 24 24">
                                            <path d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>

                                    <!-- Dropdown -->
                                    <ul x-show="open" @click.away="open = false" x-transition x-cloak
                                        class="mt-2 bg-white shadow-lg rounded-md w-full z-10">
                                        <li>
                                            <x-responsive-nav-link href="{{ route('admin.listkambing') }}"
                                                class="{{ request()->routeIs('admin.listkambing') || request()->routeIs('admin.kambing.show') || request()->routeIs('admin.kambings.update') ? 'block w-full ps-3 pe-4 py-2 border-indigo-400 text-start text-base font-medium text-orange-700 bg-orange-50 focus:outline-none focus:text-orange-800 focus:bg-orange-100 focus:border-orange-700 transition duration-150 ease-in-out' : 'block w-full ps-3 pe-4 py-2 border-transparent text-start text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out' }}"
                                                @click="if (!{{ request()->routeIs('admin.listkambing') }}) { open = false; }">
                                                Kambing
                                            </x-responsive-nav-link>
                                        </li>
                                        <li>
                                            <x-responsive-nav-link href="{{ route('admin.listdomba') }}"
                                                class="{{ request()->routeIs('admin.listdomba') || request()->routeIs('admin.domba.show') || request()->routeIs('admin.dombas.update') ? 'block w-full ps-3 pe-4 py-2 border-indigo-400 text-start text-base font-medium text-orange-700 bg-orange-50 focus:outline-none focus:text-orange-800 focus:bg-orange-100 focus:border-orange-700 transition duration-150 ease-in-out' : 'block w-full ps-3 pe-4 py-2 border-transparent text-start text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out' }}"
                                                @click="if (!{{ request()->routeIs('admin.listdomba') || request()->routeIs('admin.domba.show') || request()->routeIs('admin.dombas.update') }}) { open = false; }">
                                                Domba
                                            </x-responsive-nav-link>
                                        </li>
                                    </ul>
                                </li>

                                {{-- <li class="flex">
                                    <a href="#"
                                        class="text-base text-gray-900 font-normal rounded-lg flex items-center p-2 hover:bg-gray-100 group">
                                        <svg class="w-5 h-5 text-brand-orange group-hover:text-gray-900 transition duration-75"
                                            fill="currentColor" viewBox="0 0 16 16"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="m8 0 6.61 3h.89a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.5.5H15v7a.5.5 0 0 1 .485.38l.5 2a.498.498 0 0 1-.485.62H.5a.498.498 0 0 1-.485-.62l.5-2A.5.5 0 0 1 1 13V6H.5a.5.5 0 0 1-.5-.5v-2A.5.5 0 0 1 .5 3h.89zM3.777 3h8.447L8 1zM2 6v7h1V6zm2 0v7h2.5V6zm3.5 0v7h1V6zm2 0v7H12V6zM13 6v7h1V6zm2-1V4H1v1zm-.39 9H1.39l-.25 1h13.72z" />
                                        </svg>
                                        <x-responsive-nav-link :href="route('admin.perjanjian')" :active="request()->routeIs('admin.perjanjian')">
                                            {{ __('Perjanjian') }}
                                        </x-responsive-nav-link>
                                    </a>
                                </li> --}}
                                <li class="flex">
                                    <a href="#"
                                        class="text-base text-gray-900 font-normal rounded-lg flex items-center p-2 hover:bg-gray-100 group">
                                        <svg class="w-5 h-5 text-brand-orange group-hover:text-gray-900 transition duration-75"
                                            fill="currentColor" viewBox="0 0 16 16"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path d="M1.92.506a.5.5 0 0 1 .434.14L3 1.293l.646-.647a.5.5 0 0 1 .708 0L5 1.293l.646-.647a.5.5 0 0 1 .708 0L7 1.293l.646-.647a.5.5 0 0 1 .708 0L9 1.293l.646-.647a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .801.13l.5 1A.5.5 0 0 1 15 2v12a.5.5 0 0 1-.053.224l-.5 1a.5.5 0 0 1-.8.13L13 14.707l-.646.647a.5.5 0 0 1-.708 0L11 14.707l-.646.647a.5.5 0 0 1-.708 0L9 14.707l-.646.647a.5.5 0 0 1-.708 0L7 14.707l-.646.647a.5.5 0 0 1-.708 0L5 14.707l-.646.647a.5.5 0 0 1-.708 0L3 14.707l-.646.647a.5.5 0 0 1-.801-.13l-.5-1A.5.5 0 0 1 1 14V2a.5.5 0 0 1 .053-.224l.5-1a.5.5 0 0 1 .367-.27m.217 1.338L2 2.118v11.764l.137.274.51-.51a.5.5 0 0 1 .707 0l.646.647.646-.646a.5.5 0 0 1 .708 0l.646.646.646-.646a.5.5 0 0 1 .708 0l.646.646.646-.646a.5.5 0 0 1 .708 0l.646.646.646-.646a.5.5 0 0 1 .708 0l.646.646.646-.646a.5.5 0 0 1 .708 0l.509.509.137-.274V2.118l-.137-.274-.51.51a.5.5 0 0 1-.707 0L12 1.707l-.646.647a.5.5 0 0 1-.708 0L10 1.707l-.646.647a.5.5 0 0 1-.708 0L8 1.707l-.646.647a.5.5 0 0 1-.708 0L6 1.707l-.646.647a.5.5 0 0 1-.708 0L4 1.707l-.646.647a.5.5 0 0 1-.708 0z"/>
  <path d="M3 4.5a.5.5 0 0 1 .5-.5h6a.5.5 0 1 1 0 1h-6a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 1 1 0 1h-6a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 1 1 0 1h-6a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5m8-6a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5"/>
                                        </svg>
                                        <x-responsive-nav-link :href="route('admin.penjualan')" :active="request()->routeIs('admin.penjualan')">
                                            {{ __('Penjualan') }}
                                        </x-responsive-nav-link>
                                    </a>
                                </li>
                                <li class="flex">
                                    <a href="#"
                                        class="text-base text-gray-900 font-normal rounded-lg flex items-center p-2 hover:bg-gray-100 group">
                                        <svg class="w-5 h-5 text-brand-orange group-hover:text-gray-900 transition duration-75"
                                            fill="currentColor" viewBox="0 0 16 16"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path stroke="currentColor" stroke-linecap="round"
                                                stroke-linejoin="round" stroke-width="1"
                                                d="M11.5 2a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3M9.05 3a2.5 2.5 0 0 1 4.9 0H16v1h-2.05a2.5 2.5 0 0 1-4.9 0H0V3zM4.5 7a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3M2.05 8a2.5 2.5 0 0 1 4.9 0H16v1H6.95a2.5 2.5 0 0 1-4.9 0H0V8zm9.45 4a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3m-2.45 1a2.5 2.5 0 0 1 4.9 0H16v1h-2.05a2.5 2.5 0 0 1-4.9 0H0v-1z" />
                                        </svg>
                                        <x-responsive-nav-link :href="route('admin.site-settings.edit')" :active="request()->routeIs('admin.site-settings.edit')">
                                            {{ __('Pengaturan') }}
                                        </x-responsive-nav-link>
                                    </a>
                                </li>

                                <li class="flex">
    <a href="#"
        class="text-base text-gray-900 font-normal rounded-lg flex items-center p-2 hover:bg-gray-100 group">
        
        <svg xmlns="http://www.w3.org/2000/svg"
            class="w-5 h-5 text-brand-orange group-hover:text-gray-900 transition duration-75"
            fill="currentColor"
            viewBox="0 0 16 16">
            <path d="M0 1.5A.5.5 0 0 1 .5 1h15a.5.5 0 0 1 0 1H.5A.5.5 0 0 1 0 1.5zM1 4h14v10H1V4zm1 1v8h12V5H2z"/>
        </svg>

        <x-responsive-nav-link 
            :href="route('admin.purchase-orders.index')" 
            :active="request()->routeIs('admin.purchase-orders.*')">
            {{ __('Purchase Order') }}
        </x-responsive-nav-link>
    </a>
</li>

                            </ul>
                            <div class="space-y-2 pt-2 ">
                                <div class="pt-4 pb-1 border-t border-gray-200">
                                    <div class="px-4">
                                        <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}
                                        </div>
                                        <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                                    </div>

                                    <div class="mt-3 flex flex-col gap-2 px-3">
                                        <a href="{{ route('admin.profile.edit') }}"
                                           class="w-full bg-brand-orange hover:bg-orange-700 text-white font-semibold py-2 rounded-md text-center transition-colors duration-200">
                                            Profil
                                        </a>
                                        <form method="POST" action="{{ route('admin.logout') }}">
                                            @csrf
                                            <button type="submit"
                                            onclick="confirmAdminLogout(event)"
                                                class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-2 rounded-md text-center transition-colors duration-200">
                                                Keluar
                                            </button>
                                        </form>
                                        <a href="/" target="_blank"
                                           class="w-full bg-cyan-600 hover:bg-cyan-700 text-white font-semibold py-2 rounded-md text-center transition-colors duration-200 mt-2">
                                            Lihat Web
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </aside>
            <div class="bg-gray-900 opacity-50 hidden fixed inset-0 z-10" id="sidebarBackdrop"></div>
            <div id="main-content" class="h-full w-full bg-gray-50 relative overflow-y-auto lg:ml-64">
                <main>

                    {{ $slot }}

                </main>
                <footer
                    class="bg-orange-100 md:flex  md:items-center md:justify-between shadow rounded-lg p-4 md:p-6 xl:p-8 my-6 mx-4">
                    <ul class="flex items-center flex-wrap mb-6 md:mb-0">
                        <li><a href="#"
                                class="text-sm font-normal text-gray-700 hover:underline mr-4 md:mr-6">{{ e($settings->site_name ?? 'SI MBEK') }}</a>
                        </li>
                        {{-- <li><a href="#"
                                class="text-sm font-normal text-gray-500 hover:underline mr-4 md:mr-6">Privacy
                                Policy</a></li>
                        <li><a href="#"
                                class="text-sm font-normal text-gray-500 hover:underline mr-4 md:mr-6">Licensing</a>
                        </li>
                        <li><a href="#"
                                class="text-sm font-normal text-gray-500 hover:underline mr-4 md:mr-6">Cookie
                                Policy</a></li>
                        <li><a href="#" class="text-sm font-normal text-gray-500 hover:underline">Contact</a>
                        </li> --}}
                    </ul>
                    <div class="flex sm:justify-center space-x-6">
                        @foreach (['twitter', 'facebook', 'instagram'] as $social)
                            @if ($settings->social[$social]['active'] ?? false)
                                <a href="{{ $settings->social[$social]['url'] }}"
                                    class="bg-brand-orange p-2 rounded-full hover:bg-yellow-500 transition-colors"
                                    target="_blank">
                                    @if ($social === 'twitter')
                                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z" />
                                        </svg>
                                    @elseif($social === 'facebook')
                                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm3 8h-1.35c-.538 0-.65.221-.65.778v1.222h2l-.209 2h-1.791v7h-3v-7h-2v-2h2v-2.308c0-1.769.931-2.692 3.029-2.692h1.971v3z" />
                                        </svg>
                                    @elseif($social === 'instagram')
                                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
                                        </svg>
                                    @endif
                                </a>
                            @endif
                        @endforeach
                    </div>
                </footer>
                <p class="text-center text-sm text-gray-500 my-10">
                    &copy; {{ date('Y') }} {{ e($settings->site_name ?? 'SI MBEK') }}. All rights reserved.
                </p>
            </div>
        </div>
        <script async defer src="https://buttons.github.io/buttons.js"></script>
        <script src="https://demo.themesberg.com/windster/app.bundle.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function confirmAdminLogout(event) {
    event.preventDefault();

    Swal.fire({
        title: 'Keluar dari akun Admin?',
        text: "Apakah Anda yakin ingin keluar?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, keluar!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('admin-logout-form').submit();
        }
    });
}
</script>

    </div>
    @stack('scripts')
</body>

</html>
