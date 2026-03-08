{{--
    Partial: Tab Overview
    Vars: $product, $allocations, $batches, $belowRop, $qJual, $qInternal
--}}
<div class="space-y-6">

    {{-- Stat Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border {{ $belowRop ? 'border-red-200' : 'border-gray-200' }} shadow-sm p-5">
            <p class="text-xs text-gray-400 uppercase tracking-wide font-medium">Stok Total</p>
            <p class="text-3xl font-bold mt-1 {{ $belowRop ? 'text-red-600' : 'text-gray-800' }}">
                {{ number_format($product->stok) }}
            </p>
            <p class="text-xs text-gray-400 mt-0.5">unit</p>
        </div>
       {{-- Ganti kartu ROP lama dengan form ini --}}
<div class="bg-white rounded-xl border border-orange-100 shadow-sm p-5">
    <p class="text-xs text-gray-400 uppercase tracking-wide font-medium mb-2">Reorder Point (ROP)</p>
    <form action="{{ route('admin.inventory.product.update-rop', $product->id) }}" method="POST" class="flex items-center gap-2">
        @csrf
        <div class="relative flex-1">
            <input type="number" name="rop" value="{{ $product->rop ?? 0 }}" min="0"
                class="w-full text-2xl font-bold text-orange-500 bg-orange-50/50 border-none rounded-lg p-0 focus:ring-0">
        </div>
        <button type="submit" title="Simpan ROP" 
            class="p-2 bg-orange-100 text-orange-600 rounded-lg hover:bg-orange-200 transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
        </button>
    </form>
    <p class="text-[10px] text-gray-400 mt-1">Klik angka untuk mengubah, lalu tekan centang</p>
</div>
        <div class="bg-white rounded-xl border border-blue-100 shadow-sm p-5">
            <p class="text-xs text-gray-400 uppercase tracking-wide font-medium">Alokasi Jual</p>
            <p class="text-3xl font-bold text-blue-600 mt-1">{{ number_format($qJual) }}</p>
            <p class="text-xs text-gray-400 mt-0.5">unit dialokasikan</p>
        </div>
        <div class="bg-white rounded-xl border border-purple-100 shadow-sm p-5">
            <p class="text-xs text-gray-400 uppercase tracking-wide font-medium">Alokasi Internal</p>
            <p class="text-3xl font-bold text-purple-600 mt-1">{{ number_format($qInternal) }}</p>
            <p class="text-xs text-gray-400 mt-0.5">unit dialokasikan</p>
        </div>
    </div>

    {{-- Info + Status --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

        {{-- Info Produk --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Informasi Produk</h3>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-400">Harga Jual</dt>
                    <dd class="font-semibold text-gray-700">Rp {{ number_format($product->harga ?? 0, 0, ',', '.') }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-400">Tipe Produk</dt>
                    <dd class="font-semibold text-gray-700">{{ ucfirst($product->type) }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-400">Sumber Stok</dt>
                    <dd class="font-semibold text-gray-700">{{ ucfirst($product->source) }}</dd>
                </div>
                @if($product->formula)
                    <div class="flex justify-between">
                        <dt class="text-gray-400">Formula</dt>
                        <dd class="font-semibold text-orange-600 font-mono text-xs">
                            {{ $product->formula->kode_formula }} · {{ $product->formula->nama_formula }}
                        </dd>
                    </div>
                @endif
                <div class="flex justify-between">
                    <dt class="text-gray-400">Batch Aktif</dt>
                    <dd class="font-semibold text-gray-700">
                        {{ $batches->where('qty', '>', 0)->count() }} / {{ $batches->count() }} batch
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-400">Sisa Bebas</dt>
                    <dd class="font-semibold {{ max(0, $product->stok - $qJual - $qInternal) === 0 ? 'text-red-600' : 'text-gray-700' }}">
                        {{ number_format(max(0, $product->stok - $qJual - $qInternal)) }} unit
                    </dd>
                </div>
            </dl>
        </div>

        {{-- Status Stok --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Status Stok</h3>

            @php
                $rop    = $product->rop ?? 0;
                $maxVal = max($rop * 2, $product->stok, 1);
                $pct    = min(round(($product->stok / $maxVal) * 100), 100);
                $ropPct = $maxVal > 0 ? min(round(($rop / $maxVal) * 100), 100) : 50;
                $barCol = $belowRop ? 'bg-red-500' : ($pct < 55 ? 'bg-yellow-400' : 'bg-green-500');
            @endphp

            @if($rop > 0)
                <div class="flex justify-between text-xs text-gray-400 mb-1">
                    <span>0</span>
                    <span class="text-orange-500 font-medium">ROP {{ number_format($rop) }}</span>
                    <span>{{ number_format($maxVal) }}</span>
                </div>
                <div class="relative h-4 bg-gray-100 rounded-full overflow-hidden mb-1">
                    <div class="h-full rounded-full transition-all duration-500 {{ $barCol }}" style="width: {{ $pct }}%"></div>
                    <div class="absolute inset-y-0 w-0.5 bg-orange-500 opacity-80" style="left: {{ $ropPct }}%"></div>
                </div>
                <div class="flex justify-between text-xs text-gray-400 mb-5">
                    <span>Stok: <strong class="{{ $belowRop ? 'text-red-600' : 'text-green-600' }}">{{ number_format($product->stok) }}</strong></span>
                    <span>{{ $pct }}%</span>
                </div>
            @endif

            @if($belowRop)
                <div class="mb-3 flex items-start gap-2 bg-red-50 border border-red-200 text-red-700 px-3 py-2.5 rounded-lg text-xs">
                    <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    Stok di bawah ROP. {{ $product->source === 'produksi' ? 'Segera jalankan produksi baru.' : 'Segera buat Purchase Order.' }}
                </div>
            @else
                <div class="mb-3 flex items-center gap-2 bg-green-50 border border-green-200 text-green-700 px-3 py-2.5 rounded-lg text-xs">
                    <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    Stok aman, di atas ROP.
                </div>
            @endif

            <form method="POST" action="{{ route('admin.inventory.product.sync', $product->id) }}">
                @csrf
                <button type="submit"
                    class="w-full flex items-center justify-center gap-2 border border-gray-300 hover:bg-gray-50 text-gray-600 font-medium px-3 py-2.5 rounded-lg text-xs transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Sinkronisasi Stok dari Batch
                </button>
            </form>
        </div>
    </div>

    {{-- CTA ROP --}}
    @if($belowRop)
        <div class="bg-orange-50 border border-orange-200 rounded-xl p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-orange-800 text-sm">Stok di bawah ROP!</p>
                    <p class="text-xs text-orange-600 mt-0.5">
                        Stok <strong>{{ number_format($product->stok) }}</strong> unit —
                        ROP <strong>{{ number_format($product->rop) }}</strong> unit.
                    </p>
                </div>
            </div>
            @if($product->source === 'produksi')
                <a href="{{ route('admin.productions.index') }}"
                    class="flex-shrink-0 inline-flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white font-semibold px-4 py-2 rounded-lg text-sm shadow transition-colors">
                    🏭 Jalankan Produksi
                </a>
            @else
                <a href="{{ route('admin.purchase-orders.create') }}"
                    class="flex-shrink-0 inline-flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white font-semibold px-4 py-2 rounded-lg text-sm shadow transition-colors">
                    📋 Buat PO Sekarang
                </a>
            @endif
        </div>
    @endif

</div>