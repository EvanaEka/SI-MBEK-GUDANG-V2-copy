<x-admin-app-layout>
    <div class="p-6 max-w-7xl mx-auto">

        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-800">Inventori Produk</h1>
            <p class="text-sm text-gray-500 mt-1">Stok produk jadi hasil produksi dan pembelian</p>
        </div>

        {{-- Flash --}}
        @if(session('success'))
            <div class="mb-5 flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl text-sm">
                <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-5 flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-sm">
                <svg class="w-5 h-5 text-red-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                {{ session('error') }}
            </div>
        @endif

        {{-- Stats --}}
        @php
            $allBatches      = $products->flatMap->stocks;
            $belowRopList    = $products->filter(fn($p) => $p->isBelowRop());
            $expiringBatches = $allBatches->filter(fn($s) =>
                $s->expired_date && $s->qty > 0
                && \Carbon\Carbon::parse($s->expired_date)->isFuture()
                && \Carbon\Carbon::now()->diffInDays($s->expired_date) <= 30
            );
            $expiredBatches  = $allBatches->filter(fn($s) =>
                $s->expired_date && $s->qty > 0
                && \Carbon\Carbon::parse($s->expired_date)->isPast()
            );
        @endphp

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            {{-- Total Produk --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex items-center gap-3">
                <div class="w-10 h-10 bg-orange-50 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
                <div>
                    <p class="text-xs text-gray-400 font-medium">Total Produk</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $products->count() }}</p>
                </div>
            </div>

            {{-- Di Bawah ROP --}}
            <div class="bg-white rounded-xl border {{ $belowRopList->count() > 0 ? 'border-red-200' : 'border-gray-100' }} shadow-sm p-5 flex items-center gap-3">
                <div class="w-10 h-10 {{ $belowRopList->count() > 0 ? 'bg-red-50' : 'bg-gray-50' }} rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 {{ $belowRopList->count() > 0 ? 'text-red-500' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div>
                    <p class="text-xs {{ $belowRopList->count() > 0 ? 'text-red-500' : 'text-gray-400' }} font-medium">Di Bawah ROP</p>
                    <p class="text-2xl font-bold {{ $belowRopList->count() > 0 ? 'text-red-600' : 'text-gray-800' }}">{{ $belowRopList->count() }}</p>
                </div>
            </div>

            {{-- Hampir Expired --}}
            <div class="bg-white rounded-xl border {{ $expiringBatches->count() > 0 ? 'border-yellow-200' : 'border-gray-100' }} shadow-sm p-5 flex items-center gap-3">
                <div class="w-10 h-10 {{ $expiringBatches->count() > 0 ? 'bg-yellow-50' : 'bg-gray-50' }} rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 {{ $expiringBatches->count() > 0 ? 'text-yellow-500' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-xs {{ $expiringBatches->count() > 0 ? 'text-yellow-600' : 'text-gray-400' }} font-medium">Hampir Expired</p>
                    <p class="text-2xl font-bold {{ $expiringBatches->count() > 0 ? 'text-yellow-600' : 'text-gray-800' }}">{{ $expiringBatches->count() }}</p>
                    <p class="text-xs text-gray-400">batch ≤ 30 hari</p>
                </div>
            </div>

            {{-- Batch Expired --}}
            <div class="bg-white rounded-xl border {{ $expiredBatches->count() > 0 ? 'border-red-100' : 'border-gray-100' }} shadow-sm p-5 flex items-center gap-3">
                <div class="w-10 h-10 {{ $expiredBatches->count() > 0 ? 'bg-red-50' : 'bg-gray-50' }} rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 {{ $expiredBatches->count() > 0 ? 'text-red-400' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-xs {{ $expiredBatches->count() > 0 ? 'text-red-500' : 'text-gray-400' }} font-medium">Batch Expired</p>
                    <p class="text-2xl font-bold {{ $expiredBatches->count() > 0 ? 'text-red-600' : 'text-gray-800' }}">{{ $expiredBatches->count() }}</p>
                    <p class="text-xs text-gray-400">masih ada stok</p>
                </div>
            </div>
        </div>

        {{-- Filter --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 mb-5 flex flex-wrap items-end gap-3">
            <div class="flex-1 min-w-[180px]">
                <label class="block text-xs font-medium text-gray-500 mb-1.5">Cari</label>
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/></svg>
                    <input id="q" type="text" placeholder="Nama atau kode produk..."
                        class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                </div>
            </div>
            <div class="min-w-[120px]">
                <label class="block text-xs font-medium text-gray-500 mb-1.5">Tipe</label>
                <select id="f-type" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                    <option value="">Semua</option>
                    <option value="pakan">Pakan</option>
                    <option value="obat">Obat</option>
                </select>
            </div>
            <div class="min-w-[130px]">
                <label class="block text-xs font-medium text-gray-500 mb-1.5">Sumber</label>
                <select id="f-source" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                    <option value="">Semua</option>
                    <option value="produksi">Produksi</option>
                    <option value="pembelian">Pembelian</option>
                </select>
            </div>
            <div class="min-w-[150px]">
                <label class="block text-xs font-medium text-gray-500 mb-1.5">Status Stok</label>
                <select id="f-status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                    <option value="">Semua</option>
                    <option value="rop">⚠ Di Bawah ROP</option>
                    <option value="aman">✅ Aman</option>
                </select>
            </div>
            <button id="reset-btn" class="px-3 py-2 text-sm text-gray-500 hover:text-gray-800 rounded-lg hover:bg-gray-100 transition-colors">Reset</button>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Produk</th>
                            <th class="px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide text-center">Tipe</th>
                            <th class="px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide text-center">Sumber</th>
                            <th class="px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide text-right">Stok</th>
                            <th class="px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide text-right">ROP</th>
                            <th class="px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide text-center">Alokasi Jual</th>
                            <th class="px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide text-center">Alokasi Internal</th>
                            <th class="px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide text-center">Status</th>
                             <th class="px-5 py-3.5 text-xs font-semibold text-gray-600 uppercase tracking-wide text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tbl-body" class="divide-y divide-gray-50">
                        @forelse($products as $prod)
                            @php
                                $isRop     = $prod->isBelowRop();
                                $aJual     = $prod->allocations->where('type','jual')->first();
                                $aInt      = $prod->allocations->where('type','internal')->first();
                                $activeBat = $prod->stocks->where('qty','>',0);

                                $nearExp   = $activeBat->whereNotNull('expired_date')->sortBy('expired_date')->first();
                                $expSoon   = $nearExp
                                    && \Carbon\Carbon::parse($nearExp->expired_date)->isFuture()
                                    && \Carbon\Carbon::now()->diffInDays($nearExp->expired_date) <= 30;
                                $hasExp    = $activeBat->filter(fn($s) =>
                                    $s->expired_date && \Carbon\Carbon::parse($s->expired_date)->isPast()
                                )->isNotEmpty();
                            @endphp
                            <tr class="hover:bg-gray-50/80 transition-colors {{ $isRop ? 'bg-red-50/20' : '' }}"
                                data-name="{{ strtolower($prod->nama.' '.$prod->kode) }}"
                                data-type="{{ $prod->type }}"
                                data-source="{{ $prod->source }}"
                                data-status="{{ $isRop ? 'rop' : 'aman' }}">

                                <td class="px-5 py-4">
                                    <p class="font-semibold text-gray-800">{{ $prod->nama }}</p>
                                    <p class="text-xs text-gray-400 font-mono">{{ $prod->kode }}</p>
                                    @if($hasExp)
                                        <p class="text-xs text-red-500 font-medium mt-0.5">⚠ Batch expired</p>
                                    @elseif($expSoon)
                                        <p class="text-xs text-yellow-600 mt-0.5">⏳ Hampir expired</p>
                                    @endif
                                </td>

                                <td class="px-5 py-4 text-center">
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold
                                        {{ $prod->type==='pakan' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">
                                        {{ ucfirst($prod->type) }}
                                    </span>
                                </td>

                                <td class="px-5 py-4 text-center">
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold
                                        {{ $prod->source==='produksi' ? 'bg-orange-100 text-orange-700' : 'bg-purple-100 text-purple-700' }}">
                                        {{ ucfirst($prod->source) }}
                                    </span>
                                </td>

                                <td class="px-5 py-4 text-right">
                                    <span class="text-lg font-bold {{ $isRop ? 'text-red-600' : 'text-gray-800' }}">
                                        {{ number_format($prod->stok) }}
                                    </span>
                                </td>

                                <td class="px-5 py-4 text-right text-sm {{ $isRop ? 'text-red-400' : 'text-gray-500' }}">
                                    {{ $prod->rop > 0 ? number_format($prod->rop) : '—' }}
                                </td>

                                <td class="px-5 py-4 text-center">
                                    @if($aJual && $aJual->qty > 0)
                                        <span class="inline-flex px-2 py-0.5 rounded-md text-xs font-semibold bg-blue-100 text-blue-700">
                                            {{ number_format($aJual->qty) }}
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-300">—</span>
                                    @endif
                                </td>

                                <td class="px-5 py-4 text-center">
                                    @if($aInt && $aInt->qty > 0)
                                        <span class="inline-flex px-2 py-0.5 rounded-md text-xs font-semibold bg-purple-100 text-purple-700">
                                            {{ number_format($aInt->qty) }}
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-300">—</span>
                                    @endif
                                </td>

                                <td class="px-5 py-4 text-center">
                                    @if($isRop)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                            Perlu Reorder
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                            Aman
                                        </span>
                                    @endif
                                </td>

                                <td class="px-5 py-4 text-right">
                                    <a href="{{ route('admin.inventory.product.show', $prod->id) }}"class="inline-flex items-center gap-1.5 text-orange-600 hover:text-orange-800 font-medium text-xs transition-colors">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-16 text-center text-gray-400">
                                    <svg class="w-12 h-12 mx-auto mb-3 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                    <p class="text-sm font-medium">Belum ada produk terdaftar</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <p id="no-result" class="hidden py-10 text-center text-sm text-gray-400">Tidak ada produk yang sesuai filter.</p>
        </div>

    </div>

    @push('scripts')
    <script>
        const elQ      = document.getElementById('q');
        const elType   = document.getElementById('f-type');
        const elSrc    = document.getElementById('f-source');
        const elStatus = document.getElementById('f-status');
        const rows     = document.querySelectorAll('#tbl-body tr[data-name]');
        const noResult = document.getElementById('no-result');

        function applyFilter() {
            const q  = elQ.value.toLowerCase().trim();
            const t  = elType.value;
            const s  = elSrc.value;
            const st = elStatus.value;
            let n    = 0;
            rows.forEach(r => {
                const ok = (!q  || r.dataset.name.includes(q))
                        && (!t  || r.dataset.type   === t)
                        && (!s  || r.dataset.source === s)
                        && (!st || r.dataset.status === st);
                r.classList.toggle('hidden', !ok);
                if (ok) n++;
            });
            noResult.classList.toggle('hidden', n > 0);
        }

        [elQ, elType, elSrc, elStatus].forEach(el => el.addEventListener('input', applyFilter));
        document.getElementById('reset-btn').addEventListener('click', () => {
            [elQ, elType, elSrc, elStatus].forEach(el => el.value = '');
            applyFilter();
        });
    </script>
    @endpush
</x-admin-app-layout>