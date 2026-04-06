<x-admin-app-layout>
    <div class="p-6 max-w-6xl mx-auto">

        {{-- Header --}}
        <div class="flex items-center gap-3 mb-8">
            <a href="{{ route('admin.material.index') }}" class="p-2 rounded-lg hover:bg-gray-100 text-gray-500 hover:text-gray-700 transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div class="flex-1">
                <div class="flex items-center gap-3 flex-wrap">
                    <h1 class="text-2xl font-bold text-gray-800">{{ $material->nama_bahan }}</h1>
                    @if($material->kategori) <span class="bg-gray-100 text-gray-600 px-2.5 py-0.5 rounded-md text-xs">{{ $material->kategori }}</span> @endif
                    @if($material->isBelowRop()) <span class="bg-red-100 text-red-700 border border-red-200 px-2.5 py-0.5 rounded-full text-xs">Di Bawah ROP</span> @endif
                </div>
                <p class="text-sm text-gray-500 mt-0.5">Detail inventori, batch stok & riwayat pergerakan</p>
            </div>
        </div>

        {{-- Gunakan Partial Flash Messages --}}
       @include('admin.inventory.material.partials.flash-messages')

        <div class="space-y-6">

            {{-- BARIS 1: Stat Cards & Form ROP --}}
            @php $belowRop = $material->isBelowRop(); @endphp
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                <div class="lg:col-span-4 flex flex-col gap-4">
                    <div class="bg-white rounded-xl border {{ $belowRop ? 'border-red-200 bg-red-50/10' : 'border-gray-200' }} shadow-sm p-6 flex-1 flex flex-col justify-center">
                        <p class="text-xs text-gray-400 uppercase tracking-wide font-medium">Stok Saat Ini</p>
                        <div class="flex items-baseline gap-2 mt-2">
                            <p class="text-4xl font-bold {{ $belowRop ? 'text-red-600' : 'text-gray-800' }}">{{ number_format($material->stok) }}</p>
                            <p class="text-sm text-gray-400">{{ $material->satuan }}</p>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl border border-orange-200 bg-orange-50/10 shadow-sm p-6 flex-1 flex flex-col justify-center">
                        <p class="text-xs text-orange-500 uppercase tracking-wide font-medium">Reorder Point (ROP)</p>
                        <div class="flex items-baseline gap-2 mt-2">
                            <p class="text-4xl font-bold text-orange-600">{{ number_format($material->rop, 1) }}</p>
                            <p class="text-sm text-orange-400">{{ $material->satuan }}</p>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-8 bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4 flex items-center gap-2">Pengaturan Parameter ROP</h3>
                    <form method="POST" action="{{ route('admin.materials.update', $material->id) }}" class="space-y-4">
                        @csrf @method('PUT')
                        <input type="hidden" name="nama_bahan" value="{{ $material->nama_bahan }}">
    <input type="hidden" name="satuan" value="{{ $material->satuan }}">
    <input type="hidden" name="kategori" value="{{ $material->kategori }}">
    <input type="hidden" name="source" value="inventory">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-xs text-gray-500 mb-2">Pemakaian Rata-rata / Hari</label>
                                <input type="number" step="0.01" name="pemakaian_rata_rata" value="{{ $material->pemakaian_rata_rata ?? 0 }}" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-2">Lead Time (Hari)</label>
                                <input type="number" name="lead_time" value="{{ $material->lead_time ?? 0 }}" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-2">Safety Stock</label>
                                <input type="number" name="safety_stock" value="{{ $material->safety_stock ?? 0 }}" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400">
                            </div>
                        </div>
                        <div class="flex justify-end pt-4 border-t border-gray-50">
                            <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white font-semibold py-2.5 px-8 rounded-lg text-sm shadow-md transition-all flex items-center gap-2">Update & Hitung ROP</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Info + Status Row --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">Informasi Bahan</h3>
                    <dl class="space-y-3 text-sm">
                        <div class="flex justify-between"><dt class="text-gray-400">Satuan</dt><dd class="font-medium text-gray-700">{{ $material->satuan }}</dd></div>
                        <div class="flex justify-between"><dt class="text-gray-400">Harga Rata-rata</dt><dd class="font-medium text-gray-700">Rp {{ number_format($material->harga_rata_rata, 0, ',', '.') }}</dd></div>
                        <div class="flex justify-between"><dt class="text-gray-400">Total Batch</dt><dd class="font-medium text-gray-700">{{ $batches->count() }} batch</dd></div>
                    </dl>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">Status Stok vs ROP</h3>
                    @php
                        $maxVal = max($material->rop * 2, $material->stok);
                        $pct    = $maxVal > 0 ? min(100, round(($material->stok / $maxVal) * 100)) : 100;
                        $ropPct = $maxVal > 0 ? min(100, round(($material->rop   / $maxVal) * 100)) : 50;
                    @endphp
                    <div class="relative h-4 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-500 {{ $belowRop ? 'bg-red-500' : ($pct < 60 ? 'bg-yellow-400' : 'bg-green-500') }}" style="width: {{ $pct }}%"></div>
                        <div class="absolute top-0 bottom-0 w-0.5 bg-orange-500 opacity-70" style="left: {{ $ropPct }}%"></div>
                    </div>
                    <form method="POST" action="{{ route('admin.inventory.material.sync', $material->id) }}" class="mt-4">
                        @csrf <button type="submit" class="w-full flex items-center justify-center gap-2 border border-gray-300 hover:bg-gray-50 text-gray-600 font-medium px-3 py-2 rounded-lg text-xs">Sinkronisasi Stok dari Batch</button>
                    </form>
                </div>
            </div>

            {{-- Adjustment Form --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h3 class="text-sm font-semibold text-gray-700 mb-5">± Adjustment Stok Manual</h3>
                <form method="POST" action="{{ route('admin.inventory.material.adjust', $material->id) }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
                    @csrf
                    <div><label class="block text-xs text-gray-500 mb-1.5">Tipe</label><select name="type" id="adj-type" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400"><option value="in">➕ Tambah</option><option value="out">➖ Kurangi</option></select></div>
                    <div><label class="block text-xs text-gray-500 mb-1.5">Jumlah</label><input type="number" name="quantity" min="1" required class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm"></div>
                    <div id="adj-expired-wrap"><label class="block text-xs text-gray-500 mb-1.5">Expired Date</label><input type="date" name="expired_date" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm"></div>
                    <div><label class="block text-xs text-gray-500 mb-1.5">Catatan</label><input type="text" name="note" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm"></div>
                    <div><button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-2.5 rounded-lg text-sm">Simpan</button></div>
                </form>
            </div>

            {{-- Gunakan Partial Batch List & Movement List --}}
            @include('admin.inventory.material.partials.batch-list')
           @include('admin.inventory.material.partials.movement-list')

            {{-- CTA ROP --}}
            @if($belowRop)
                <div class="bg-orange-50 border border-orange-200 rounded-xl p-5 flex justify-between items-center gap-4">
                    <p class="font-semibold text-orange-800 text-sm">Stok di bawah ROP! Segera pesan ulang.</p>
                    <a href="{{ route('admin.purchase-orders.create') }}" class="bg-orange-500 hover:bg-orange-600 text-white font-semibold px-4 py-2 rounded-lg text-sm shadow">Buang PO Sekarang</a>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        const adjType = document.getElementById('adj-type'), expWrap = document.getElementById('adj-expired-wrap');
        function toggleExpiredField() { expWrap.style.display = adjType.value === 'out' ? 'none' : ''; }
        adjType.addEventListener('change', toggleExpiredField); toggleExpiredField();
    </script>
    @endpush
</x-admin-app-layout>