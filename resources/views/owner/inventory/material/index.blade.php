<x-owner-app-layout>
    <div class="p-6 max-w-7xl mx-auto">

        {{-- Header --}}
        <div class="flex items-start justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Inventori Bahan Baku</h1>
                <p class="text-sm text-gray-500 mt-1">Stok bahan baku yang masuk dari Purchase Order</p>
            </div>
        </div>

        {{-- Flash Messages Partial --}}
        @include('owner.inventory.material.partials.flash-messages')

        {{-- Stats --}}
        @php
            $totalBahan    = $materials->count();
            $belowRopCount = $materials->filter(fn($m) => $m->isBelowRop())->count();
            $allBatches    = $materials->flatMap->materialStocks;
            $expiringCount = $allBatches->filter(fn($s) => $s->expired_date && \Carbon\Carbon::parse($s->expired_date)->isFuture() && \Carbon\Carbon::parse($s->expired_date)->diffInDays(now()) <= 30)->count();
            $expiredCount  = $allBatches->filter(fn($s) => $s->expired_date && \Carbon\Carbon::parse($s->expired_date)->isPast())->count();
        @endphp

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <p class="text-xs text-gray-400 font-medium">Total Bahan</p>
                <p class="text-2xl font-bold text-gray-800">{{ $totalBahan }}</p>
            </div>
            <div class="bg-white rounded-xl border border-red-100 shadow-sm p-5">
                <p class="text-xs text-red-500 font-medium">Di Bawah ROP</p>
                <p class="text-2xl font-bold text-red-600">{{ $belowRopCount }}</p>
            </div>
            <div class="bg-white rounded-xl border border-yellow-100 shadow-sm p-5">
                <p class="text-xs text-yellow-600 font-medium">Hampir Expired</p>
                <p class="text-2xl font-bold text-yellow-600">{{ $expiringCount }}</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <p class="text-xs text-gray-400 font-medium">Batch Expired</p>
                <p class="text-2xl font-bold text-gray-500">{{ $expiredCount }}</p>
            </div>
        </div>

        {{-- Search & Filter --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 mb-6 flex flex-wrap items-end gap-3">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-medium text-gray-500 mb-1.5">Cari Bahan</label>
                <input type="text" id="search-input" placeholder="Nama bahan..." class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-orange-400">
            </div>
            <div class="min-w-[160px]">
                <label class="block text-xs font-medium text-gray-500 mb-1.5">Status Stok</label>
                <select id="filter-status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-400">
                    <option value="">Semua</option>
                    <option value="rop">⚠️ Di Bawah ROP</option>
                    <option value="aman">✅ Aman</option>
                </select>
            </div>
            <div class="min-w-[160px]">
                <label class="block text-xs font-medium text-gray-500 mb-1.5">Kondisi Batch</label>
                <select id="filter-expiry" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-400">
                    <option value="">Semua</option>
                    <option value="expiring">⏳ Hampir Expired</option>
                    <option value="expired">❌ Ada yang Expired</option>
                </select>
            </div>
            <button type="button" id="reset-filter" class="text-sm text-gray-500 hover:text-gray-700 px-3 py-2 rounded-lg hover:bg-gray-100 transition-colors">Reset</button>
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
                                $nearestBatch  = $material->materialStocks->sortBy('expired_date')->whereNotNull('expired_date')->first();
                                $batchExpired  = $nearestBatch && \Carbon\Carbon::parse($nearestBatch->expired_date)->isPast();
                                $batchExpiring = $nearestBatch && !$batchExpired && \Carbon\Carbon::parse($nearestBatch->expired_date)->diffInDays(now()) <= 30;
                                $activeBatches = $material->materialStocks->where('qty', '>', 0)->count();
                                $dataExpiry    = $batchExpired ? 'expired' : ($batchExpiring ? 'expiring' : 'ok');
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors {{ $belowRop ? 'bg-red-50/20' : '' }}" data-name="{{ strtolower($material->nama_bahan) }}" data-status="{{ $belowRop ? 'rop' : 'aman' }}" data-expiry="{{ $dataExpiry }}">
                                <td class="px-5 py-4">
                                    <p class="font-semibold text-gray-800">{{ $material->nama_bahan }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5">{{ $material->satuan }}</p>
                                </td>
                                <td class="px-5 py-4"><span class="px-2.5 py-0.5 bg-gray-100 text-gray-600 rounded-md">{{ $material->kategori ?? '-' }}</span></td>
                                <td class="px-5 py-4 text-right"><span class="font-bold {{ $belowRop ? 'text-red-600' : 'text-gray-800' }}">{{ number_format($material->stok) }}</span></td>
                                <td class="px-5 py-4 text-right text-gray-500">{{ number_format($material->rop, 1) }}</td>
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
                                        <span class="text-xs font-medium {{ $batchExpired ? 'text-red-600' : ($batchExpiring ? 'text-yellow-600' : 'text-gray-600') }}">{{ \Carbon\Carbon::parse($nearestBatch->expired_date)->format('d M Y') }}</span>
                                        @if($batchExpired) <span class="block text-xs text-red-500 font-semibold">Kadaluarsa!</span>
                                        @elseif($batchExpiring) <span class="block text-xs text-yellow-500">{{ (int) now()->diffInDays(\Carbon\Carbon::parse($nearestBatch->expired_date), false) }} hari lagi</span>
                                        @endif
                                    @else
                                        <span class="text-xs text-gray-300">-</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <a href="{{ route('owner.material.show', $material->id) }}" class="inline-flex items-center gap-1.5 text-orange-600 hover:text-orange-800 font-medium text-xs transition-colors">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="px-5 py-16 text-center text-gray-400">Belum ada data bahan baku</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div id="no-results" class="hidden px-5 py-12 text-center text-sm text-gray-400">Tidak ada bahan baku yang sesuai filter.</div>
        </div>
    </div>

    @push('scripts')
    <script>
        const searchInput  = document.getElementById('search-input'), filterStatus = document.getElementById('filter-status'), filterExpiry = document.getElementById('filter-expiry'), tbody = document.getElementById('material-tbody'), noResults = document.getElementById('no-results');
        function applyFilter() {
            let visible = 0;
            tbody.querySelectorAll('tr').forEach(row => {
                const show = (!searchInput.value.toLowerCase().trim() || (row.dataset.name || '').includes(searchInput.value.toLowerCase().trim())) && (!filterStatus.value || row.dataset.status === filterStatus.value) && (!filterExpiry.value || row.dataset.expiry === filterExpiry.value);
                row.classList.toggle('hidden', !show);
                if (show) visible++;
            });
            noResults.classList.toggle('hidden', visible > 0);
        }
        [searchInput, filterStatus, filterExpiry].forEach(el => el.addEventListener('input', applyFilter));
        document.getElementById('reset-filter').addEventListener('click', () => { searchInput.value = ''; filterStatus.value = ''; filterExpiry.value = ''; applyFilter(); });
    </script>
    @endpush
</x-owner-app-layout>