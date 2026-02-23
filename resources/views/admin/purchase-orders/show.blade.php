<x-admin-app-layout>
    <div class="p-6 max-w-5xl mx-auto">

        {{-- Header --}}
        <div class="flex items-start justify-between mb-8">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.purchase-orders.index') }}"
                    class="p-2 rounded-lg hover:bg-gray-100 text-gray-500 hover:text-gray-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-2xl font-bold text-gray-800 font-mono">{{ $po->kode_po }}</h1>
                        @php
                            $statusConfig = [
                                'draft'    => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                                'dipesan'  => 'bg-blue-100 text-blue-700 border-blue-200',
                                'diterima' => 'bg-green-100 text-green-700 border-green-200',
                            ];
                            $cls = $statusConfig[$po->status] ?? 'bg-gray-100 text-gray-600 border-gray-200';
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $cls }}">
                            {{ ucfirst($po->status) }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-500 mt-0.5">Detail Purchase Order</p>
                </div>
            </div>
        </div>

        {{-- Alerts --}}
        @if(session('success'))
            <div class="mb-6 flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">
                <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm">
                <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                {{ session('error') }}
            </div>
        @endif

        <div class="space-y-6">

            {{-- Info Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">

                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                    <p class="text-xs text-gray-400 uppercase tracking-wide font-medium">Supplier</p>
                    <p class="text-sm font-semibold text-gray-800 mt-1.5">{{ $po->supplier->nama_supplier ?? '-' }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $po->supplier->kota ?? '' }}{{ $po->supplier->kota && $po->supplier->provinsi ? ', ' : '' }}{{ $po->supplier->provinsi ?? '' }}</p>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                    <p class="text-xs text-gray-400 uppercase tracking-wide font-medium">Tipe</p>
                    <p class="text-sm font-semibold mt-1.5">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium
                            {{ $po->type === 'material' ? 'bg-purple-50 text-purple-700' : 'bg-cyan-50 text-cyan-700' }}">
                            {{ ucfirst($po->type ?? '-') }}
                        </span>
                    </p>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                    <p class="text-xs text-gray-400 uppercase tracking-wide font-medium">Tanggal Pesan</p>
                    <p class="text-sm font-semibold text-gray-800 mt-1.5">
                        {{ \Carbon\Carbon::parse($po->tanggal_pesan)->format('d M Y') }}
                    </p>
                </div>

                {{-- Card Tanggal Disetujui --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
        <p class="text-xs text-gray-400 uppercase tracking-wide font-medium">Tanggal Disetujui</p>
        <p class="text-sm font-semibold text-gray-800 mt-1.5">
            {{ $po->tanggal_disetujui ? \Carbon\Carbon::parse($po->tanggal_disetujui)->format('d M Y') : '-' }}
        </p>
    </div>

                {{-- Card Tanggal Diterima --}}
<div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
    <p class="text-xs text-gray-400 uppercase tracking-wide font-medium">Tanggal Diterima</p>
    <p class="text-sm font-semibold text-gray-800 mt-1.5">
        {{ $po->tanggal_diterima ? \Carbon\Carbon::parse($po->tanggal_diterima)->format('d M Y') : '-' }}
    </p>
</div>
            </div>

            {{-- PO Actors --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h3 class="text-sm font-semibold text-gray-700 mb-4">Informasi Pemesanan</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-4 h-4 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Dipesan Atas Nama</p>
                            <p class="font-semibold text-gray-800">{{ $po->dipesanOleh->name ?? '-' }}</p>
                            <p class="text-xs text-gray-400">{{ class_basename($po->dipesan_oleh_type ?? '') }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Dicatat / Diinput Oleh</p>
                            <p class="font-semibold text-gray-800">{{ $po->dicatatOleh->name ?? '-' }}</p>
                            <p class="text-xs text-gray-400">{{ class_basename($po->dicatat_oleh_type ?? '') }}</p>
                        </div>
                    </div>
                </div>
                @if($po->catatan_owner)
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <p class="text-xs text-gray-400 mb-1">Catatan</p>
                    <p class="text-sm text-gray-700 bg-gray-50 rounded-lg p-3">{{ $po->catatan_owner }}</p>
                </div>
                @endif
            </div>

            {{-- Items Table --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-700">Item Pesanan</h3>
                    <span class="text-xs text-gray-400">{{ $po->items->count() }} item</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="px-5 py-3 text-left font-semibold text-gray-600 text-xs uppercase tracking-wide">Material / Produk</th>
                                <th class="px-5 py-3 text-right font-semibold text-gray-600 text-xs uppercase tracking-wide">Jumlah Pesan</th>
                                <th class="px-5 py-3 text-right font-semibold text-gray-600 text-xs uppercase tracking-wide">Harga Satuan</th>
                                <th class="px-5 py-3 text-right font-semibold text-gray-600 text-xs uppercase tracking-wide">Subtotal</th>
                                <th class="px-5 py-3 text-right font-semibold text-gray-600 text-xs uppercase tracking-wide">Diterima</th>
                                <th class="px-5 py-3 text-right font-semibold text-gray-600 text-xs uppercase tracking-wide">Selisih</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($po->items as $item)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-5 py-4">
                                        <p class="font-medium text-gray-800">{{ $item->material->nama_bahan ?? $item->product->nama ?? '-' }}</p>
                                        <p class="text-xs text-gray-400">{{ $item->material->satuan ?? '' }}</p>
                                    </td>
                                    <td class="px-5 py-4 text-right text-gray-700">{{ number_format($item->jumlah) }}</td>
                                    <td class="px-5 py-4 text-right text-gray-700">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                                    <td class="px-5 py-4 text-right font-semibold text-gray-800">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                    <td class="px-5 py-4 text-right">
                                        @if($item->jumlah_diterima !== null)
                                            <span class="font-medium text-gray-700">{{ number_format($item->jumlah_diterima) }}</span>
                                        @else
                                            <span class="text-gray-300">-</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-right">
                                        @if($item->selisih !== null)
                                            <span class="font-semibold {{ $item->selisih < 0 ? 'text-red-600' : ($item->selisih > 0 ? 'text-green-600' : 'text-gray-500') }}">
                                                {{ $item->selisih > 0 ? '+' : '' }}{{ number_format($item->selisih) }}
                                            </span>
                                        @else
                                            <span class="text-gray-300">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="px-5 py-8 text-center text-gray-400">Tidak ada item</td></tr>
                            @endforelse
                        </tbody>
                        <tfoot class="bg-orange-50 border-t border-orange-100">
                            <tr>
                                <td colspan="3" class="px-5 py-3 text-right text-sm font-semibold text-gray-600">Total:</td>
                                <td class="px-5 py-3 text-right text-base font-bold text-orange-700">
                                    Rp {{ number_format($po->items->sum('subtotal'), 0, ',', '.') }}
                                </td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- Receive Form (Admin only, status dipesan) --}}
            @if(auth()->guard('admin')->check() && $po->status === 'dipesan')
                <div class="bg-white rounded-xl border border-blue-200 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 bg-blue-50 border-b border-blue-100 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        <h3 class="text-sm font-semibold text-blue-700">Terima Barang</h3>
                        <span class="text-xs text-blue-500 ml-auto">Admin only</span>
                    </div>

                    <form method="POST" action="{{ route('admin.purchase-orders.receive', $po->id) }}" id="receive-form">
                        @csrf
                        <div class="p-5 overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="border-b border-gray-100">
                                    <tr>
                                        <th class="pb-3 text-left font-semibold text-gray-600 text-xs uppercase tracking-wide">Material</th>
                                        <th class="pb-3 text-right font-semibold text-gray-600 text-xs uppercase tracking-wide">Dipesan</th>
                                        <th class="pb-3 text-center font-semibold text-gray-600 text-xs uppercase tracking-wide w-32">Jumlah Diterima</th>
                                        @if($po->type === 'material')
                                        <th class="pb-3 text-center font-semibold text-gray-600 text-xs uppercase tracking-wide">Expired Date</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @foreach($po->items as $item)
                                        <tr>
                                            <input type="hidden" name="items[{{ $loop->index }}][id]" value="{{ $item->id }}">
                                            <td class="py-3.5 pr-4">
                                                <p class="font-medium text-gray-800">{{ $item->material->nama_bahan ?? $item->product->nama ?? '-' }}</p>
                                                <p class="text-xs text-gray-400">{{ $item->material->satuan ?? '' }}</p>
                                            </td>
                                            <td class="py-3.5 text-right text-gray-600 pr-4">{{ number_format($item->jumlah) }}</td>
                                            <td class="py-3.5 text-center">
                                                @if($item->jumlah_diterima !== null && $item->jumlah_diterima != 0)
                                                    <span class="inline-flex items-center gap-1 text-green-700 font-semibold text-xs bg-green-50 px-2.5 py-1 rounded-full">
                                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                        Sudah ({{ $item->jumlah_diterima }})
                                                    </span>
                                                @else
                                                    <input type="number" name="items[{{ $loop->index }}][jumlah_diterima]"
                                                        min="0" value="{{ $item->jumlah }}" required
                                                        class="w-24 border border-gray-300 rounded-lg px-2 py-1.5 text-center text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent">
                                                @endif
                                            </td>
                                            @if($po->type === 'material')
                                            <td class="py-3.5 text-center">
                                                @if($item->jumlah_diterima !== null && $item->jumlah_diterima != 0)
                                                    <span class="text-gray-400 text-xs">-</span>
                                                @else
                                                    <input type="date" name="items[{{ $loop->index }}][expired_date]"
                                                        class="border border-gray-300 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent">
                                                @endif
                                            </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="px-5 pb-5">
                            <button type="button" onclick="confirmReceive()"
                                class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold px-5 py-2.5 rounded-lg text-sm shadow transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                                Konfirmasi Penerimaan Barang
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            {{-- Timeline --}}
            @if($po->status === 'diterima')
                <div class="bg-green-50 border border-green-200 rounded-xl p-5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-green-800">Purchase Order Selesai</p>
                            <p class="text-sm text-green-600 mt-0.5">
                                Semua barang telah diterima pada
                                {{ $po->tanggal_diterima ? \Carbon\Carbon::parse($po->tanggal_diterima)->format('d M Y, H:i') : '-' }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>

    @push('scripts')
    <script>
        function confirmReceive() {
            Swal.fire({
                title: 'Konfirmasi Penerimaan Barang?',
                text: 'Stok akan diperbarui sesuai jumlah yang diterima.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#16a34a',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Terima Barang!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('receive-form').submit();
                }
            });
        }
    </script>
    @endpush
</x-admin-app-layout>