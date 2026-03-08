{{--
    Partial: Tab Batch & Riwayat
    Vars: $product, $batches, $movements, $belowRop
--}}
<div class="space-y-6">

    {{-- ── Daftar Batch ── --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-700">Daftar Batch Stok</h3>
            <div class="flex items-center gap-3 text-xs text-gray-400">
                <span>
                    <span class="font-semibold text-gray-700">{{ $batches->where('qty', '>', 0)->count() }}</span>
                    aktif dari {{ $batches->count() }} batch
                </span>
                @php
                    $expiredCount  = $batches->filter(fn($b) => $b->qty > 0 && $b->expired_date && \Carbon\Carbon::parse($b->expired_date)->isPast())->count();
$expiringSoon = $batches->filter(fn($b) => $b->qty > 0 && $b->expired_date
    && \Carbon\Carbon::parse($b->expired_date)->isFuture()
    && round(\Carbon\Carbon::now()->diffInDays($b->expired_date)) <= 30)->count(); // Tambahkan round()
                @endphp
                @if($expiredCount > 0)
                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                        {{ $expiredCount }} expired
                    </span>
                @endif
                @if($expiringSoon > 0)
                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">
                        {{ $expiringSoon }} hampir exp
                    </span>
                @endif
            </div>
        </div>

        @if($batches->isEmpty())
            <div class="py-14 text-center text-gray-400">
                <svg class="w-10 h-10 mx-auto mb-2 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                <p class="text-sm">Belum ada batch stok.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-5 py-3 text-left   text-xs font-semibold text-gray-500 uppercase tracking-wide">#</th>
                            <th class="px-5 py-3 text-right  text-xs font-semibold text-gray-500 uppercase tracking-wide">Qty</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Tgl Terima</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Expired</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Sumber</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($batches as $i => $batch)
                            @php
                                $bEmpty = $batch->qty <= 0;
                                $bExp   = $batch->expired_date && \Carbon\Carbon::parse($batch->expired_date)->isPast();
                                $bSoon  = !$bExp && $batch->expired_date
                                    && \Carbon\Carbon::parse($batch->expired_date)->isFuture()
                                    && \Carbon\Carbon::now()->diffInDays($batch->expired_date) <= 30;
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors {{ $bExp ? 'bg-red-50/30' : '' }} {{ $bEmpty ? 'opacity-50' : '' }}">
                                <td class="px-5 py-3.5 text-xs text-gray-400">{{ $i + 1 }}</td>

                                <td class="px-5 py-3.5 text-right font-bold text-gray-800">
                                    {{ number_format($batch->qty) }}
                                </td>

                                <td class="px-5 py-3.5 text-center text-xs text-gray-500">
                                    {{ $batch->received_date ? \Carbon\Carbon::parse($batch->received_date)->format('d M Y') : '—' }}
                                </td>

                                <td class="px-5 py-3.5 text-center">
                                    @if($batch->expired_date)
                                        <span class="text-xs font-medium {{ $bExp ? 'text-red-600' : ($bSoon ? 'text-yellow-600' : 'text-gray-600') }}">
                                            {{ \Carbon\Carbon::parse($batch->expired_date)->format('d M Y') }}
                                        </span>
                                        @if($bSoon)
                                            <span class="block text-xs text-yellow-500">
                                                {{ round(\Carbon\Carbon::now()->diffInDays($batch->expired_date)) }} hari lagi
                                            </span>
                                        @endif
                                    @else
                                        <span class="text-xs text-gray-300">—</span>
                                    @endif
                                </td>

                                <td class="px-5 py-3.5 text-center">
                                    @php
                                        $srcMap = [
                                            'production'        => ['bg-orange-100 text-orange-700', 'Produksi'],
                                            'manual_adjustment' => ['bg-gray-100 text-gray-500',     'Adjustment'],
                                            'purchase'          => ['bg-purple-100 text-purple-700', 'PO'],
                                        ];
                                        [$sc, $sl] = $srcMap[$batch->source] ?? ['bg-gray-100 text-gray-500', ucfirst($batch->source ?? '—')];
                                    @endphp
                                    <span class="inline-flex px-2 py-0.5 rounded-md text-xs font-medium {{ $sc }}">{{ $sl }}</span>
                                </td>

                                <td class="px-5 py-3.5 text-center">
                                    @if($bEmpty)
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-400">Habis</span>
                                    @elseif($bExp)
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">Expired</span>
                                    @elseif($bSoon)
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">Hampir Exp</span>
                                    @else
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Aktif</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50 border-t border-gray-200">
                        <tr>
                            <td colspan="2" class="px-5 py-3 text-right text-xs font-semibold text-gray-600">
                                Total:
                                <span class="ml-1 {{ $belowRop ? 'text-red-600' : 'text-green-700' }}">
                                    {{ number_format($batches->sum('qty')) }} unit
                                </span>
                            </td>
                            <td colspan="4"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </div>

    {{-- ── Riwayat Movements ── --}}
    @if($movements->isNotEmpty())
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-700">Riwayat Pergerakan Stok</h3>
                <span class="text-xs text-gray-400">{{ $movements->count() }} transaksi terakhir</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-5 py-3 text-left   text-xs font-semibold text-gray-500 uppercase tracking-wide">Tanggal</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Tipe</th>
                            <th class="px-5 py-3 text-right  text-xs font-semibold text-gray-500 uppercase tracking-wide">Qty</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Sumber</th>
                            <th class="px-5 py-3 text-left   text-xs font-semibold text-gray-500 uppercase tracking-wide">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($movements as $mov)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-3.5 text-xs text-gray-500">
                                    {{ \Carbon\Carbon::parse($mov->movement_date)->format('d M Y, H:i') }}
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    @if($mov->type === 'in')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/></svg>
                                            Masuk
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                                            Keluar
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-right font-bold {{ $mov->type === 'in' ? 'text-green-700' : 'text-red-600' }}">
                                    {{ $mov->type === 'in' ? '+' : '-' }}{{ number_format($mov->quantity) }}
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    @php
                                        $sMap = [
                                            'production'         => ['bg-orange-100 text-orange-700', 'Produksi'],
                                            'jual'               => ['bg-blue-100 text-blue-700',     'Penjualan'],
                                            'pemakaian_internal' => ['bg-purple-100 text-purple-700', 'Internal'],
                                            'manual_adjustment'  => ['bg-gray-100 text-gray-600',     'Adjustment'],
                                            'PO'                 => ['bg-indigo-100 text-indigo-700', 'Purchase Order'],
                                        ];
                                        [$sc, $sl] = $sMap[$mov->source] ?? ['bg-gray-100 text-gray-500', ucfirst($mov->source ?? '—')];
                                    @endphp
                                    <span class="inline-flex px-2 py-0.5 rounded-md text-xs font-medium {{ $sc }}">{{ $sl }}</span>
                                </td>
                                <td class="px-5 py-3.5 text-xs text-gray-400">{{ $mov->catatan ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm px-5 py-12 text-center text-gray-400">
            <p class="text-sm">Belum ada riwayat pergerakan stok.</p>
        </div>
    @endif

</div>