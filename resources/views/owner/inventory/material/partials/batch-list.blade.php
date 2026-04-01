<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
        <h3 class="text-sm font-semibold text-gray-700">Daftar Batch Stok</h3>
        <div class="flex items-center gap-3">
            <span class="text-xs text-gray-400">
                {{ $batches->where('qty', '>', 0)->count() }} aktif / {{ $batches->count() }} total
            </span>
        </div>
    </div>

    @if($batches->isEmpty())
        <div class="px-5 py-12 text-center text-gray-400">
            <svg class="w-10 h-10 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
            <p class="text-sm">Belum ada batch stok masuk.</p>
            <p class="text-xs mt-1">Batch akan muncul setelah Purchase Order diterima.</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">#</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wide">Qty Tersisa</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wide">Tgl Diterima</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wide">Expired Date</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wide">Harga</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wide">Status</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wide">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($batches as $i => $batch)
                        @php
    // Gunakan startOfDay() dan today() agar perbandingan murni per hari (00:00:00)
    $expDate = $batch->expired_date ? \Carbon\Carbon::parse($batch->expired_date)->startOfDay() : null;
    $bExp  = $expDate && $expDate->isPast();
    $diffDays = $expDate ? today()->diffInDays($expDate, false) : 0;
    
    $bSoon = $expDate && !$bExp && $diffDays <= 30;
    $bEmpty = $batch->qty <= 0;
@endphp
                        <tr class="hover:bg-gray-50 transition-colors {{ $bExp ? 'bg-red-50/30' : '' }} {{ $bEmpty ? 'opacity-60' : '' }}">
                            <td class="px-5 py-3.5 text-xs text-gray-400">{{ $i + 1 }}</td>
                            <td class="px-5 py-3.5 text-right font-bold {{ $bEmpty ? 'text-gray-400' : 'text-gray-800' }}">
                                {{ number_format($batch->qty) }}
                                <span class="text-xs text-gray-400 font-normal ml-1">{{ $material->satuan }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-center text-gray-600">
                                {{ \Carbon\Carbon::parse($batch->received_date)->format('d M Y') }}
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                @if($batch->expired_date)
                                    <span class="text-xs font-medium {{ $bExp ? 'text-red-600' : ($bSoon ? 'text-yellow-600' : 'text-gray-600') }}">
                                        {{ \Carbon\Carbon::parse($batch->expired_date)->format('d M Y') }}
                                    </span>
                                   @if($bExp)
    <span class="block text-xs text-red-500 font-semibold">Kadaluarsa</span>
@elseif($bSoon)
    {{-- Ubah menjadi $diffDays --}}
    <span class="block text-xs text-yellow-500">{{ (int) $diffDays }} hari lagi</span>
@endif
                                @else
                                    <span class="text-xs text-gray-300">-</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-right text-gray-600 font-medium">
                                @php
                                    $lastPurchase = \App\Models\PurchaseOrderItem::where('material_id', $material->id)->whereHas('purchaseOrder', function($q) { $q->where('status', 'diterima'); })->latest()->first();
                                    $hargaTampil = ($batch->source == 'PO') ? ($batch->purchaseOrderItems->subtotal ?? 0) : ($lastPurchase ? ($batch->qty * $lastPurchase->harga_satuan) : 0);
                                @endphp
                                Rp {{ number_format($hargaTampil, 0, ',', '.') }}
                                <p class="text-[10px] text-gray-400">(Est. Rp {{ number_format($lastPurchase->harga_satuan ?? 0, 0, ',', '.') }}/unit)</p>
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                @if($bEmpty)
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-400">Habis</span>
                                @elseif($bExp)
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">Expired</span>
                                @elseif($bSoon)
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">Hampir Expired</span>
                                @else
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Aktif</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                @if($bExp && !$bEmpty)
                                    <form action="{{ url('owner/disposal/material/'.$batch->id) }}" method="POST" onsubmit="return confirm('Buang seluruh sisa stok di batch ini ke Disposal?');">
                                        @csrf
                                        <input type="hidden" name="reason" value="expired">
                                        <button type="submit" class="inline-flex items-center gap-1 bg-red-100 hover:bg-red-200 text-red-700 font-medium px-2 py-1 rounded text-xs transition-colors">
                                            Buang
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-gray-300">—</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 border-t border-gray-200">
                    <tr>
                        <td colspan="2" class="px-5 py-3 text-right text-xs font-semibold text-gray-600">
                            Total Stok: <span class="ml-1 {{ $belowRop ? 'text-red-600' : 'text-green-700' }}">{{ number_format($batches->sum('qty')) }} {{ $material->satuan }}</span>
                        </td>
                        <td colspan="4"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @endif
</div>