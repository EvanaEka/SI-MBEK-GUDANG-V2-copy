<x-owner-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Monitoring Perkembangan Domba') }}
            </h2>
        </div>
    </x-slot>

    {{-- Normalisasi daftar bulan --}}
    @php
        $monthList = \App\Models\DombaHistory::where('Domba_id', $domba->id)
            ->pluck('bulan')
            ->map(fn($b) => \Carbon\Carbon::parse(strlen($b) === 7 ? $b.'-01' : $b)->format('Y-m'))
            ->unique()
            ->sort()
            ->values();
    @endphp

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-2xl rounded-2xl p-6">

                {{-- Filter Bulan & Judul --}}
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-8 gap-4 border-b pb-4">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('owner.domba.show', ['domba' => $domba->id]) }}" class="inline-flex items-center px-3 py-1.5 bg-gray-100 border border-gray-300 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200 transition">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Kembali
                        </a>
                        <h3 class="text-lg font-semibold text-gray-700">
                            Monitoring: <span class="text-brand-orange">{{ $domba->name }}</span> <span class="text-gray-400">(ID: {{ $domba->id }})</span>
                        </h3>
                    </div>
                    <form method="get" class="flex items-center gap-2 bg-gray-50 px-3 py-2 rounded-lg border border-gray-200">
                        <label for="bulan_filter" class="text-gray-700 font-medium">Filter Bulan:</label>
                        <select name="bulan" id="bulan_filter" onchange="this.form.submit()" class="border-gray-300 focus:border-indigo-400 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm px-3 pr-8 py-1.5 text-sm">
                            <option value="all" {{ ($selectedMonth ?? 'all') === 'all' ? 'selected' : '' }}>Semua Bulan</option>
                            @foreach($monthList as $bulan)
                                <option value="{{ $bulan }}" {{ ($selectedMonth ?? '') === $bulan ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::createFromFormat('Y-m', $bulan)->format('M Y') }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>

                {{-- Jika tidak ada data --}}
                @if($historis->isEmpty())
                    <div class="text-center py-16 bg-gray-50 rounded-xl shadow-inner">
                        <p class="text-gray-500 text-lg">Data monitoring belum tersedia untuk pilihan ini.</p>
                    </div>
                @else
                    {{-- Grafik Berat --}}
                    <div class="mb-8 p-6 border border-gray-100 rounded-xl bg-gradient-to-br from-orange-50 to-white shadow">
                        <h4 class="text-lg font-bold text-orange-700 mb-4 border-b pb-2">Perkembangan Berat (kg)</h4>
                        <div class="w-full h-72">
                            <canvas id="weightChart"></canvas>
                        </div>
                    </div>

                    {{-- Grafik Harga --}}
                    <div class="mb-8 p-6 border border-gray-100 rounded-xl bg-gradient-to-br from-green-50 to-white shadow">
                        <h4 class="text-lg font-bold text-green-700 mb-4 border-b pb-2">Perkembangan Harga (Rp)</h4>
                        <div class="w-full h-72">
                            <canvas id="priceChart"></canvas>
                        </div>
                    </div>

                    {{-- Tabel Historis --}}
                    <div class="p-6 border border-gray-100 rounded-xl bg-gray-50 shadow">
                        <h4 class="text-lg font-bold text-gray-700 mb-4 border-b pb-2">Data Historis</h4>
                        <div class="overflow-x-auto rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal Update</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Bulan</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Berat (kg)</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Harga (Rp)</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-100">
                                    @foreach($historis as $i => $record)
                                        <tr class="{{ $i % 2 === 0 ? 'bg-gray-50' : '' }} hover:bg-indigo-50 transition">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                                {{ \Carbon\Carbon::parse($record->tanggal ?? $record->updated_at)->format('d M Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                                {{ \Carbon\Carbon::createFromFormat('Y-m', $record->bulan)->format('M Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">{{ number_format($record->berat, 2) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">Rp {{ number_format($record->harga, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>

    {{-- Chart Scripts --}}
    @if(!$historis->isEmpty())
        @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const labels    = @json($labels);
                const beratData = @json($beratData);
                const hargaData = @json($hargaData);

                function makeChart(id, label, data, color, suffix = '') {
                    const ctx = document.getElementById(id).getContext('2d');
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels,
                            datasets: [{
                                label: label,
                                data: data,
                                borderColor: color,
                                backgroundColor: color + '33',
                                tension: 0.3,
                                fill: true,
                                pointRadius: 5,
                                pointHoverRadius: 8,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            const label = context.dataset.label || '';
                                            const value = context.raw;
                                            return label + ': ' + (suffix === 'Rp' ? 'Rp ' : '') + value.toLocaleString('id-ID') + (suffix === 'kg' ? ' kg' : '');
                                        }
                                    }
                                },
                                legend: {
                                    display: true,
                                    position: 'top',
                                    labels: { font: { size: 14 } }
                                }
                            },
                            scales: {
                                x: {
                                    grid: { display: false },
                                    ticks: { font: { size: 12 } }
                                },
                                y: {
                                    beginAtZero: false,
                                    grid: { color: '#e0e0e0' },
                                    ticks: {
                                        callback: function(value) {
                                            return (suffix === 'Rp' ? 'Rp ' : '') + value.toLocaleString('id-ID') + (suffix === 'kg' ? ' kg' : '');
                                        },
                                        font: { size: 12 }
                                    }
                                }
                            }
                        }
                    });
                }

                makeChart('weightChart', 'Berat (kg)', beratData, '#e58609', 'kg');
                makeChart('priceChart', 'Harga (Rp)', hargaData, '#10b981', 'Rp');
            });
        </script>
        @endpush
    @endif
</x-owner-app-layout>
