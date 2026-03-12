{{--
    resources/views/admin/report/_nav.blade.php
    Include di setiap halaman laporan:
    @include('admin.report._nav', ['active' => 'stock'])
--}}

@php
    $tabs = [
        'stock'      => ['label' => 'Stok',       'icon' => '📦', 'route' => route('admin.report.stock')],
        'production' => ['label' => 'Produksi',   'icon' => '🏭', 'route' => route('admin.report.production')],
        'disposal'   => ['label' => 'Disposal',   'icon' => '🗑',  'route' => route('admin.report.disposal')],
        'monthly'    => ['label' => 'Rekap Bulanan', 'icon' => '📊', 'route' => route('admin.report.monthly')],
    ];
@endphp

<div class="flex items-center justify-between mb-6">
    <div>
        <div class="flex items-center gap-2 mb-1">
            <h1 class="text-2xl font-bold text-gray-900">Laporan</h1>
        </div>
        <p class="text-sm text-gray-500 ml-1">Data & analitik operasional peternakan</p>
    </div>
    <button onclick="window.print()"
        class="inline-flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white font-semibold px-5 py-2.5 rounded-lg shadow text-sm transition-colors">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
        </svg>
        Cetak Laporan
    </button>
</div>

{{-- Tab Nav --}}
<div class="no-print flex gap-0.5 bg-gray-100 p-1 rounded-xl w-fit mb-6 overflow-x-auto">
    @foreach($tabs as $key => $tab)
        <a href="{{ $tab['route'] }}"
            class="flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold whitespace-nowrap transition-all
                {{ $active === $key
                    ? 'bg-white text-gray-900 shadow-sm'
                    : 'text-gray-500 hover:text-gray-700' }}">
            {{ $tab['icon'] }} {{ $tab['label'] }}
        </a>
    @endforeach
</div>