<x-owner-app-layout>

@push('styles')
<style>
    @media print {
        .no-print { display: none !important; }
        .print-break { page-break-before: always; }
        body { background: white !important; }
        .shadow-sm { box-shadow: none !important; }
    }
</style>
@endpush

<div class="p-6 max-w-7xl mx-auto">

    @include('owner.report._nav', ['active' => 'stock'])

    {{-- ── Filter ── --}}
    <form method="GET" action="{{ route('owner.report.stock') }}"
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
            <div class="min-w-[130px]">
                <label class="block text-xs font-semibold text-gray-500 mb-1.5">Tipe</label>
                <select name="type" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-300">
                    <option value="">Semua</option>
                    <option value="in"  {{ request('type')==='in'  ? 'selected':'' }}>Masuk</option>
                    <option value="out" {{ request('type')==='out' ? 'selected':'' }}>Keluar</option>
                </select>
            </div>
            <div class="min-w-[150px]">
                <label class="block text-xs font-semibold text-gray-500 mb-1.5">Sumber</label>
                <select name="source" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-300">
                    <option value="">Semua</option>
                    @foreach($sources as $src)
                        <option value="{{ $src }}" {{ request('source')===$src?'selected':'' }}>{{ ucfirst(str_replace('_',' ',$src)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold rounded-lg transition-colors">Terapkan</button>
                <a href="{{ route('owner.report.stock') }}" class="px-3 py-2 text-sm text-gray-400 hover:text-gray-700 rounded-lg hover:bg-gray-100 transition-colors">Reset</a>
            </div>
        </div>
    </form>

    {{-- ── Summary Cards ── --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide">Total Transaksi</p>
            <p class="text-3xl font-bold text-gray-800 mt-1">{{ number_format($summary->total_transaksi ?? 0) }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ \Carbon\Carbon::parse($startDate)->format('d M') }} – {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
        </div>
        <div class="bg-white rounded-xl border border-green-100 shadow-sm p-5">
            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide">Stok Masuk</p>
            <p class="text-3xl font-bold text-green-600 mt-1">{{ number_format($summary->total_masuk ?? 0) }}</p>
            <p class="text-xs text-gray-400 mt-1">unit</p>
        </div>
        <div class="bg-white rounded-xl border border-red-100 shadow-sm p-5">
            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide">Stok Keluar</p>
            <p class="text-3xl font-bold text-red-500 mt-1">{{ number_format($summary->total_keluar ?? 0) }}</p>
            <p class="text-xs text-gray-400 mt-1">unit</p>
        </div>
    </div>

    {{-- ── Chart ── --}}
    @if($chartData->isNotEmpty())
    <div class="no-print bg-white rounded-xl border border-gray-200 shadow-sm p-5 mb-6">
        <h3 class="text-sm font-semibold text-gray-700 mb-4">Grafik Pergerakan Stok</h3>
        <div class="h-56">
            <canvas id="stockChart"></canvas>
        </div>
    </div>
    @endif

    {{-- ── Tabel ── --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-700">Detail Pergerakan Stok</h3>
            <span class="text-xs text-gray-400">{{ $movements->total() }} entri</span>
        </div>

        @if($movements->isEmpty())
            <div class="py-16 text-center text-sm text-gray-400">Tidak ada data untuk periode ini.</div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Item</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Tipe</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Qty</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Sumber</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($movements as $m)
                            <tr class="hover:bg-gray-50/60 transition-colors">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <p class="text-xs font-medium text-gray-700">{{ \Carbon\Carbon::parse($m->movement_date)->format('d M Y') }}</p>
                                    <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($m->movement_date)->format('H:i') }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    @if($m->stockable)
                                        <p class="font-medium text-gray-800">{{ $m->stockable->nama ?? $m->stockable->name ?? '-' }}</p>
                                        <p class="text-xs text-gray-400">{{ class_basename($m->stockable_type) }}</p>
                                    @else
                                        <span class="text-xs text-gray-400 italic">Item dihapus</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold
                                        {{ $m->type === 'in' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                                        {{ $m->type === 'in' ? '↑ Masuk' : '↓ Keluar' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="font-bold {{ $m->type === 'in' ? 'text-green-600' : 'text-red-500' }}">
                                        {{ $m->type === 'in' ? '+' : '-' }}{{ number_format($m->quantity) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-xs px-2 py-0.5 bg-gray-100 text-gray-600 rounded font-medium">
                                        {{ ucfirst(str_replace('_', ' ', $m->source ?? '-')) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-500 max-w-[180px] truncate">{{ $m->catatan ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($movements->hasPages())
                <div class="px-5 py-4 border-t border-gray-100 no-print">{{ $movements->links() }}</div>
            @endif
        @endif
    </div>

</div>

@push('scripts')
@if($chartData->isNotEmpty())
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('stockChart'), {
    type: 'bar',
    data: {
        labels: {!! $chartData->pluck('tgl')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d M'))->toJson() !!},
        datasets: [
            {
                label: 'Masuk',
                data: {!! $chartData->pluck('masuk')->toJson() !!},
                backgroundColor: 'rgba(34,197,94,0.7)',
                borderRadius: 4,
            },
            {
                label: 'Keluar',
                data: {!! $chartData->pluck('keluar')->toJson() !!},
                backgroundColor: 'rgba(239,68,68,0.7)',
                borderRadius: 4,
            }
        ]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { position: 'top' } },
        scales: { y: { beginAtZero: true, grid: { color: '#f3f4f6' } }, x: { grid: { display: false } } }
    }
});
</script>
@endif
@endpush

</x-owner-app-layout>