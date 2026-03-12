<x-admin-app-layout>
    <div class="p-6 max-w-6xl mx-auto">

        {{-- Header --}}
        <div class="flex items-center gap-3 mb-8">
            <a href="{{ route('admin.material.index') }}"
                class="p-2 rounded-lg hover:bg-gray-100 text-gray-500 hover:text-gray-700 transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div class="flex-1">
                <div class="flex items-center gap-3 flex-wrap">
                    <h1 class="text-2xl font-bold text-gray-800">{{ $material->nama_bahan }}</h1>
                    @if($material->kategori)
                        <span class="inline-flex px-2.5 py-0.5 rounded-md text-xs font-medium bg-gray-100 text-gray-600">
                            {{ $material->kategori }}
                        </span>
                    @endif
                    @if($material->isBelowRop())
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700 border border-red-200">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            Di Bawah ROP
                        </span>
                    @endif
                </div>
                <p class="text-sm text-gray-500 mt-0.5">Detail inventori, batch stok & riwayat pergerakan</p>
            </div>
        </div>

        {{-- Flash --}}
        @if(session('success'))
            <div class="mb-6 flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">
                <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif
        @if($errors->any())
            <div class="mb-6 flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm">
                <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                {{ $errors->first() }}
            </div>
        @endif

        <div class="space-y-6">

           {{-- ── BARIS 1: Stat Cards & Form ROP Sejajar Horizontal ── --}}
