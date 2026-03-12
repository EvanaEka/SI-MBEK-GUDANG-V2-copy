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

    @include('admin.report._nav', ['active' => 'production'])

    {{-- ── Filter ── --}}
    <form method="GET" action="{{ route('admin.report.production') }}"
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
                <label class="block text-xs font-semibold text-gray-500 mb-1.5">Status</label>
                <select name="status" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-300">
                    <option value="">Semua</option>
                    <option value="diproses" {{ request('status')==='diproses'?'selected':'' }}>Diproses</option>
                    <option value="selesai"  {{ request('status')==='selesai' ?'selected':'' }}>Selesai</option>
                </select>
            </div>
            <div class="min-w-[130px]">
                <label class="block text-xs font-semibold text-gray-500 mb-1.5">QC</label>
                <select name="qc_status" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-300">
                    <option value="">Semua</option>
                    <option value="lulus" {{ request('qc_status')==='lulus'?'selected':'' }}>Lulus</option>
                    <option value="gagal" {{ request('qc_status')==='gagal'?'selected':'' }}>Gagal</option>
                    <option value="pending" {{ request('qc_status')==='pending'?'selected':'' }}>Pending</option>
                </select>
            </div>
            <div class="min-w-[160px]">
                <label class="block text-xs font-semibold text-gray-500 mb-1.5">Produk</label>
                <select name="product_id" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-300">
                    <option value="">Semua Produk</option>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}" {{ request('product_id')==$p->id?'selected':'' }}>{{ $p->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold rounded-lg transition-colors">Terapkan</button>
                <a href="{{ route('admin.report.production') }}" class="px-3 py-2 text-sm text-gray-400 hover:text-gray-700 rounded-lg hover:bg-gray-100 transition-colors">Reset</a>
            </div>
        </div>
    </form>

    {{-- ── Summary Cards ── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide">Total Batch</p>
            <p class="text-3xl font-bold text-gray-800 mt-1">{{ number_format($summary->total_batch ?? 0) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-blue-100 shadow-sm p-5">
            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide">Total Produksi</p>
            <p class="text-3xl font-bold text-blue-600 mt-1">{{ number_format($summary->total_qty ?? 0) }}</p>
            <p class="text-xs text-gray-400 mt-1">unit</p>
        </div>
        <div class="bg-white rounded-xl border border-green-100 shadow-sm p-5">
            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide">Lulus QC</p>
            <p class="text-3xl font-bold text-green-600 mt-1">{{ number_format($summary->lulus_qc ?? 0) }}</p>
            @if(($summary->total_batch ?? 0) > 0)
                <p class="text-xs text-gray-400 mt-1">{{ round(($summary->lulus_qc / $summary->total_batch) * 100) }}% dari total batch</p>
            @endif
        </div>
        <div class="bg-white rounded-xl border border-red-100 shadow-sm p-5">
            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide">Gagal QC</p>
            <p class="text-3xl font-bold text-red-500 mt-1">{{ number_format($summary->gagal_qc ?? 0) }}</p>
        </div>
    </div>

    {{-- ── Chart ── --}}
    @if($chartData->isNotEmpty())
    <div class="no-print bg-white rounded-xl border border-gray-200 shadow-sm p-5 mb-6">
        <h3 class="text-sm font-semibold text-gray-700 mb-4">Grafik Produksi Harian</h3>
        <div class="h-56">
            <canvas id="productionChart"></canvas>
        </div>
    </div>
    @endif

    {{-- ── Tabel ── --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-700">Detail Produksi</h3>
            <span class="text-xs text-gray-400">{{ $productions->total() }} batch</span>
        </div>

        @if($productions->isEmpty())
            <div class="py-16 text-center text-sm text-gray-400">Tidak ada data untuk periode ini.</div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Produk</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Formula</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Qty</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">QC</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">QC %</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($productions as $prod)
                            <tr class="hover:bg-gray-50/60 transition-colors">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <p class="text-xs font-medium text-gray-700">{{ \Carbon\Carbon::parse($prod->production_date)->format('d M Y') }}</p>
                                    @if($prod->expired_date)
                                        <p class="text-xs text-gray-400">Exp: {{ \Carbon\Carbon::parse($prod->expired_date)->format('d M Y') }}</p>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <p class="font-medium text-gray-800">{{ $prod->product?->nama ?? '-' }}</p>
                                    @if($prod->product?->type)
                                        <span class="text-xs px-1.5 py-0.5 rounded font-medium
                                            {{ $prod->product->type === 'pakan' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">
                                            {{ $prod->product->type === 'pakan' ? '🌾 Pakan' : '💊 Obat' }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-gray-600 text-xs">{{ $prod->formula?->nama_formula ?? '-' }}</td>
                                <td class="px-4 py-3 text-center font-bold text-gray-700">{{ number_format($prod->qty_produksi) }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="text-xs px-2 py-0.5 rounded-full font-semibold
                                        {{ $prod->status === 'selesai' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                        {{ ucfirst($prod->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($prod->qc_status)
                                        <span class="text-xs px-2 py-0.5 rounded-full font-semibold
                                            {{ $prod->qc_status === 'lulus' ? 'bg-green-100 text-green-700' : ($prod->qc_status === 'gagal' ? 'bg-red-100 text-red-600' : 'bg-gray-100 text-gray-500') }}">
                                            {{ ucfirst($prod->qc_status) }}
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-300">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($prod->qc_percentage !== null)
                                        <span class="font-medium text-gray-700">{{ $prod->qc_percentage }}%</span>
                                        <div class="w-full bg-gray-100 rounded-full h-1.5 mt-1">
                                            <div class="h-1.5 rounded-full {{ $prod->qc_percentage >= ($prod->qc_threshold ?? 80) ? 'bg-green-500' : 'bg-red-400' }}"
                                                style="width: {{ min($prod->qc_percentage, 100) }}%"></div>
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-300">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($productions->hasPages())
                <div class="px-5 py-4 border-t border-gray-100 no-print">{{ $productions->links() }}</div>
            @endif
        @endif
    </div>

</div>

@push('scripts')
@if($chartData->isNotEmpty())
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('productionChart'), {
    type: 'bar',
    data: {
        labels: {!! $chartData->pluck('tgl')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d M'))->toJson() !!},
        datasets: [
            {
                label: 'Qty Produksi',
                data: {!! $chartData->pluck('qty')->toJson() !!},
                backgroundColor: 'rgba(59,130,246,0.7)',
                borderRadius: 4,
                yAxisID: 'y',
            },
            {
                label: 'Lulus QC',
                data: {!! $chartData->pluck('lulus')->toJson() !!},
                backgroundColor: 'rgba(34,197,94,0.7)',
                borderRadius: 4,
                yAxisID: 'y1',
                type: 'line',
                borderColor: 'rgb(34,197,94)',
                tension: 0.3,
                fill: false,
            }
        ]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { position: 'top' } },
        scales: {
            y:  { beginAtZero: true, grid: { color: '#f3f4f6' }, title: { display: true, text: 'Qty' } },
            y1: { beginAtZero: true, position: 'right', grid: { display: false }, title: { display: true, text: 'Batch Lulus' } },
            x:  { grid: { display: false } }
        }
    }
});
</script>
@endif
@endpush

</x-admin-app-layout>