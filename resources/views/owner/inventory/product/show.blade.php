<x-owner-app-layout>
    <div class="p-6 max-w-6xl mx-auto">

        {{-- Header --}}
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('owner.inventory.product.index') }}"
                class="p-2 rounded-lg hover:bg-gray-100 text-gray-500 hover:text-gray-700 transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div class="flex-1 min-w-0">
                <div class="flex flex-wrap items-center gap-2">
                    <h1 class="text-2xl font-bold text-gray-800">{{ $product->nama }}</h1>
                    <span class="font-mono text-sm text-gray-400">{{ $product->kode }}</span>
                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold
                        {{ $product->type === 'pakan' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">
                        {{ ucfirst($product->type) }}
                    </span>
                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold
                        {{ $product->source === 'produksi' ? 'bg-orange-100 text-orange-700' : 'bg-purple-100 text-purple-700' }}">
                        {{ ucfirst($product->source) }}
                    </span>
                    @if($product->isBelowRop())
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700 border border-red-200">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            Di Bawah ROP
                        </span>
                    @endif
                </div>
                <p class="text-sm text-gray-500 mt-0.5">Inventori detail · batch · riwayat pergerakan stok</p>
            </div>
        </div>

        {{-- Flash --}}
        @if(session('success'))
            <div class="mb-5 flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl text-sm">
                <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session('warning'))
            <div class="mb-5 flex items-center gap-3 bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-xl text-sm">
                <svg class="w-5 h-5 text-yellow-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                {{ session('warning') }}
            </div>
        @endif
        @if($errors->any())
            <div class="mb-5 bg-red-50 border border-red-200 rounded-xl p-4">
                <p class="text-sm font-semibold text-red-700 mb-1">Terdapat kesalahan:</p>
                @foreach($errors->all() as $e)
                    <p class="text-sm text-red-600">• {{ $e }}</p>
                @endforeach
            </div>
        @endif

        {{-- Shared vars untuk semua partials --}}
        @php
            $belowRop  = $product->isBelowRop();
            $qJual     = $allocations->where('type', 'jual')->first()?->qty ?? 0;
            $qInternal = $allocations->where('type', 'internal')->first()?->qty ?? 0;
            $activeTab = request('tab', 'overview');
        @endphp

        {{-- Tab Nav --}}
        <div class="flex gap-1 border-b border-gray-200 mb-6">
            @php
                $tabs = [
                    'overview'  => ['label' => '📦 Overview',          'icon' => null],
                    'batches'   => ['label' => '📋 Batch & Riwayat',   'icon' => null],
                ];
            @endphp
            @foreach($tabs as $key => $tab)
                <a href="{{ request()->fullUrlWithQuery(['tab' => $key]) }}"
                    class="px-5 py-2.5 text-sm font-semibold rounded-t-lg border-b-2 -mb-px transition-colors
                        {{ $activeTab === $key
                            ? 'border-orange-500 text-orange-600 bg-white'
                            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    {{ $tab['label'] }}
                    @if($key === 'alokasi' && ($qJual + $qInternal) > $product->stok)
                        <span class="ml-1 inline-flex w-2 h-2 bg-red-500 rounded-full"></span>
                    @endif
                    @if($key === 'overview' && $belowRop)
                        <span class="ml-1 inline-flex w-2 h-2 bg-red-500 rounded-full"></span>
                    @endif
                </a>
            @endforeach
        </div>

        {{-- Tab Content --}}
        @if($activeTab === 'overview')
            @include('owner.inventory.product.partials._overview')
        @elseif($activeTab === 'batches')
            @include('owner.inventory.product.partials._batches')
        @endif

    </div>
</x-owner-app-layout>