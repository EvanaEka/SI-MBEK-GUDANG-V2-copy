<x-admin-app-layout>
    <div class="p-6 max-w-2xl mx-auto">

        {{-- Header --}}
        <div class="flex items-center gap-3 mb-8">
            <a href="{{ route('admin.productions.index') }}"
                class="p-2 rounded-lg hover:bg-gray-100 text-gray-500 hover:text-gray-700 transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Buat Produksi Baru</h1>
                <p class="text-sm text-gray-500 mt-0.5">Mulai proses produksi berdasarkan formula aktif</p>
            </div>
        </div>

        {{-- Error --}}
        @if($errors->any())
            <div class="mb-6 flex items-start gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm">
                <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">

            {{-- Info alur --}}
            <div class="mb-6 bg-orange-50 border border-orange-100 rounded-lg px-4 py-3 text-xs text-orange-700">
                <p class="font-semibold mb-1">Alur Produksi:</p>
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="inline-flex items-center gap-1 bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full font-medium">1. Buat Produksi</span>
                    <svg class="w-3 h-3 text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    <span class="inline-flex items-center gap-1 bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full font-medium">2. Input QC</span>
                    <svg class="w-3 h-3 text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    <span class="inline-flex items-center gap-1 bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-medium">3. Selesaikan</span>
                </div>
                <p class="mt-2 text-orange-600">Pastikan produk yang dipilih sudah di-assign ke formula tersebut.</p>
            </div>

            <form action="{{ route('admin.productions.store') }}" method="POST">
                @csrf
                <div class="space-y-5">

                    {{-- Formula --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1.5">
                            Formula <span class="text-red-500">*</span>
                        </label>
                        <select name="formula_id" id="selectFormula" required onchange="loadProducts(this.value)"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-orange-400 transition-all">
                            <option value="">-- Pilih Formula --</option>
                            @foreach($formulas as $formula)
                                <option value="{{ $formula->id }}" {{ old('formula_id') == $formula->id ? 'selected' : '' }}>
                                    [{{ $formula->kode_formula }}] {{ $formula->nama_formula }}
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-gray-400">Hanya formula aktif yang ditampilkan</p>
                    </div>

                    {{-- Produk --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1.5">
                            Produk <span class="text-red-500">*</span>
                        </label>
                        <select name="product_id" id="selectProduct" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-orange-400 transition-all">
                            <option value="">-- Pilih formula dahulu --</option>
                        </select>
                        <p class="mt-1 text-xs text-gray-400">
                            Produk yang muncul hanya yang memiliki <code class="bg-gray-100 px-1 rounded">formula_id</code> sesuai formula yang dipilih.
                            Jika kosong, pastikan produk sudah di-assign ke formula ini.
                        </p>
                    </div>

                    {{-- Qty --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1.5">
                            Jumlah Produksi <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="number" name="qty_produksi" min="1" step="1" required
                                value="{{ old('qty_produksi') }}" placeholder="0"
                                class="w-full border border-gray-300 rounded-lg pl-3 pr-10 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-orange-400 transition-all">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400">kg</span>
                        </div>
                        <p class="mt-1 text-xs text-gray-400">Stok bahan baku akan dikurangi otomatis sesuai komposisi formula (FIFO)</p>
                    </div>

                    {{-- Preview komposisi --}}
                    <div id="previewKomposisi" class="hidden">
                        <label class="block text-xs font-medium text-gray-500 mb-1.5">Preview Komposisi Bahan</label>
                        <div id="komposisiList" class="bg-gray-50 rounded-lg border border-gray-200 divide-y divide-gray-100"></div>
                    </div>

                </div>

                <div class="mt-6 pt-5 border-t border-gray-100 flex items-center justify-end gap-3">
                    <a href="{{ route('admin.productions.index') }}"
                        class="px-5 py-2.5 text-sm font-medium text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        Batal
                    </a>
                    <button type="submit"
                        class="inline-flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white font-semibold px-6 py-2.5 rounded-lg shadow text-sm transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Mulai Produksi
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
    // Data formula → produk dari controller
    const formulaProducts  = @json($formulaProducts ?? []);
    const formulaMaterials = @json($formulaMaterials ?? []);

    function loadProducts(formulaId) {
        const select   = document.getElementById('selectProduct');
        const preview  = document.getElementById('previewKomposisi');
        const list     = document.getElementById('komposisiList');
        const products = formulaProducts[formulaId] || [];

        select.innerHTML = '';
        if (!formulaId) {
            select.innerHTML = '<option value="">-- Pilih formula dahulu --</option>';
            preview.classList.add('hidden');
            return;
        }
        if (products.length === 0) {
            select.innerHTML = '<option value="">-- Tidak ada produk untuk formula ini --</option>';
        } else {
            select.innerHTML = '<option value="">-- Pilih Produk --</option>';
            products.forEach(p => {
                select.innerHTML += `<option value="${p.id}">[${p.kode}] ${p.nama}</option>`;
            });
        }

        // Preview komposisi bahan
        const materials = formulaMaterials[formulaId] || [];
        if (materials.length > 0) {
            list.innerHTML = materials.map(m =>
                `<div class="flex justify-between items-center px-4 py-2.5 text-xs">
                    <span class="text-gray-700">${m.nama_bahan}</span>
                    <span class="font-semibold text-gray-800">${m.persentase}%</span>
                </div>`
            ).join('');
            preview.classList.remove('hidden');
        } else {
            preview.classList.add('hidden');
        }
    }

    // Jika ada old value setelah error
    const oldFormula = document.getElementById('selectFormula').value;
    if (oldFormula) loadProducts(oldFormula);
    </script>
    @endpush
</x-admin-app-layout>