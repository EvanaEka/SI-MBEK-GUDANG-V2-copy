<x-admin-app-layout>

@push('styles')
<style>
    @media print {
        .no-print { display: none !important; }
        body { background: white !important; }
    }
    .rop-bar { transition: width 0.6s ease; }
</style>
@endpush

<div class="p-6 max-w-7xl mx-auto space-y-6">

    {{-- ══ HEADER ══ --}}
    <div class="flex items-start justify-between">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <h1 class="text-2xl font-bold text-gray-900">Dashboard Gudang</h1>
            </div>
            <p class="text-sm text-gray-500 ml-1">Ringkasan operasional inventori · {{ now()->translatedFormat('d F Y') }}</p>
        </div>
        <a href="{{ route('admin.warehouse.activity-log') }}"
             class="inline-flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white font-semibold px-5 py-2.5 rounded-lg shadow text-sm transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            Log Aktivitas
        </a>
    </div>

    {{-- ══ 1. SUMMARY CARDS ROW (4 Kolom) ══ --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Material --}}
        <a href="{{ route('admin.material.index') }}"
            class="bg-white rounded-xl border shadow-sm p-5 hover:shadow-md transition-shadow group
                {{ $summary['material_below_rop'] > 0 ? 'border-red-200' : 'border-gray-100' }}">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                @if($summary['material_below_rop'] > 0)
                    <span class="text-xs font-bold bg-red-100 text-red-600 px-2 py-0.5 rounded-full animate-pulse">
                        {{ $summary['material_below_rop'] }} ⚠
                    </span>
                @endif
            </div>
            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide">Material</p>
            <p class="text-3xl font-bold text-gray-800 mt-1">{{ number_format($summary['total_material']) }}</p>
            @if($summary['material_below_rop'] > 0)
                <p class="text-xs text-red-500 mt-1 font-medium">{{ $summary['material_below_rop'] }} di bawah ROP</p>
            @else
                <p class="text-xs text-green-500 mt-1">Semua stok aman</p>
            @endif
        </a>

        {{-- Produk --}}
        <a href="{{ route('admin.inventory.product.index') }}"
            class="bg-white rounded-xl border shadow-sm p-5 hover:shadow-md transition-shadow group
                {{ $summary['product_below_rop'] > 0 ? 'border-red-200' : 'border-gray-100' }}">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                    </svg>
                </div>
                @if($summary['product_below_rop'] > 0)
                    <span class="text-xs font-bold bg-red-100 text-red-600 px-2 py-0.5 rounded-full animate-pulse">
                        {{ $summary['product_below_rop'] }} ⚠
                    </span>
                @endif
            </div>
            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide">Produk</p>
            <p class="text-3xl font-bold text-gray-800 mt-1">{{ number_format($summary['total_product']) }}</p>
            @if($summary['product_below_rop'] > 0)
                <p class="text-xs text-red-500 mt-1 font-medium">{{ $summary['product_below_rop'] }} di bawah ROP</p>
            @else
                <p class="text-xs text-green-500 mt-1">Semua stok aman</p>
            @endif
        </a>

        {{-- Supplier --}}
        <a href="{{ route('admin.suppliers.index') }}"
            class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 hover:shadow-md transition-shadow">
            <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center mb-3">
                <svg class="w-5 h-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide">Supplier</p>
            <p class="text-3xl font-bold text-gray-800 mt-1">{{ number_format($summary['total_supplier']) }}</p>
            <p class="text-xs text-gray-400 mt-1">aktif terdaftar</p>
        </a>

        {{-- Buyer --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <div class="w-10 h-10 rounded-xl bg-orange-50 flex items-center justify-center mb-3">
                <svg class="w-5 h-5 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide">Pembeli</p>
            <p class="text-3xl font-bold text-gray-800 mt-1">{{ number_format($summary['total_buyer']) }}</p>
            <p class="text-xs text-gray-400 mt-1">unik bertransaksi</p>
        </div>
    </div>

    {{-- ══ 2. MIDDLE ROW (Stok Kritis & Status PO) ══ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- PO Status (1 Kolom Kiri) --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <div class="w-10 h-10 rounded-xl bg-yellow-50 flex items-center justify-center mb-4">
                <svg class="w-5 h-5 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">Status PO Terkini</h3>
            
            <div class="space-y-3">
                @foreach([
                    'pending'  => ['label'=>'Menunggu (Pending)', 'color'=>'text-yellow-600', 'bg'=>'bg-yellow-50/50'],
                    'approved' => ['label'=>'PO Disetujui', 'color'=>'text-blue-600', 'bg'=>'bg-blue-50/50'],
                    'received' => ['label'=>'Selesai / Diterima', 'color'=>'text-green-600', 'bg'=>'bg-green-50/50']
                ] as $s => $cfg)
                    <div class="flex items-center justify-between p-3 rounded-lg {{ $cfg['bg'] }} border border-transparent">
                        <span class="text-sm font-medium text-gray-700">{{ $cfg['label'] }}</span>
                        <span class="text-xl font-bold {{ $cfg['color'] }}">{{ $poSummary[$s] ?? 0 }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ROP Warnings (2 Kolom Kanan) --}}
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden flex flex-col">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-700">⚠ Peringatan Stok Kritis (Di Bawah ROP)</h3>
                @php $totalRop = $materialsLow->count() + $productsLow->count(); @endphp
                @if($totalRop > 0)
                    <span class="text-xs font-bold bg-red-100 text-red-600 px-3 py-1 rounded-full">{{ $totalRop }} Item Butuh Perhatian</span>
                @else
                    <span class="text-xs text-green-500 font-semibold bg-green-50 px-3 py-1 rounded-full border border-green-100">Semua Stok Aman ✓</span>
                @endif
            </div>
            
            <div class="flex-1 overflow-y-auto" style="max-height: 250px;">
                @if($materialsLow->isEmpty() && $productsLow->isEmpty())
                    <div class="flex flex-col items-center justify-center h-full text-sm text-gray-400 p-8">
                        <span class="text-4xl block mb-3">✅</span>
                        <p>Kondisi gudang prima.</p>
                        <p>Semua stok bahan dan produk berada di atas batas ROP.</p>
                    </div>
                @else
                    <div class="divide-y divide-gray-50">
                        {{-- List Material Low --}}
                        @foreach($materialsLow as $m)
                            <div class="px-5 py-3.5 hover:bg-gray-50 transition-colors">
                                <div class="flex items-center justify-between mb-2">
                                    <p class="text-sm font-bold text-gray-800">{{ $m->nama_bahan }}</p>
                                    <span class="text-[10px] bg-blue-50 text-blue-600 px-2 py-0.5 rounded font-medium uppercase tracking-wider">Material</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="flex-1 bg-gray-100 rounded-full h-2">
                                        @php
                                            $safeStock = ($m->pemakaian_rata_rata * $m->lead_time) + ($m->safety_stock ?? 0);
                                            $pct = $safeStock > 0 ? min(($m->stok / $safeStock) * 100, 100) : 0;
                                        @endphp
                                        <div class="rop-bar h-2 rounded-full bg-red-400" style="width: {{ $pct }}%"></div>
                                    </div>
                                    <span class="text-xs font-bold text-red-500 whitespace-nowrap">{{ $m->stok }} Tersisa</span>
                                </div>
                            </div>
                        @endforeach

                        {{-- List Product Low --}}
                        @foreach($productsLow as $p)
                            <div class="px-5 py-3.5 hover:bg-gray-50 transition-colors">
                                <div class="flex items-center justify-between mb-2">
                                    <p class="text-sm font-bold text-gray-800">{{ $p->nama }}</p>
                                    <span class="text-[10px] px-2 py-0.5 rounded font-medium uppercase tracking-wider {{ $p->type === 'pakan' ? 'bg-green-50 text-green-600' : 'bg-purple-50 text-purple-600' }}">
                                        Produk {{ ucfirst($p->type) }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="flex-1 bg-gray-100 rounded-full h-2">
                                        @php $pct = $p->rop > 0 ? min(($p->stok / $p->rop) * 100, 100) : 0; @endphp
                                        <div class="rop-bar h-2 rounded-full bg-orange-400" style="width: {{ $pct }}%"></div>
                                    </div>
                                    <span class="text-xs font-bold text-orange-500 whitespace-nowrap">{{ $p->stok }} / {{ $p->rop }} (Batas)</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ══ 3. BOTTOM ROW (Grafik, Distribusi & Aktivitas) ══ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Kolom Kiri: Chart & Distribusi --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Movement Chart --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-gray-700">Pergerakan Stok Gudang (7 Hari Terakhir)</h3>
                    <a href="{{ route('admin.report.stock') }}" class="text-xs text-orange-500 font-semibold hover:underline no-print">Lihat Laporan Detail &rarr;</a>
                </div>
                <div class="h-60">
                    <canvas id="movementChart"></canvas>
                </div>
            </div>

            {{-- Row Distribusi (Dibagi 2 Sebelahan) --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Supplier Distribution --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">Distribusi Pembelian (PO)</h3>
                    @if($supplierDistribution->isEmpty())
                        <p class="text-sm text-gray-400 text-center py-4">Belum ada data PO.</p>
                    @else
                        @php $maxPo = $supplierDistribution->max('total'); @endphp
                        <div class="space-y-3">
                            @foreach($supplierDistribution->sortByDesc('total')->take(4) as $sd)
                                <div>
                                    <div class="flex justify-between text-xs mb-1.5">
                                        <span class="font-medium text-gray-700 truncate max-w-[150px]">{{ $sd->supplier?->nama_supplier ?? 'Supplier #'.$sd->supplier_id }}</span>
                                        <span class="font-bold text-gray-800">{{ $sd->total }} PO</span>
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-full h-1.5">
                                        <div class="h-1.5 rounded-full bg-blue-400 transition-all duration-700" style="width: {{ $maxPo > 0 ? ($sd->total / $maxPo * 100) : 0 }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Buyer Distribution --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">Top Pembeli Produk</h3>
                    @if($buyerDistribution->isEmpty())
                        <p class="text-sm text-gray-400 text-center py-4">Belum ada data transaksi.</p>
                    @else
                        @php $maxBuy = $buyerDistribution->max('total'); @endphp
                        <div class="space-y-3">
                            @foreach($buyerDistribution->take(4) as $bd)
                                <div>
                                    <div class="flex justify-between text-xs mb-1.5">
                                        <span class="font-medium text-gray-700 truncate max-w-[150px]">{{ $bd->user?->name ?? 'User #'.$bd->user_id }}</span>
                                        <span class="font-bold text-orange-500">{{ $bd->total }} Order</span>
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-full h-1.5">
                                        <div class="h-1.5 rounded-full bg-orange-400 transition-all duration-700" style="width: {{ $maxBuy > 0 ? ($bd->total / $maxBuy * 100) : 0 }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Kolom Kanan: Recent Activity --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden flex flex-col h-full">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/30">
                <h3 class="text-sm font-semibold text-gray-700">Log Aktivitas Terbaru</h3>
                <a href="{{ route('admin.warehouse.activity-log') }}" class="text-xs text-gray-500 hover:text-orange-500 transition-colors">
                    Lihat Semua
                </a>
            </div>
            
            <div class="flex-1 overflow-y-auto">
                @if($recentActivities->isEmpty())
                    <div class="p-8 text-center text-sm text-gray-400">Belum ada aktivitas tercatat.</div>
                @else
                    <div class="divide-y divide-gray-50">
                        @foreach($recentActivities as $log)
                            @php
                                $iconCfg = match(true) {
                                    str_contains($log->type, 'po_')         => ['bg'=>'bg-blue-50',  'text'=>'text-blue-500',   'icon'=>'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                                    str_contains($log->type, 'production_') => ['bg'=>'bg-purple-50','text'=>'text-purple-500', 'icon'=>'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z'],
                                    str_contains($log->type, 'order_')      => ['bg'=>'bg-green-50', 'text'=>'text-green-500',  'icon'=>'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z'],
                                    str_contains($log->type, 'disposal_')   => ['bg'=>'bg-red-50',   'text'=>'text-red-400',   'icon'=>'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16'],
                                    str_contains($log->type, 'qc_')         => ['bg'=>'bg-yellow-50','text'=>'text-yellow-500','icon'=>'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                                    default                                 => ['bg'=>'bg-gray-50',  'text'=>'text-gray-400',  'icon'=>'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                                };
                            @endphp
                            <div class="flex items-start gap-4 px-5 py-4 hover:bg-gray-50 transition-colors">
                                <div class="w-9 h-9 rounded-full {{ $iconCfg['bg'] }} flex-shrink-0 flex items-center justify-center">
                                    <svg class="w-4 h-4 {{ $iconCfg['text'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $iconCfg['icon'] }}"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0 pt-0.5">
                                    <p class="text-sm font-medium text-gray-800 leading-snug line-clamp-2">{{ $log->description }}</p>
                                    <div class="flex items-center gap-2 mt-1.5">
                                        <span class="text-[11px] text-gray-400">{{ $log->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
@if(isset($movementChart))
    @php
        $days = collect(range(6, 0))->map(fn($i) => now()->subDays($i)->toDateString());
        $chartLabels  = $days->map(fn($d) => \Carbon\Carbon::parse($d)->format('d M'));
        $chartMasuk   = $days->map(fn($d) => $movementChart->where('tgl', $d)->first()?->masuk  ?? 0);
        $chartKeluar  = $days->map(fn($d) => $movementChart->where('tgl', $d)->first()?->keluar ?? 0);
    @endphp
    new Chart(document.getElementById('movementChart'), {
        type: 'bar',
        data: {
            labels: {!! $chartLabels->toJson() !!},
            datasets: [
                {
                    label: 'Stok Masuk',
                    data: {!! $chartMasuk->toJson() !!},
                    backgroundColor: 'rgba(34,197,94,0.8)',
                    borderRadius: 4,
                    barPercentage: 0.6,
                    categoryPercentage: 0.8
                },
                {
                    label: 'Stok Keluar',
                    data: {!! $chartKeluar->toJson() !!},
                    backgroundColor: 'rgba(239,68,68,0.8)',
                    borderRadius: 4,
                    barPercentage: 0.6,
                    categoryPercentage: 0.8
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: { 
                legend: { 
                    position: 'top', 
                    align: 'end',
                    labels: { 
                        boxWidth: 12, 
                        usePointStyle: true, 
                        font: { size: 11, family: "'Inter', sans-serif" } 
                    } 
                },
                tooltip: {
                    backgroundColor: 'rgba(17, 24, 39, 0.9)',
                    titleFont: { size: 13, family: "'Inter', sans-serif" },
                    bodyFont: { size: 12, family: "'Inter', sans-serif" },
                    padding: 10,
                    cornerRadius: 8,
                    displayColors: true
                }
            },
            scales: {
                y: { 
                    beginAtZero: true, 
                    grid: { color: '#f3f4f6', drawBorder: false }, 
                    border: { dash: [4, 4] },
                    ticks: { font: { size: 11, family: "'Inter', sans-serif" }, color: '#9ca3af', padding: 10 } 
                },
                x: { 
                    grid: { display: false, drawBorder: false }, 
                    ticks: { font: { size: 11, family: "'Inter', sans-serif" }, color: '#6b7280', padding: 8 } 
                }
            }
        }
    });
@endif
</script>
@endpush

</x-admin-app-layout>