<x-owner-app-layout>

@push('styles')
<style>
    @media print {
        .no-print { display: none !important; }
        body { background: white !important; }
        .shadow-sm { box-shadow: none !important; }
    }
    .month-row:hover { background: #fafafa; }
</style>
@endpush

<div class="p-6 max-w-7xl mx-auto">

    @include('owner.report._nav', ['active' => 'monthly'])

    {{-- ── Year Filter ── --}}
    <form method="GET" action="{{ route('owner.report.monthly') }}"
        class="no-print flex items-center gap-3 mb-6">
        <label class="text-sm font-semibold text-gray-600">Tahun:</label>
        <select name="year"
            onchange="this.form.submit()"
            class="border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-orange-300 shadow-sm">
            @foreach($availableYears as $y)
                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endforeach
        </select>
        <span class="text-sm text-gray-400">Menampilkan data tahun {{ $year }}</span>
    </form>

    {{-- ── Annual Totals ── --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-green-100 shadow-sm p-4">
            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide">Stok Masuk</p>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ number_format($totals['stok_masuk']) }}</p>
            <p class="text-xs text-gray-400 mt-0.5">unit setahun</p>
        </div>
        <div class="bg-white rounded-xl border border-red-100 shadow-sm p-4">
            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide">Stok Keluar</p>
            <p class="text-2xl font-bold text-red-500 mt-1">{{ number_format($totals['stok_keluar']) }}</p>
            <p class="text-xs text-gray-400 mt-0.5">unit setahun</p>
        </div>
        <div class="bg-white rounded-xl border border-blue-100 shadow-sm p-4">
            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide">Total Produksi</p>
            <p class="text-2xl font-bold text-blue-600 mt-1">{{ number_format($totals['total_produksi']) }}</p>
            <p class="text-xs text-gray-400 mt-0.5">unit setahun</p>
        </div>
        <div class="bg-white rounded-xl border border-purple-100 shadow-sm p-4">
            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide">Total Batch</p>
            <p class="text-2xl font-bold text-purple-600 mt-1">{{ number_format($totals['total_batch']) }}</p>
            <p class="text-xs text-gray-400 mt-0.5">batch setahun</p>
        </div>
        <div class="bg-white rounded-xl border border-orange-100 shadow-sm p-4">
            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide">Total Disposal</p>
            <p class="text-2xl font-bold text-orange-500 mt-1">{{ number_format($totals['total_disposal']) }}</p>
            <p class="text-xs text-gray-400 mt-0.5">kejadian setahun</p>
        </div>
    </div>

    {{-- ── Chart Tahunan ── --}}
    <div class="no-print grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Pergerakan Stok per Bulan</h3>
            <div class="h-56">
                <canvas id="stockMonthChart"></canvas>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Produksi & Disposal per Bulan</h3>
            <div class="h-56">
                <canvas id="prodMonthChart"></canvas>
            </div>
        </div>
    </div>

    {{-- ── Tabel Rekap Bulanan ── --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-700">Rekap per Bulan — {{ $year }}</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Bulan</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-green-600 uppercase tracking-wide">Stok Masuk</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-red-500 uppercase tracking-wide">Stok Keluar</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Net Stok</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-blue-600 uppercase tracking-wide">Produksi (unit)</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-purple-600 uppercase tracking-wide">Batch</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-orange-500 uppercase tracking-wide">Disposal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($months as $m)
                        @php
                            $isCurrentMonth = $m['bulan'] == now()->month && $year == now()->year;
                            $net = $m['stok_masuk'] - $m['stok_keluar'];
                            $hasData = $m['stok_masuk'] > 0 || $m['stok_keluar'] > 0 || $m['total_produksi'] > 0 || $m['total_disposal'] > 0;
                        @endphp
                        <tr class="month-row transition-colors {{ $isCurrentMonth ? 'bg-orange-50/40' : '' }}">
                            <td class="px-4 py-3.5">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-gray-800">{{ $m['nama'] }}</span>
                                    @if($isCurrentMonth)
                                        <span class="text-xs bg-orange-100 text-orange-600 px-1.5 py-0.5 rounded-full font-medium">Bulan ini</span>
                                    @endif
                                    @if(!$hasData)
                                        <span class="text-xs text-gray-300 italic">—</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                <span class="{{ $m['stok_masuk'] > 0 ? 'font-bold text-green-600' : 'text-gray-300' }}">
                                    {{ $m['stok_masuk'] > 0 ? '+' . number_format($m['stok_masuk']) : '—' }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                <span class="{{ $m['stok_keluar'] > 0 ? 'font-bold text-red-500' : 'text-gray-300' }}">
                                    {{ $m['stok_keluar'] > 0 ? '-' . number_format($m['stok_keluar']) : '—' }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                @if($m['stok_masuk'] > 0 || $m['stok_keluar'] > 0)
                                    <span class="font-semibold {{ $net >= 0 ? 'text-gray-700' : 'text-red-600' }}">
                                        {{ ($net >= 0 ? '+' : '') . number_format($net) }}
                                    </span>
                                @else
                                    <span class="text-gray-300">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                <span class="{{ $m['total_produksi'] > 0 ? 'font-bold text-blue-600' : 'text-gray-300' }}">
                                    {{ $m['total_produksi'] > 0 ? number_format($m['total_produksi']) : '—' }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-center text-gray-600">
                                {{ $m['total_batch'] > 0 ? $m['total_batch'] : '—' }}
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                <span class="{{ $m['total_disposal'] > 0 ? 'font-bold text-orange-500' : 'text-gray-300' }}">
                                    {{ $m['total_disposal'] > 0 ? $m['total_disposal'] . 'x' : '—' }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                {{-- Total Row --}}
                <tfoot>
                    <tr class="bg-gray-50 border-t-2 border-gray-200">
                        <td class="px-4 py-3.5 font-bold text-gray-700 text-sm">TOTAL {{ $year }}</td>
                        <td class="px-4 py-3.5 text-center font-bold text-green-600">+{{ number_format($totals['stok_masuk']) }}</td>
                        <td class="px-4 py-3.5 text-center font-bold text-red-500">-{{ number_format($totals['stok_keluar']) }}</td>
                        <td class="px-4 py-3.5 text-center font-bold text-gray-700">
                            @php $netTotal = $totals['stok_masuk'] - $totals['stok_keluar']; @endphp
                            <span class="{{ $netTotal >= 0 ? 'text-gray-800' : 'text-red-600' }}">
                                {{ ($netTotal >= 0 ? '+' : '') . number_format($netTotal) }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5 text-center font-bold text-blue-600">{{ number_format($totals['total_produksi']) }}</td>
                        <td class="px-4 py-3.5 text-center font-bold text-gray-700">{{ number_format($totals['total_batch']) }}</td>
                        <td class="px-4 py-3.5 text-center font-bold text-orange-500">{{ number_format($totals['total_disposal']) }}x</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const labels = {!! $months->pluck('nama')->toJson() !!};

new Chart(document.getElementById('stockMonthChart'), {
    type: 'bar',
    data: {
        labels,
        datasets: [
            {
                label: 'Masuk',
                data: {!! $months->pluck('stok_masuk')->toJson() !!},
                backgroundColor: 'rgba(34,197,94,0.7)',
                borderRadius: 4,
            },
            {
                label: 'Keluar',
                data: {!! $months->pluck('stok_keluar')->toJson() !!},
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

new Chart(document.getElementById('prodMonthChart'), {
    type: 'bar',
    data: {
        labels,
        datasets: [
            {
                label: 'Produksi (unit)',
                data: {!! $months->pluck('total_produksi')->toJson() !!},
                backgroundColor: 'rgba(59,130,246,0.7)',
                borderRadius: 4,
            },
            {
                label: 'Disposal',
                data: {!! $months->pluck('total_disposal')->toJson() !!},
                backgroundColor: 'rgba(249,115,22,0.7)',
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
@endpush

</x-owner-app-layout>