@php $belowRop = $material->isBelowRop(); @endphp
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
    
    {{-- Sisi Kiri: Ringkasan Stok & ROP (4 Kolom) --}}
    <div class="lg:col-span-4 flex flex-col gap-4">
        {{-- Card Stok Saat Ini --}}
        <div class="bg-white rounded-xl border {{ $belowRop ? 'border-red-200 bg-red-50/10' : 'border-gray-200' }} shadow-sm p-6 flex-1 flex flex-col justify-center">
            <p class="text-xs text-gray-400 uppercase tracking-wide font-medium">Stok Saat Ini</p>
            <div class="flex items-baseline gap-2 mt-2">
                <p class="text-4xl font-bold {{ $belowRop ? 'text-red-600' : 'text-gray-800' }}">
                    {{ number_format($material->stok) }}
                </p>
                <p class="text-sm text-gray-400">{{ $material->satuan }}</p>
            </div>
        </div>

        {{-- Card Nilai ROP --}}
        <div class="bg-white rounded-xl border border-orange-200 bg-orange-50/10 shadow-sm p-6 flex-1 flex flex-col justify-center">
            <p class="text-xs text-orange-500 uppercase tracking-wide font-medium">Reorder Point (ROP)</p>
            <div class="flex items-baseline gap-2 mt-2">
                <p class="text-4xl font-bold text-orange-600">{{ number_format($material->rop, 1) }}</p>
                <p class="text-sm text-orange-400">{{ $material->satuan }}</p>
            </div>
            <p class="text-[10px] text-orange-400 mt-1">*Batas aman pemesanan kembali</p>
        </div>
    </div>

    {{-- Sisi Kanan: Form Pengaturan Parameter (8 Kolom) --}}
    {{-- lg:col-span-8 memastikan ini mengambil sisa ruang dan sejajar ke kanan --}}
    <div class="lg:col-span-8 bg-white rounded-xl border border-gray-200 shadow-sm p-6 flex flex-col justify-between">
        <div>
            <h3 class="text-sm font-semibold text-gray-700 mb-6 flex items-center gap-2">
                <svg class="w-4 h-4 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Pengaturan Parameter ROP
            </h3>
            
            <form method="POST" action="{{ route('admin.materials.update', $material->id) }}" class="space-y-8">
                @csrf
                @method('PATCH')
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-2">Pemakaian Rata-rata / Hari</label>
                        <input type="number" step="0.01" name="pemakaian_rata_rata" value="{{ $material->pemakaian_rata_rata ?? 0 }}" 
                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:border-orange-400 transition-all outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-2">Lead Time (Hari)</label>
                        <input type="number" name="lead_time" value="{{ $material->lead_time ?? 0 }}" 
                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:border-orange-400 transition-all outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-2">Safety Stock</label>
                        <input type="number" name="safety_stock" value="{{ $material->safety_stock ?? 0 }}" 
                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-400 focus:border-orange-400 transition-all outline-none">
                    </div>
                </div>

                <div class="flex justify-end pt-6 border-t border-gray-50">
                    <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white font-semibold py-2.5 px-8 rounded-lg text-sm shadow-md transition-all flex items-center gap-2 transform active:scale-95">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                        Update & Hitung ROP
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
            {{-- ── Info + Status Row ── --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                {{-- Info Bahan --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">Informasi Bahan</h3>
                    <dl class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-gray-400">Satuan</dt>
                            <dd class="font-medium text-gray-700">{{ $material->satuan }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-400">Harga Rata-rata</dt>
                            <dd class="font-medium text-gray-700">Rp {{ number_format($material->harga_rata_rata, 0, ',', '.') }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-400">Total Batch</dt>
                            <dd class="font-medium text-gray-700">{{ $batches->count() }} batch</dd>
                        </div>
                        @if($material->deskripsi)
                            <div class="pt-2 border-t border-gray-100">
                                <dt class="text-gray-400 mb-1">Deskripsi</dt>
                                <dd class="text-gray-600 text-xs leading-relaxed">{{ $material->deskripsi }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>

                {{-- Status Stok --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">Status Stok vs ROP</h3>
                    @php
                        $maxVal = max($material->rop * 2, $material->stok);
                        $pct    = $maxVal > 0 ? min(100, round(($material->stok / $maxVal) * 100)) : 100;
                        $ropPct = $maxVal > 0 ? min(100, round(($material->rop   / $maxVal) * 100)) : 50;
                        $barColor = $belowRop ? 'bg-red-500' : ($pct < 60 ? 'bg-yellow-400' : 'bg-green-500');
                    @endphp
                    <div class="flex justify-between text-xs text-gray-400 mb-1.5">
                        <span>0</span>
                        <span class="text-orange-600 font-medium">ROP {{ number_format($material->rop, 1) }}</span>
                        <span>{{ number_format($maxVal, 1) }}</span>
                    </div>
                    <div class="relative h-4 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-500 {{ $barColor }}" style="width: {{ $pct }}%"></div>
                        <div class="absolute top-0 bottom-0 w-0.5 bg-orange-500 opacity-70" style="left: {{ $ropPct }}%"></div>
                    </div>
                    <div class="flex justify-between mt-1.5 text-xs text-gray-400">
                        <span>Stok: <strong class="{{ $belowRop ? 'text-red-600' : 'text-green-600' }}">{{ number_format($material->stok) }}</strong></span>
                        <span>{{ $pct }}%</span>
                    </div>

                    <div class="mt-4 space-y-2">
                        @if($belowRop)
                            <div class="flex items-start gap-2 bg-red-50 border border-red-200 text-red-700 rounded-lg px-3 py-2 text-xs">
                                <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                Stok di bawah ROP! Segera buat Purchase Order.
                            </div>
                        @else
                            <div class="flex items-center gap-2 bg-green-50 border border-green-200 text-green-700 rounded-lg px-3 py-2 text-xs">
                                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                Stok aman di atas ROP.
                            </div>
                        @endif

                        {{-- Sync button --}}
                        <form method="POST" action="{{ route('admin.inventory.material.sync', $material->id) }}">
                            @csrf
                            <button type="submit"
                                class="w-full flex items-center justify-center gap-2 border border-gray-300 hover:bg-gray-50 text-gray-600 font-medium px-3 py-2 rounded-lg text-xs transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                Sinkronisasi Stok dari Batch
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- ── Adjustment Form ── --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h3 class="text-sm font-semibold text-gray-700 mb-5 flex items-center gap-2">
                    <span class="w-6 h-6 bg-blue-100 text-blue-600 rounded-md flex items-center justify-center text-xs font-bold">±</span>
                    Adjustment Stok Manual
                </h3>
                <form method="POST" action="{{ route('admin.inventory.material.adjust', $material->id) }}"
                    class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
                    @csrf

                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1.5">Tipe</label>
                        <select name="type" id="adj-type" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent">
                            <option value="in">➕ Tambah Stok</option>
                            <option value="out">➖ Kurangi Stok</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1.5">
                            Jumlah <span class="text-gray-400">({{ $material->satuan }})</span>
                        </label>
                        <input type="number" name="quantity" min="1" required placeholder="0"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent">
                    </div>

                    <div id="adj-expired-wrap">
                        <label class="block text-xs font-medium text-gray-500 mb-1.5">
                            Expired Date <span class="text-gray-300"></span>
                        </label>
                        <input type="date" name="expired_date"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1.5">
                            Catatan <span class="text-gray-300">(opsional)</span>
                        </label>
                        <input type="text" name="note" placeholder="Alasan adjustment..."
                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent">
                    </div>

                    <div>
                        <button type="submit"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-2.5 rounded-lg text-sm shadow transition-colors">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>

            {{-- ── Batch List ── --}}
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
                                        $bExp  = $batch->expired_date && \Carbon\Carbon::parse($batch->expired_date)->isPast();
                                        $bSoon = $batch->expired_date && !$bExp
                                            && \Carbon\Carbon::parse($batch->expired_date)->diffInDays(now()) <= 30;
                                        $bEmpty = $batch->qty <= 0;
                                    @endphp
                                    <tr class="hover:bg-gray-50 transition-colors
                                        {{ $bExp ? 'bg-red-50/30' : '' }}
                                        {{ $bEmpty ? 'opacity-60' : '' }}">
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
                                                   <span class="block text-xs text-yellow-500">
    {{ (int) ceil(now()->diffInDays(\Carbon\Carbon::parse($batch->expired_date), false)) }} hari lagi
</span>                                                @endif
                                            @else
                                                <span class="text-xs text-gray-300">-</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-3.5 text-right text-gray-600 font-medium">
    @php
        // Mengambil PO terakhir khusus untuk bahan ini
        $lastPurchase = \App\Models\PurchaseOrderItem::where('material_id', $material->id)
                        ->whereHas('purchaseOrder', function($q) {
                            $q->where('status', 'diterima');
                        })
                        ->latest()
                        ->first();
        
        $hargaTampil = 0;
        if($batch->source == 'PO') {
            // Kalau asalnya dari PO, tampilkan subtotal PO tersebut
            $hargaTampil = $batch->purchaseOrderItems->subtotal ?? 0;
        } else {
            // Kalau asalnya Adjustment, hitung: Qty Adjustment x Harga Satuan PO Terakhir
            $hargaTampil = $lastPurchase ? ($batch->qty * $lastPurchase->harga_satuan) : 0;
        }
        
    @endphp
    
    Rp {{ number_format($hargaTampil, 0, ',', '.') }}
    <p class="text-[10px] text-gray-400">
        (Est. Rp {{ number_format($lastPurchase->harga_satuan ?? 0, 0, ',', '.') }}/unit)
    </p>
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
                    {{-- TOMBOL DISPOSAL HANYA MUNCUL JIKA EXPIRED DAN QTY > 0 --}}
                    @if($bExp && !$bEmpty)
                        <form action="{{ url('admin/disposal/material/'.$batch->id) }}" method="POST" onsubmit="return confirm('Buang seluruh sisa stok di batch ini ke Disposal?');">
                            @csrf
                            <input type="hidden" name="reason" value="expired">
                            <button type="submit" class="inline-flex items-center gap-1 bg-red-100 hover:bg-red-200 text-red-700 font-medium px-2 py-1 rounded text-xs transition-colors">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
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
                                        Total Stok:
                                        <span class="ml-1 {{ $belowRop ? 'text-red-600' : 'text-green-700' }}">
                                            {{ number_format($batches->sum('qty')) }} {{ $material->satuan }}
                                        </span>
                                    </td>
                                    <td colspan="4"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @endif
            </div>

            {{-- ── Stock Movements ── --}}
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
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/>
                                                </svg>
                                                Masuk
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                                </svg>
                                                Keluar
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5 text-right font-bold {{ $mov->type === 'in' ? 'text-green-700' : 'text-red-600' }}">
                                        {{ $mov->type === 'in' ? '+' : '-' }}{{ number_format($mov->quantity) }}
                                        <span class="text-xs font-normal text-gray-400 ml-0.5">{{ $material->satuan }}</span>
                                    </td>
                                    <td class="px-5 py-3.5 text-xs text-gray-500 capitalize">
                                        @php
                                            $sourceLabel = [
                                                'PO'         => 'Purchase Order',
                                                'adjustment' => 'Adjustment Manual',
                                            ];
                                        @endphp
                                        {{ $sourceLabel[$mov->source] ?? $mov->source ?? '-' }}
                                    </td>
                                    <td class="px-5 py-3.5 text-xs text-gray-400">
                                        @if($mov->reference_id && $mov->source === 'PO')
                                            <a href="{{ route('admin.purchase-orders.show', $mov->reference_id) }}"
                                                class="text-orange-500 hover:underline font-mono">
                                                #{{ $mov->reference_id }}
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                     <td class="px-5 py-3.5 text-xs text-gray-500">
                {{ $mov->catatan ?? '-' }} {{-- Panggil kolom catatan --}}
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

            {{-- ── CTA ROP ── --}}
            @if($belowRop)
                <div class="bg-orange-50 border border-orange-200 rounded-xl p-5 flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M12 4a8 8 0 100 16A8 8 0 0012 4z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-orange-800 text-sm">Stok di bawah ROP!</p>
                            <p class="text-xs text-orange-600 mt-0.5">
                                Stok <strong>{{ number_format($material->stok) }} {{ $material->satuan }}</strong>
                                — kurang dari ROP <strong>{{ number_format($material->rop, 1) }}</strong>. Segera pesan ulang.
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('admin.purchase-orders.create') }}"
                        class="flex-shrink-0 inline-flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white font-semibold px-4 py-2 rounded-lg text-sm shadow transition-colors">
                        Buat PO Sekarang
                    </a>
                </div>
            @endif

        </div>
    </div>

    @push('scripts')
    <script>
        // Sembunyikan field expired_date saat tipe 'out' (tidak relevan)
        const adjType = document.getElementById('adj-type');
        const expWrap = document.getElementById('adj-expired-wrap');

        function toggleExpiredField() {
            expWrap.style.display = adjType.value === 'out' ? 'none' : '';
        }

        adjType.addEventListener('change', toggleExpiredField);
        toggleExpiredField(); // init
    </script>
    @endpush
</x-admin-app-layout>