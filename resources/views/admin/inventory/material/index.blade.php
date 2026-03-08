<x-admin-app-layout>
    <div class="p-6 max-w-7xl mx-auto">

        {{-- Header --}}
        <div class="flex items-start justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Inventori Bahan Baku</h1>
                <p class="text-sm text-gray-500 mt-1">Stok bahan baku yang masuk dari Purchase Order</p>
            </div>
            <a href="{{ route('admin.purchase-orders.create') }}"
                class="inline-flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white font-semibold px-5 py-2.5 rounded-lg shadow transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Buat Pemesanan Baru
            </a>
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
        @if(session('error'))
            <div class="mb-6 flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm">
                <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                {{ session('error') }}
            </div>
        @endif

        {{-- Stats --}}
        @php
            $totalBahan    = $materials->count();
            $belowRopCount = $materials->filter(fn($m) => $m->isBelowRop())->count();
            $allBatches    = $materials->flatMap->materialStocks;
            $expiringCount = $allBatches->filter(fn($s) =>
                $s->expired_date &&
                \Carbon\Carbon::parse($s->expired_date)->isFuture() &&
                \Carbon\Carbon::parse($s->expired_date)->diffInDays(now()) <= 30
            )->count();
            $expiredCount  = $allBatches->filter(fn($s) =>
                $s->expired_date && \Carbon\Carbon::parse($s->expired_date)->isPast()
            )->count();
        @endphp

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-orange-50 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 font-medium">Total Bahan</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $totalBahan }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl border border-red-100 shadow-sm p-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-red-50 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-red-500 font-medium">Di Bawah ROP</p>
                        <p class="text-2xl font-bold text-red-600">{{ $belowRopCount }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl border border-yellow-100 shadow-sm p-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-yellow-50 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-yellow-600 font-medium">Hampir Expired</p>
                        <p class="text-2xl font-bold text-yellow-600">{{ $expiringCount }}</p>
                        <p class="text-xs text-gray-400">≤ 30 hari</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 font-medium">Batch Expired</p>
                        <p class="text-2xl font-bold text-gray-500">{{ $expiredCount }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Search & Filter (client-side, no reload needed) --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 mb-6 flex flex-wrap items-end gap-3">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-medium text-gray-500 mb-1.5">Cari Bahan</label>
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                    </svg>
                    <input type="text" id="search-input" placeholder="Nama bahan..."
                        class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent">
                </div>
            </div>
            <div class="min-w-[160px]">
                <label class="block text-xs font-medium text-gray-500 mb-1.5">Status Stok</label>
                <select id="filter-status"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent">
                    <option value="">Semua</option>
                    <option value="rop">⚠️ Di Bawah ROP</option>
                    <option value="aman">✅ Aman</option>
                </select>
            </div>
            <div class="min-w-[160px]">
                <label class="block text-xs font-medium text-gray-500 mb-1.5">Kondisi Batch</label>
                <select id="filter-expiry"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent">
                    <option value="">Semua</option>
                    <option value="expiring">⏳ Hampir Expired</option>
                    <option value="expired">❌ Ada yang Expired</option>
                </select>
            </div>
            <button type="button" id="reset-filter"
                class="text-sm text-gray-500 hover:text-gray-700 px-3 py-2 rounded-lg hover:bg-gray-100 transition-colors">
                Reset
            </button>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-5 py-3.5 text-xs font-semibold text-gray-600 uppercase tracking-wide">Bahan Baku</th>
                            <th class="px-5 py-3.5 text-xs font-semibold text-gray-600 uppercase tracking-wide">Kategori</th>
                            <th class="px-5 py-3.5 text-xs font-semibold text-gray-600 uppercase tracking-wide text-right">Stok Total</th>
                            <th class="px-5 py-3.5 text-xs font-semibold text-gray-600 uppercase tracking-wide text-right">ROP</th>
                            <th class="px-5 py-3.5 text-xs font-semibold text-gray-600 uppercase tracking-wide text-center">Status</th>
                            <th class="px-5 py-3.5 text-xs font-semibold text-gray-600 uppercase tracking-wide text-center">Batch Aktif</th>
                            <th class="px-5 py-3.5 text-xs font-semibold text-gray-600 uppercase tracking-wide text-center">Batch Terdekat Expired</th>
                            <th class="px-5 py-3.5 text-xs font-semibold text-gray-600 uppercase tracking-wide text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50" id="material-tbody">
                        @forelse($materials as $material)
                            @php
                                $belowRop      = $material->isBelowRop();
                                $sortedBatches = $material->materialStocks->sortBy('expired_date');
                                $nearestBatch  = $sortedBatches->whereNotNull('expired_date')->first();
                                $batchExpired  = $nearestBatch && \Carbon\Carbon::parse($nearestBatch->expired_date)->isPast();
                                $batchExpiring = $nearestBatch && !$batchExpired
                                    && \Carbon\Carbon::parse($nearestBatch->expired_date)->diffInDays(now()) <= 30;
                                $activeBatches = $material->materialStocks->where('qty', '>', 0)->count();

                                // data attributes for JS filtering
                                $dataExpiry = $batchExpired ? 'expired' : ($batchExpiring ? 'expiring' : 'ok');
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors {{ $belowRop ? 'bg-red-50/20' : '' }}"
                                data-name="{{ strtolower($material->nama_bahan) }}"
                                data-status="{{ $belowRop ? 'rop' : 'aman' }}"
                                data-expiry="{{ $dataExpiry }}">

                                <td class="px-5 py-4">
                                    <p class="font-semibold text-gray-800">{{ $material->nama_bahan }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5">{{ $material->satuan }}</p>
                                </td>

                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-gray-100 text-gray-600">
                                        {{ $material->kategori ?? '-' }}
                                    </span>
                                </td>

                                <td class="px-5 py-4 text-right">
                                    <span class="font-bold text-lg {{ $belowRop ? 'text-red-600' : 'text-gray-800' }}">
                                        {{ number_format($material->stok) }}
                                    </span>
                                    <span class="text-xs text-gray-400 ml-1">{{ $material->satuan }}</span>
                                </td>

                                <td class="px-5 py-4 text-right text-gray-500">
                                    {{ number_format($material->rop, 1) }}
                                </td>

                                <td class="px-5 py-4 text-center">
                                    @if($belowRop)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                            </svg>
                                            Perlu Reorder
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            Aman
                                        </span>
                                    @endif
                                </td>

                                <td class="px-5 py-4 text-center">
                                    <span class="font-medium text-gray-700">{{ $activeBatches }}</span>
                                    <span class="text-xs text-gray-400"> / {{ $material->materialStocks->count() }} batch</span>
                                </td>

                                <td class="px-5 py-4 text-center">
                                    @if($nearestBatch && $nearestBatch->expired_date)
                                        @php $expDate = \Carbon\Carbon::parse($nearestBatch->expired_date); @endphp
                                        <span class="text-xs font-medium {{ $batchExpired ? 'text-red-600' : ($batchExpiring ? 'text-yellow-600' : 'text-gray-600') }}">
                                            {{ $expDate->format('d M Y') }}
                                        </span>
                                        @if($batchExpired)
                                            <span class="block text-xs text-red-500 font-semibold">Kadaluarsa!</span>
                                        @elseif($batchExpiring)
                                           <span class="block text-xs text-yellow-500">
    {{ (int) now()->diffInDays(\Carbon\Carbon::parse($nearestBatch->expired_date), false) }} hari lagi
</span>
                                        @endif
                                    @else
                                        <span class="text-xs text-gray-300">-</span>
                                    @endif
                                </td>

                                <td class="px-5 py-4 text-right">
                                    <a href="{{ route('admin.material.show', $material->id) }}"
                                        class="inline-flex items-center gap-1.5 text-orange-600 hover:text-orange-800 font-medium text-xs transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-5 py-16 text-center">
                                    <div class="flex flex-col items-center gap-3 text-gray-400">
                                        <svg class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                        </svg>
                                        <p class="text-sm font-medium">Belum ada data bahan baku</p>
                                        <p class="text-xs">Data muncul setelah Purchase Order material diterima</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div id="no-results" class="hidden px-5 py-12 text-center text-sm text-gray-400">
                Tidak ada bahan baku yang sesuai filter.
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const searchInput  = document.getElementById('search-input');
        const filterStatus = document.getElementById('filter-status');
        const filterExpiry = document.getElementById('filter-expiry');
        const resetBtn     = document.getElementById('reset-filter');
        const tbody        = document.getElementById('material-tbody');
        const noResults    = document.getElementById('no-results');

        function applyFilter() {
            const q      = searchInput.value.toLowerCase().trim();
            const status = filterStatus.value;
            const expiry = filterExpiry.value;
            let visible  = 0;

            tbody.querySelectorAll('tr').forEach(row => {
                const matchName   = !q      || (row.dataset.name   || '').includes(q);
                const matchStatus = !status || row.dataset.status  === status;
                const matchExpiry = !expiry || row.dataset.expiry  === expiry;

                const show = matchName && matchStatus && matchExpiry;
                row.classList.toggle('hidden', !show);
                if (show) visible++;
            });

            noResults.classList.toggle('hidden', visible > 0);
        }

        searchInput.addEventListener('input', applyFilter);
        filterStatus.addEventListener('change', applyFilter);
        filterExpiry.addEventListener('change', applyFilter);

        resetBtn.addEventListener('click', () => {
            searchInput.value  = '';
            filterStatus.value = '';
            filterExpiry.value = '';
            applyFilter();
        });
    </script>
    @endpush
</x-admin-app-layout>