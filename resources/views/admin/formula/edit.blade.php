<x-admin-app-layout>
    <div class="p-6 max-w-5xl mx-auto">

        {{-- Header --}}
        <div class="flex items-center gap-3 mb-8">
            <a href="{{ route('admin.formula.index') }}"
                class="p-2 rounded-lg hover:bg-gray-100 text-gray-500 hover:text-gray-700 transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div class="flex-1">
                <div class="flex items-center gap-3 flex-wrap">
                    <h1 class="text-2xl font-bold text-gray-800">Edit Formula</h1>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-mono font-semibold bg-gray-100 text-gray-700">
                        {{ $formula->kode_formula }}
                    </span>
                </div>
                <p class="text-sm text-gray-500 mt-0.5">{{ $formula->nama_formula }}</p>
            </div>
        </div>

        {{-- Error --}}
        @if($errors->any())
            <div class="mb-6 flex items-start gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm">
                <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.formula.update', $formula) }}" method="POST" id="formulaForm">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                {{-- Kiri: Info Formula --}}
                <div class="lg:col-span-4">
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                        <h3 class="text-sm font-semibold text-gray-700 mb-5 flex items-center gap-2">
                            <svg class="w-4 h-4 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Informasi Formula
                        </h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1.5">Kode Formula</label>
                                <input type="text" value="{{ $formula->kode_formula }}" disabled
                                    class="w-full border border-gray-200 bg-gray-50 rounded-lg px-3 py-2.5 text-sm text-gray-500 cursor-not-allowed">
                                <p class="mt-1 text-xs text-gray-400">Kode tidak dapat diubah</p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1.5">
                                    Nama Formula <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="nama_formula"
                                    value="{{ old('nama_formula', $formula->nama_formula) }}"
                                    class="w-full border {{ $errors->has('nama_formula') ? 'border-red-400' : 'border-gray-300' }} rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-orange-400 transition-all"
                                    required>
                                @error('nama_formula')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1.5">Deskripsi</label>
                                <textarea name="deskripsi" rows="4"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-orange-400 transition-all resize-none">{{ old('deskripsi', $formula->deskripsi) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Kanan: Komposisi --}}
                <div class="lg:col-span-8">
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                        <div class="flex items-center justify-between mb-5">
                            <h3 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                                <svg class="w-4 h-4 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                                Komposisi Bahan Baku
                            </h3>
                            <span id="totalBadge" class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-500">
                                Total: 0%
                            </span>
                        </div>

                        <div class="mb-5">
                            <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                                <div id="progressBar" class="h-2 rounded-full transition-all duration-300 bg-gray-300" style="width: 0%"></div>
                            </div>
                            <p id="progressLabel" class="text-xs text-gray-400 mt-1 text-right">0% / 100%</p>
                        </div>

                        <div id="materialRows" class="space-y-2 mb-4">
                            @php $i = 0; @endphp
                            @foreach($formula->materials as $material)
                            <div class="material-row flex items-center gap-2">
                                <div class="flex-1">
                                    <select name="materials[{{ $i }}][material_id]"
                                        class="material-select w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-orange-400 transition-all" required>
                                        <option value="">-- Pilih Bahan --</option>
                                        @foreach($materials as $m)
                                            <option value="{{ $m->id }}" {{ $material->id == $m->id ? 'selected' : '' }}>{{ $m->nama_bahan }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="w-28">
                                    <div class="relative">
                                        <input type="number" name="materials[{{ $i }}][persentase]"
                                            class="persentase-input w-full border border-gray-300 rounded-lg pl-3 pr-7 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-orange-400 transition-all"
                                            step="0.01" min="0.01" max="100"
                                            value="{{ $material->pivot->persentase }}" required>
                                        <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-xs text-gray-400">%</span>
                                    </div>
                                </div>
                                <button type="button" class="btn-remove flex-shrink-0 p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                            @php $i++; @endphp
                            @endforeach
                        </div>

                        <button type="button" id="btnTambahBahan"
                            class="inline-flex items-center gap-2 text-sm text-orange-600 hover:text-orange-700 font-medium px-3 py-2 rounded-lg hover:bg-orange-50 transition-colors">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                            </svg>
                            Tambah Bahan
                        </button>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-3">
                <a href="{{ route('admin.formula.index') }}"
                    class="px-5 py-2.5 text-sm font-medium text-gray-600 hover:text-gray-800 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Batal
                </a>
                <button type="submit"
                    class="inline-flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white font-semibold px-6 py-2.5 rounded-lg shadow text-sm transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Perubahan
                </button>
            </div>
        </form>

        <template id="rowTemplate">
            <div class="material-row flex items-center gap-2">
                <div class="flex-1">
                    <select name="materials[__IDX__][material_id]"
                        class="material-select w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-orange-400 transition-all" required>
                        <option value="">-- Pilih Bahan --</option>
                        @foreach($materials as $m)
                            <option value="{{ $m->id }}">{{ $m->nama_bahan }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-28">
                    <div class="relative">
                        <input type="number" name="materials[__IDX__][persentase]"
                            class="persentase-input w-full border border-gray-300 rounded-lg pl-3 pr-7 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-orange-400 transition-all"
                            step="0.01" min="0.01" max="100" placeholder="0" required>
                        <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-xs text-gray-400">%</span>
                    </div>
                </div>
                <button type="button" class="btn-remove flex-shrink-0 p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </div>
        </template>
    </div>

    @push('scripts')
    <script>
    let rowIdx = {{ $formula->materials->count() }};

    function updateTotal() {
        let total = 0;
        document.querySelectorAll('.persentase-input').forEach(i => total += parseFloat(i.value) || 0);
        total = Math.round(total * 100) / 100;

        const bar   = document.getElementById('progressBar');
        const badge = document.getElementById('totalBadge');
        const label = document.getElementById('progressLabel');

        bar.style.width = Math.min(total, 100) + '%';
        label.textContent = total + '% / 100%';

        if (total === 100) {
            bar.className   = 'h-2 rounded-full transition-all duration-300 bg-green-500';
            badge.className = 'inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700';
            badge.textContent = 'Total: ' + total + '% ✓';
        } else if (total > 100) {
            bar.className   = 'h-2 rounded-full transition-all duration-300 bg-red-500';
            badge.className = 'inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700';
            badge.textContent = 'Total: ' + total + '% ✗';
        } else {
            bar.className   = 'h-2 rounded-full transition-all duration-300 bg-orange-400';
            badge.className = 'inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-700';
            badge.textContent = 'Total: ' + total + '%';
        }
    }

    function updateRemoveButtons() {
        const rows = document.querySelectorAll('.material-row');
        rows.forEach(row => {
            const btn = row.querySelector('.btn-remove');
            if (rows.length === 1) {
                btn.disabled = true;
                btn.className = 'btn-remove flex-shrink-0 p-2 text-gray-300 rounded-lg cursor-not-allowed';
            } else {
                btn.disabled = false;
                btn.className = 'btn-remove flex-shrink-0 p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors';
            }
        });
    }

    document.getElementById('btnTambahBahan').addEventListener('click', function () {
        const tpl = document.getElementById('rowTemplate').innerHTML.replace(/__IDX__/g, rowIdx++);
        const div = document.createElement('div');
        div.innerHTML = tpl.trim();
        const row = div.firstChild;
        row.querySelector('.btn-remove').addEventListener('click', () => { row.remove(); updateRemoveButtons(); updateTotal(); });
        row.querySelector('.persentase-input').addEventListener('input', updateTotal);
        document.getElementById('materialRows').appendChild(row);
        updateRemoveButtons();
    });

    document.querySelectorAll('.btn-remove').forEach(btn => {
        btn.addEventListener('click', function () {
            this.closest('.material-row').remove();
            updateRemoveButtons();
            updateTotal();
        });
    });

    document.querySelectorAll('.persentase-input').forEach(i => i.addEventListener('input', updateTotal));

    document.getElementById('formulaForm').addEventListener('submit', function(e) {
        let total = 0;
        document.querySelectorAll('.persentase-input').forEach(i => total += parseFloat(i.value) || 0);
        if (Math.round(total * 100) / 100 !== 100) {
            e.preventDefault();
            alert('Total persentase bahan harus tepat 100%. Saat ini: ' + total + '%');
        }
    });

    updateTotal();
    updateRemoveButtons();
    </script>
    @endpush
</x-admin-app-layout>