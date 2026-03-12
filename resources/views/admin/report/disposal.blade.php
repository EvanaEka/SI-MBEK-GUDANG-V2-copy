<x-admin-app-layout>

@push('styles')
<style>
    @media print {
        .no-print { display: none !important; }
        body { background: white !important; }
        .shadow-sm { box-shadow: none !important; }
    }
</style>
@endpush

<div class="p-6 max-w-7xl mx-auto">

    @include('admin.report._nav', ['active' => 'disposal'])

    {{-- ── Filter ── --}}
    <form method="GET" action="{{ route('admin.report.disposal') }}"
        class="no-print bg-white rounded-xl border border-gray-200 shadow-sm p-4 mb-6">
        <div class="flex flex-wrap items-end gap-3">
            <div class="min-w-[130px]">
                <label class="block text-xs font-semibold text-gray-500 mb-1.5">Dari</label>
                <input type="date" name="start_date" value="{{ $startDate }}"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-300">
            </div>
            <div class="min-w-[130px]">
                <label class="block text-xs font-semibold text-gray-500 mb-1.5">Sampai</label>
                <input type="date" name="end_date" value="{{ $endDate }}"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-300">
            </div>
            <div class="min-w-[160px]">
                <label class="block text-xs font-semibold text-gray-500 mb-1.5">Alasan</label>
                <select name="reason" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-300">
                    <option value="">Semua</option>
                    @foreach($reasons as $r)
                        <option value="{{ $r }}" {{ request('reason')===$r?'selected':'' }}>{{ ucfirst(str_replace('_',' ',$r)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold rounded-lg transition-colors">Terapkan</button>
                <a href="{{ route('admin.report.disposal') }}" class="px-3 py-2 text-sm text-gray-400 hover:text-gray-700 rounded-lg hover:bg-gray-100 transition-colors">Reset</a>
            </div>
        </div>
    </form>

    {{-- ── Summary ── --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide">Total Disposal</p>
            <p class="text-3xl font-bold text-gray-800 mt-1">{{ number_format($summary->total_disposal ?? 0) }}</p>
            <p class="text-xs text-gray-400 mt-1">kejadian</p>
        </div>
        <div class="bg-white rounded-xl border border-red-100 shadow-sm p-5">
            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide">Total Qty Dibuang</p>
            <p class="text-3xl font-bold text-red-500 mt-1">{{ number_format($summary->total_qty ?? 0) }}</p>
            <p class="text-xs text-gray-400 mt-1">unit</p>
        </div>
        <div class="bg-white rounded-xl border border-orange-100 shadow-sm p-5">
            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide">Breakdown Alasan</p>
            <div class="mt-2 space-y-1">
                @forelse($reasonBreakdown->take(3) as $rb)
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-600">{{ ucfirst(str_replace('_',' ',$rb->reason)) }}</span>
                        <span class="font-bold text-gray-800">{{ $rb->jumlah }}x</span>
                    </div>
                @empty
                    <p class="text-xs text-gray-400">—</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        {{-- Chart Trend ── --}}
        @if($chartData->isNotEmpty())
        <div class="no-print lg:col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Trend Disposal Harian</h3>
            <div class="h-52">
                <canvas id="disposalTrendChart"></canvas>
            </div>
        </div>
        @endif

        {{-- Donut Alasan ── --}}
        @if($reasonBreakdown->isNotEmpty())
        <div class="no-print bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Distribusi Alasan</h3>
            <div class="h-52">
                <canvas id="reasonChart"></canvas>
            </div>
        </div>
        @endif
    </div>

    {{-- ── Tabel ── --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-700">Detail Disposal</h3>
            <span class="text-xs text-gray-400">{{ $disposals->total() }} entri</span>
        </div>

        @if($disposals->isEmpty())
            <div class="py-16 text-center text-sm text-gray-400">Tidak ada data untuk periode ini.</div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Item</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Qty</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Alasan</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Catatan</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Oleh</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($disposals as $d)
                            <tr class="hover:bg-gray-50/60 transition-colors">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <p class="text-xs font-medium text-gray-700">{{ $d->created_at->format('d M Y') }}</p>
                                    <p class="text-xs text-gray-400">{{ $d->created_at->format('H:i') }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    @if($d->disposable)
                                        <p class="font-medium text-gray-800">
                                            {{ $d->disposable->nama ?? $d->disposable->name ?? '#' . $d->disposable_id }}
                                        </p>
                                        <p class="text-xs text-gray-400">{{ class_basename($d->disposable_type) }}</p>
                                    @else
                                        <span class="text-xs text-gray-400 italic">Item dihapus</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="font-bold text-red-500">{{ number_format($d->quantity) }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-xs px-2 py-0.5 bg-red-50 text-red-600 rounded font-medium">
                                        {{ ucfirst(str_replace('_', ' ', $d->reason ?? '-')) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-500 max-w-[200px] truncate">{{ $d->notes ?? '—' }}</td>
                                <td class="px-4 py-3 text-xs text-gray-600">{{ $d->admin?->name ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($disposals->hasPages())
                <div class="px-5 py-4 border-t border-gray-100 no-print">{{ $disposals->links() }}</div>
            @endif
        @endif
    </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@if($chartData->isNotEmpty())
<script>
new Chart(document.getElementById('disposalTrendChart'), {
    type: 'bar',
    data: {
        labels: {!! $chartData->pluck('tgl')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d M'))->toJson() !!},
        datasets: [{
            label: 'Qty Disposal',
            data: {!! $chartData->pluck('qty')->toJson() !!},
            backgroundColor: 'rgba(239,68,68,0.7)',
            borderRadius: 4,
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, grid: { color: '#f3f4f6' } }, x: { grid: { display: false } } }
    }
});
</script>
@endif
@if($reasonBreakdown->isNotEmpty())
<script>
new Chart(document.getElementById('reasonChart'), {
    type: 'doughnut',
    data: {
        labels: {!! $reasonBreakdown->pluck('reason')->map(fn($r) => ucfirst(str_replace('_',' ',$r)))->toJson() !!},
        datasets: [{
            data: {!! $reasonBreakdown->pluck('jumlah')->toJson() !!},
            backgroundColor: ['rgba(239,68,68,0.7)','rgba(249,115,22,0.7)','rgba(234,179,8,0.7)','rgba(168,85,247,0.7)','rgba(59,130,246,0.7)'],
            borderWidth: 2,
            borderColor: '#fff',
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { position: 'bottom', labels: { font: { size: 11 }, padding: 12 } } },
        cutout: '60%',
    }
});
</script>
@endif
@endpush

</x-admin-app-layout>