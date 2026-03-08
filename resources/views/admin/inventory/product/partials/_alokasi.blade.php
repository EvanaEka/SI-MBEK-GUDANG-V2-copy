{{--
    Partial: Tab Alokasi & Adjustment
    Vars: $product, $allocations, $belowRop, $qJual, $qInternal
--}}
<div class="space-y-6">

    {{-- Mini stat bar --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm px-5 py-4 flex flex-wrap items-center gap-6 text-sm">
        <div class="flex items-center gap-2">
            <span class="text-gray-400 text-xs">Stok Total</span>
            <span class="font-bold {{ $belowRop ? 'text-red-600' : 'text-gray-800' }}">{{ number_format($product->stok) }}</span>
        </div>
        <div class="w-px h-4 bg-gray-200"></div>
        <div class="flex items-center gap-2">
            <span class="text-gray-400 text-xs">Alokasi Jual</span>
            <span class="font-bold text-blue-600">{{ number_format($qJual) }}</span>
        </div>
        <div class="w-px h-4 bg-gray-200"></div>
        <div class="flex items-center gap-2">
            <span class="text-gray-400 text-xs">Alokasi Internal</span>
            <span class="font-bold text-purple-600">{{ number_format($qInternal) }}</span>
        </div>
        <div class="w-px h-4 bg-gray-200"></div>
        @php $sisaBebas = max(0, $product->stok - $qJual - $qInternal); @endphp
        <div class="flex items-center gap-2">
            <span class="text-gray-400 text-xs">Sisa Bebas</span>
            <span class="font-bold {{ $sisaBebas === 0 ? 'text-red-600' : 'text-green-600' }}">{{ number_format($sisaBebas) }}</span>
        </div>
        @php $totalAlokasi = $qJual + $qInternal; @endphp
        @if($totalAlokasi > $product->stok)
            <span class="ml-auto inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                Alokasi melebihi stok!
            </span>
        @endif
    </div>

    {{-- Alokasi Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

        {{-- ── Alokasi Jual ── --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="bg-blue-50 border-b border-blue-100 px-5 py-4 flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-blue-800">Alokasi Jual</p>
                    <p class="text-xs text-blue-500 mt-0.5">Stok yang disiapkan untuk dijual</p>
                </div>
                <span class="text-2xl font-bold text-blue-700">{{ number_format($qJual) }}</span>
            </div>
            <div class="p-5 space-y-4">

                {{-- Set alokasi --}}
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Set Qty Alokasi</p>
                    <form method="POST" action="{{ route('admin.product.allocations.set', $product->id) }}"
                        class="flex gap-2">
                        @csrf
                        <input type="hidden" name="type" value="jual">
                        <input type="number" name="qty" min="0" required value="{{ $qJual }}"
                            placeholder="0"
                            class="flex-1 border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <button type="submit"
                            class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition-colors flex-shrink-0">
                            Simpan
                        </button>
                    </form>
                </div>

                <div class="border-t border-gray-100"></div>

                {{-- Catat Penjualan --}}
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Catat Penjualan</p>
                    @if($qJual > 0)
                        <form method="POST" action="{{ route('admin.product.allocations.sell', $product->id) }}"
                            class="flex gap-2">
                            @csrf
                            <input type="number" name="qty" min="1" max="{{ $qJual }}" required
                                placeholder="Qty terjual (maks. {{ $qJual }})"
                                class="flex-1 border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                            <button type="submit"
                                class="px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white text-xs font-semibold rounded-lg transition-colors flex-shrink-0">
                                Jual
                            </button>
                        </form>
                        <p class="text-xs text-gray-400 mt-1.5">Akan mengurangi stok + alokasi jual via FIFO.</p>
                    @else
                        <div class="flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-lg px-3 py-2.5 text-xs text-gray-400">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Set alokasi jual terlebih dahulu.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ── Alokasi Internal ── --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="bg-purple-50 border-b border-purple-100 px-5 py-4 flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-purple-800">Alokasi Internal</p>
                    <p class="text-xs text-purple-500 mt-0.5">Stok untuk pemakaian internal</p>
                </div>
                <span class="text-2xl font-bold text-purple-700">{{ number_format($qInternal) }}</span>
            </div>
            <div class="p-5 space-y-4">

                {{-- Set alokasi --}}
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Set Qty Alokasi</p>
                    <form method="POST" action="{{ route('admin.product.allocations.set', $product->id) }}"
                        class="flex gap-2">
                        @csrf
                        <input type="hidden" name="type" value="internal">
                        <input type="number" name="qty" min="0" required value="{{ $qInternal }}"
                            placeholder="0"
                            class="flex-1 border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-purple-400">
                        <button type="submit"
                            class="px-4 py-2.5 bg-purple-600 hover:bg-purple-700 text-white text-xs font-semibold rounded-lg transition-colors flex-shrink-0">
                            Simpan
                        </button>
                    </form>
                </div>

                <div class="border-t border-gray-100"></div>

                {{-- Catat Pemakaian --}}
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Catat Pemakaian</p>
                    @if($qInternal > 0)
                        <form method="POST" action="{{ route('admin.product.allocations.use-internal', $product->id) }}"
                            class="flex gap-2">
                            @csrf
                            <input type="number" name="qty" min="1" max="{{ $qInternal }}" required
                                placeholder="Qty dipakai (maks. {{ $qInternal }})"
                                class="flex-1 border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-purple-400">
                            <button type="submit"
                                class="px-4 py-2.5 bg-purple-600 hover:bg-purple-700 text-white text-xs font-semibold rounded-lg transition-colors flex-shrink-0">
                                Pakai
                            </button>
                        </form>
                        <p class="text-xs text-gray-400 mt-1.5">Akan mengurangi stok + alokasi internal via FIFO.</p>
                    @else
                        <div class="flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-lg px-3 py-2.5 text-xs text-gray-400">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Set alokasi internal terlebih dahulu.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ── Adjustment Manual ── --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
        <h3 class="text-sm font-semibold text-gray-800 mb-1 flex items-center gap-2">
            <span class="w-6 h-6 bg-gray-100 text-gray-600 rounded-md flex items-center justify-center font-bold text-xs">±</span>
            Adjustment Stok Manual
        </h3>
        <p class="text-xs text-gray-400 mb-5">Tambah atau kurangi stok secara manual. Pengurangan menggunakan metode FIFO.</p>

        <form method="POST" action="{{ route('admin.inventory.product.adjust', $product->id) }}"
             class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
                    @csrf
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Tipe <span class="text-red-500">*</span></label>
                <select name="type" id="adj-type" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                    <option value="in">➕ Tambah Stok</option>
                    <option value="out">➖ Kurangi Stok (FIFO)</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Jumlah <span class="text-red-500">*</span></label>
                <input type="number" name="qty" min="1" required placeholder="0"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
            </div>
              <div id="adj-expired-wrap">
                        <label class="block text-xs font-medium text-gray-500 mb-1.5">
                            Expired Date <span class="text-gray-300"></span>
                        </label>
                        <input type="date" name="expired_date"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent">
                    </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Alasan</label>
                <input type="text" name="reason" placeholder="Misal: stok opname..."
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
            </div>
            <div>
                 <button type="submit"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-2.5 rounded-lg text-sm shadow transition-colors">
                            Simpan
                        </button>
            </div>
        </form>
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

</div>