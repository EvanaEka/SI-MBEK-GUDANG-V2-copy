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

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
    
    <div x-data="{ isSidebarOpen: false }">
        {{-- TOP NAVBAR --}}
        <nav class="bg-white border-b border-gray-200 fixed z-40 w-full">
            <div class="px-3 py-3 lg:px-5 lg:pl-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center justify-start">
                       <button @click="isSidebarOpen = !isSidebarOpen"
                            class="lg:hidden mr-2 text-gray-700 hover:text-gray-900 cursor-pointer p-2 hover:bg-gray-100 focus:bg-gray-100 focus:ring-2 focus:ring-gray-100 rounded">
                            <svg x-show="!isSidebarOpen" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h6a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            <svg x-show="isSidebarOpen" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" style="display: none;">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                        <div class="flex items-center space-x-3">
                            <img class="w-12 h-12" src="{{ $settings->site_logo ? asset('storage/'.$settings->site_logo) : asset('logo/logosiembek.png') }}" alt="Site Logo">
                            <span class="text-2xl font-bold text-gray-800">
                                {{ $settings->site_name ?? 'SI MBEK' }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="flex items-center">
                        <nav x-data="{ open: false }" class="border-gray-100">
                            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                                <div class="flex justify-between h-16">
                                    <div class="flex items-center">
                                        <span class="hidden sm:flex sm:items-center sm:ms-6 font-medium text-gray-700 px-2">{{ Auth::guard('owner')->user()->name }}</span>
                                        <div class="relative" x-data="{ open: false }">
                                           <button @click="open = !open" class="flex items-center justify-center w-10 h-10 rounded-full bg-brand-orange text-white focus:outline-none focus:ring-2 focus:ring-orange-300 overflow-hidden shadow" id="user-menu-button" aria-expanded="false" aria-haspopup="true">
    @php
        $owner = Auth::guard('owner')->user();
        $avatar = $owner->profile_picture ? asset('storage/owner_avatars/' . $owner->profile_picture) : null;
    @endphp

    @if($avatar)
        <img src="{{ $avatar }}" alt="Profile" class="w-full h-full object-cover">
    @else
        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.655 6.879 1.804M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
        </svg>
    @endif
</button>
                                            <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-44 bg-white rounded-md shadow-lg py-2 z-50 border border-gray-100" style="display: none;">
                                                <a href="{{ route('owner.profile.edit') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 transition">Profil</a>
                                                <form method="POST" action="{{ route('owner.logout') }}" id="owner-logout-form">
                                                    @csrf
                                                    <button type="button" onclick="confirmOwnerLogout(event)" class="w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100 transition">
                                                        Keluar
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </nav>
                    </div>
                </div>
            </div>
        </nav>

        {{-- SIDEBAR & MAIN CONTENT WRAPPER --}}
        <div class="flex overflow-hidden bg-white pt-16">
            
            {{-- SIDEBAR --}}
            <aside id="sidebar" 
                :class="isSidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
                class="fixed z-30 h-full top-0 left-0 pt-16 flex flex-shrink-0 flex-col w-64 transition-transform duration-300 ease-in-out bg-white border-r border-gray-200 shadow-xl lg:shadow-none" 
                aria-label="Sidebar">
                
                {{-- 🔥 3 KOTAK WRAPPER INI WAJIB ADA (Biar ada jarak atas & menu bisa di-scroll) 🔥 --}}
                <div class="relative flex-1 flex flex-col min-h-0 bg-white pt-0">
                    <div class="flex-1 flex flex-col pt-5 pb-4 overflow-y-auto">
                        <div class="flex-1 px-3 bg-white divide-y space-y-1">
                            
                            {{-- Menu Links --}}
                            <ul class="space-y-2 pb-2 py-2">
                                
                                {{-- Dashboard --}}
                                <li>
                                    <x-sidebar-link :href="route('owner.dashboard')" :active="request()->routeIs('owner.dashboard')">
                                        <x-slot name="icon">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg"><path d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L2 8.207V13.5A1.5 1.5 0 0 0 3.5 15h9a1.5 1.5 0 0 0 1.5-1.5V8.207l.646.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293zM13 7.207V13.5a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5V7.207l5-5z" /></svg>
                                        </x-slot>
                                        {{ __('Dashboard') }}
                                    </x-sidebar-link>
                                </li>

                                {{-- Pengguna --}}
                                <li>
                                    <x-sidebar-link :href="route('owner.penitip')" :active="request()->routeIs('owner.penitip')">
                                        <x-slot name="icon">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg"><path d="M11 8a3 3 0 1 1-6 0 3 3 0 0 1 6 0" /><path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2M9.5 3A1.5 1.5 0 0 0 11 4.5h2v9.255S12 12 8 12s-5 1.755-5 1.755V2a1 1 0 0 1 1-1h5.5z" /></svg>
                                        </x-slot>
                                        {{ __('Pengguna') }}
                                    </x-sidebar-link>
                                </li>

                                {{-- Dropdown Master Data --}}
                                @php
                                    $isMasterDataActive = request()->routeIs('owner.listkambing') || request()->routeIs('owner.listdomba') || request()->routeIs('owner.materials.index') || request()->routeIs('owner.products.index') || request()->routeIs('owner.suppliers.index') || request()->routeIs('owner.owners.index');
                                @endphp
                                <li class="relative" x-data="{ open: {{ $isMasterDataActive ? 'true' : 'false' }} }">
                                    <button @click="open = !open" class="flex items-center justify-between p-2 w-full text-base font-normal text-gray-900 rounded-lg hover:bg-gray-100 group {{ $isMasterDataActive ? 'bg-gray-100' : '' }}">
                                        <div class="flex items-center">
                                            {{-- ICON WARNA OREN (Otomatis abu-abu kalau aktif/di-hover) --}}
                                            <svg class="w-5 h-5 transition duration-75 {{ $isMasterDataActive ? 'text-gray-900' : 'text-brand-orange group-hover:text-gray-900' }}" fill="currentColor" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M.54 3.87.5 3a2 2 0 0 1 2-2h3.672a2 2 0 0 1 1.414.586l.828.828A2 2 0 0 0 9.828 3h3.982a2 2 0 0 1 1.992 2.181l-.637 7A2 2 0 0 1 13.174 14H2.826a2 2 0 0 1-1.991-1.819l-.637-7a2 2 0 0 1 .342-1.31zM2.19 4a1 1 0 0 0-.996 1.09l.637 7a1 1 0 0 0 .995.91h10.348a1 1 0 0 0 .995-.91l.637-7A1 1 0 0 0 13.81 4zm4.69-1.707A1 1 0 0 0 6.172 2H2.5a1 1 0 0 0-1 .981l.006.139q.323-.119.684-.12h5.396z" />
                                            </svg>
                                            <span class="ml-3 text-left whitespace-nowrap">Master Data</span>
                                        </div>
                                        <svg class="w-4 h-4 text-gray-500 transition-transform duration-200 transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"></path></svg>
                                    </button>
                                    <ul x-show="open" @click.away="open = false" x-transition x-cloak class="mt-2 bg-white shadow-lg rounded-md w-full z-10 overflow-hidden">
                                        <li><a href="{{ route('owner.owners.index') }}" class="block w-full pl-11 pr-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('owner.owners.index') ? 'bg-orange-50 text-brand-orange font-medium' : '' }}">Data Admin</a></li>
                                        <li><a href="{{ route('owner.listkambing') }}" class="block w-full pl-11 pr-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('owner.listkambing') ? 'bg-orange-50 text-brand-orange font-medium' : '' }}">Kambing</a></li>
                                        <li><a href="{{ route('owner.listdomba') }}" class="block w-full pl-11 pr-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('owner.listdomba') ? 'bg-orange-50 text-brand-orange font-medium' : '' }}">Domba</a></li>
                                        <li><a href="{{ route('owner.materials.index') }}" class="block w-full pl-11 pr-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('owner.materials.index') ? 'bg-orange-50 text-brand-orange font-medium' : '' }}">Material</a></li>
                                        <li><a href="{{ route('owner.products.index') }}" class="block w-full pl-11 pr-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('owner.products.index') ? 'bg-orange-50 text-brand-orange font-medium' : '' }}">Produk</a></li>
                                        <li><a href="{{ route('owner.suppliers.index') }}" class="block w-full pl-11 pr-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('owner.suppliers.index') ? 'bg-orange-50 text-brand-orange font-medium' : '' }}">Supplier</a></li>
                                    </ul>
                                </li>

                                {{-- Penjualan --}}
                                <li>
                                    <x-sidebar-link :href="route('owner.penjualan')" :active="request()->routeIs('owner.penjualan')">
                                        <x-slot name="icon">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg"><path d="M1.92.506a.5.5 0 0 1 .434.14L3 1.293l.646-.647a.5.5 0 0 1 .708 0L5 1.293l.646-.647a.5.5 0 0 1 .708 0L7 1.293l.646-.647a.5.5 0 0 1 .708 0L9 1.293l.646-.647a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .801.13l.5 1A.5.5 0 0 1 15 2v12a.5.5 0 0 1-.053.224l-.5 1a.5.5 0 0 1-.8.13L13 14.707l-.646.647a.5.5 0 0 1-.708 0L11 14.707l-.646.647a.5.5 0 0 1-.708 0L9 14.707l-.646.647a.5.5 0 0 1-.708 0L7 14.707l-.646.647a.5.5 0 0 1-.708 0L5 14.707l-.646.647a.5.5 0 0 1-.708 0L3 14.707l-.646.647a.5.5 0 0 1-.801-.13l-.5-1A.5.5 0 0 1 1 14V2a.5.5 0 0 1 .053-.224l.5-1a.5.5 0 0 1 .367-.27m.217 1.338L2 2.118v11.764l.137.274.51-.51a.5.5 0 0 1 .707 0l.646.647.646-.646a.5.5 0 0 1 .708 0l.646.646.646-.646a.5.5 0 0 1 .708 0l.646.646.646-.646a.5.5 0 0 1 .708 0l.646.646.646-.646a.5.5 0 0 1 .708 0l.646.646.646-.646a.5.5 0 0 1 .708 0l.509.509.137-.274V2.118l-.137-.274-.51.51a.5.5 0 0 1-.707 0L12 1.707l-.646.647a.5.5 0 0 1-.708 0L10 1.707l-.646.647a.5.5 0 0 1-.708 0L8 1.707l-.646.647a.5.5 0 0 1-.708 0L6 1.707l-.646.647a.5.5 0 0 1-.708 0L4 1.707l-.646.647a.5.5 0 0 1-.708 0z" /><path d="M3 4.5a.5.5 0 0 1 .5-.5h6a.5.5 0 1 1 0 1h-6a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 1 1 0 1h-6a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 1 1 0 1h-6a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5m8-6a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5" /></svg>
                                        </x-slot>
                                        {{ __('Penjualan') }}
                                    </x-sidebar-link>
                                </li>

                                {{-- Pemesanan Bahan --}}
                                <li>
                                    <x-sidebar-link :href="route('owner.purchase-orders.index')" :active="request()->routeIs('owner.purchase-orders.*')">
                                        <x-slot name="icon">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg"><path d="M0 1.5A.5.5 0 0 1 .5 1h15a.5.5 0 0 1 0 1H.5A.5.5 0 0 1 0 1.5zM1 4h14v10H1V4zm1 1v8h12V5H2z" /></svg>
                                        </x-slot>
                                        {{ __('Pemesanan Bahan') }}
                                    </x-sidebar-link>
                                </li>

                                {{-- Inventori Bahan --}}
                                <li>
                                    <x-sidebar-link :href="route('owner.material.index')" :active="request()->routeIs('owner.material.*')">
                                        <x-slot name="icon">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg"><path d="M8.186 1.113a.5.5 0 0 0-.372 0L1.846 3.5l2.404.961L10.404 2zm3.564 1.426L5.596 5 8 5.961 14.154 3.5zm.325 1.483L5.15 6.443v4.385L12.075 8.31V4.022zM4.15 6.443 1.075 5.212v3.098l2.903 1.162zm.174 5.376 3.162 1.265a.5.5 0 0 0 .372 0L11 11.819V9.567L8.186 10.693a.5.5 0 0 1-.372 0L4.324 9.567zM1.075 9.424v3.098l2.903-1.162V8.262z" /></svg>
                                        </x-slot>
                                        {{ __('Inventori Bahan') }}
                                    </x-sidebar-link>
                                </li>

                                {{-- Inventori Produk --}}
                                <li>
                                    <x-sidebar-link :href="route('owner.inventory.product.index')" :active="request()->routeIs('owner.inventory.product.*')">
                                        <x-slot name="icon">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg"><path d="M7.752.066a.5.5 0 0 1 .496 0l3.75 2.143a.5.5 0 0 1 .252.434v3.995l3.498 2a.5.5 0 0 1 .252.434v3.995a.5.5 0 0 1-.252.434l-3.75 2.143a.5.5 0 0 1-.496 0l-3.502-2-3.502 2a.5.5 0 0 1-.496 0l-3.75-2.143A.5.5 0 0 1 0 13.067V9.072a.5.5 0 0 1 .252-.434l3.498-2V2.643a.5.5 0 0 1 .252-.434L7.752.066ZM4.25 12.13V7.137L1.5 8.708v3.422l2.75 1.571Zm.5 1.714 2.75-1.571V7.273l-2.75 1.571v4.999Zm3.25-1.571 2.75 1.571V8.995L8 7.424v4.999ZM11.25 7.137l2.75 1.571V5.286L11.25 3.715v3.422ZM8 1.424l-2.75 1.571v3.422L8 4.846l2.75 1.571V2.995L8 1.424Z" /></svg>
                                        </x-slot>
                                        {{ __('Inventori Produk') }}
                                    </x-sidebar-link>
                                </li>

                                {{-- Gudang --}}
                                <li>
                                    <x-sidebar-link :href="route('owner.warehouse.dashboard')" :active="request()->routeIs('owner.warehouse.*')">
                                        <x-slot name="icon">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                                        </x-slot>
                                        {{ __('Gudang') }}
                                    </x-sidebar-link>
                                </li>

                                {{-- Laporan --}}
                                <li>
                                    <x-sidebar-link :href="route('owner.report.stock')" :active="request()->routeIs('owner.report.*')">
                                        <x-slot name="icon">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                        </x-slot>
                                        {{ __('Laporan') }}
                                    </x-sidebar-link>
                                </li>
                            </ul>
                            
                            {{-- Sidebar Bottom (Profile & Logout) --}}
                            <div class="space-y-2 pt-2 ">
                                <div class="pt-4 pb-1 border-t border-gray-200">
                                    <div class="px-4">
                                        <div class="font-medium text-base text-gray-800">{{ Auth::guard('owner')->user()->name }}</div>
                                        <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                                    </div>
                                    <div class="mt-3 flex flex-col gap-2 px-3">
                                        <a href="{{ route('owner.profile.edit') }}" class="w-full bg-brand-orange hover:bg-orange-700 text-white font-semibold py-2 rounded-md text-center transition-colors duration-200">
                                            Profil
                                        </a>
                                        <form method="POST" action="{{ route('owner.logout') }}">
                                            @csrf
                                            <button type="submit" onclick="confirmOwnerLogout(event)" class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-2 rounded-md text-center transition-colors duration-200">
                                                Keluar
                                            </button>
                                        </form>
                                        <a href="/" target="_blank" class="w-full bg-cyan-600 hover:bg-cyan-700 text-white font-semibold py-2 rounded-md text-center transition-colors duration-200 mt-2">
                                            Lihat Web
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </aside>
            
           <div x-show="isSidebarOpen" @click="isSidebarOpen = false" x-transition.opacity 
                class="bg-gray-900/50 fixed inset-0 z-20 lg:hidden" style="display: none;"></div>
            
           {{-- MAIN CONTENT AREA --}}
            <div id="main-content" class="min-h-screen w-full bg-gray-50 relative overflow-y-auto lg:ml-64">
                
                <main>
                    {{ $slot }}
                </main>
                
                <footer class="bg-orange-100 md:flex md:items-center md:justify-between shadow rounded-lg p-4 md:p-6 xl:p-8 my-6 mx-4">
                    <ul class="flex items-center flex-wrap mb-6 md:mb-0">
                        <li>
                            <a href="#" class="text-sm font-normal text-gray-700 hover:underline mr-4 md:mr-6">
                                {{ e($settings->site_name ?? 'SI MBEK') }}
                            </a>
                        </li>
                    </ul>
                    <div class="flex sm:justify-center space-x-6">
                        @foreach (['twitter', 'facebook', 'instagram'] as $social)
                            @if ($settings->social[$social]['active'] ?? false)
                                <a href="{{ $settings->social[$social]['url'] }}" class="bg-brand-orange p-2 rounded-full hover:bg-yellow-500 transition-colors" target="_blank">
                                    @if ($social === 'twitter')
                                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z" />
                                        </svg>
                                    @elseif($social === 'facebook')
                                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm3 8h-1.35c-.538 0-.65.221-.65.778v1.222h2l-.209 2h-1.791v7h-3v-7h-2v-2h2v-2.308c0-1.769.931-2.692 3.029-2.692h1.971v3z" />
                                        </svg>
                                    @elseif($social === 'instagram')
                                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
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
            function confirmOwnerLogout(event) {
                event.preventDefault();

                Swal.fire({
                    title: 'Keluar dari akun Owner?',
                    text: "Apakah Anda yakin ingin keluar?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, keluar!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('owner-logout-form').submit();
                    }
                });
            }
        </script>
    </div>
    
    @stack('scripts')
</body>
</html>