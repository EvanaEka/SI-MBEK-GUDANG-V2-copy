@if($movements->isNotEmpty())
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
        <h3 class="text-sm font-semibold text-gray-700">Riwayat Pergerakan Stok</h3>
        <span class="text-xs text-gray-400">{{ $movements->count() }} transaksi</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Tanggal</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wide">Tipe</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wide">Qty</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Sumber</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Referensi</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Catatan</th>
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
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700">Masuk</span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700">Keluar</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-right font-bold {{ $mov->type === 'in' ? 'text-green-700' : 'text-red-600' }}">
                            {{ $mov->type === 'in' ? '+' : '-' }}{{ number_format($mov->quantity) }}
                            <span class="text-xs font-normal text-gray-400 ml-0.5">{{ $material->satuan }}</span>
                        </td>
                        <td class="px-5 py-3.5 text-xs text-gray-500 capitalize">
                            @php $sourceLabel = ['PO' => 'Purchase Order', 'adjustment' => 'Adjustment Manual']; @endphp
                            {{ $sourceLabel[$mov->source] ?? $mov->source ?? '-' }}
                        </td>
                        <td class="px-5 py-3.5 text-xs text-gray-400">
                            @if($mov->reference_id && $mov->source === 'PO')
                                <a href="{{ route('owner.purchase-orders.show', $mov->reference_id) }}" class="text-orange-500 hover:underline font-mono">#{{ $mov->reference_id }}</a>
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-xs text-gray-500">
                            {{ $mov->catatan ?? '-' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@else
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm px-5 py-10 text-center text-gray-400">
        <p class="text-sm">Belum ada riwayat pergerakan stok.</p>
    </div>
@endif