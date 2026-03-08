<x-admin-app-layout>
    <div class="p-6 max-w-7xl mx-auto">

        {{-- Header --}}
        <div class="flex items-start justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Resep Produksi (Formula)</h1>
                <p class="text-sm text-gray-500 mt-1">Kelola komposisi bahan baku untuk setiap formula produk</p>
            </div>
            <a href="{{ route('admin.formula.create') }}"
                class="inline-flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white font-semibold px-5 py-2.5 rounded-lg shadow text-sm transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Formula
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
        @if($errors->has('formula'))
            <div class="mb-6 flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm">
                <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                {{ $errors->first('formula') }}
            </div>
        @endif

        {{-- Stats --}}
        @php
            $totalFormula  = $formulas->count();
            $aktifCount    = $formulas->where('is_active', true)->count();
            $nonaktifCount = $formulas->where('is_active', false)->count();
        @endphp
        <div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-orange-50 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 font-medium">Total Formula</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $totalFormula }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl border border-green-100 shadow-sm p-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-green-50 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-green-600 font-medium">Aktif</p>
                        <p class="text-2xl font-bold text-green-700">{{ $aktifCount }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 font-medium">Nonaktif</p>
                        <p class="text-2xl font-bold text-gray-500">{{ $nonaktifCount }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filter --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 mb-6 flex flex-wrap items-end gap-3">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-medium text-gray-500 mb-1.5">Cari Formula</label>
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                    </svg>
                    <input type="text" id="search-input" placeholder="Kode atau nama formula..."
                        class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent">
                </div>
            </div>
            <div class="min-w-[160px]">
                <label class="block text-xs font-medium text-gray-500 mb-1.5">Status</label>
                <select id="filter-status"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent">
                    <option value="">Semua</option>
                    <option value="aktif">✅ Aktif</option>
                    <option value="nonaktif">🚫 Nonaktif</option>
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
                            <th class="px-5 py-3.5 text-xs font-semibold text-gray-600 uppercase tracking-wide">#</th>
                            <th class="px-5 py-3.5 text-xs font-semibold text-gray-600 uppercase tracking-wide">Kode</th>
                            <th class="px-5 py-3.5 text-xs font-semibold text-gray-600 uppercase tracking-wide">Nama Formula</th>
                            <th class="px-5 py-3.5 text-xs font-semibold text-gray-600 uppercase tracking-wide">Deskripsi</th>
                            <th class="px-5 py-3.5 text-xs font-semibold text-gray-600 uppercase tracking-wide">Bahan Baku</th>
                            <th class="px-5 py-3.5 text-xs font-semibold text-gray-600 uppercase tracking-wide text-center">Status</th>
                            <th class="px-5 py-3.5 text-xs font-semibold text-gray-600 uppercase tracking-wide">Dibuat</th>
                            <th class="px-5 py-3.5 text-xs font-semibold text-gray-600 uppercase tracking-wide text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50" id="formula-tbody">
                        @forelse($formulas as $index => $formula)
                        <tr class="hover:bg-gray-50 transition-colors"
                            data-name="{{ strtolower($formula->kode_formula . ' ' . $formula->nama_formula) }}"
                            data-status="{{ $formula->is_active ? 'aktif' : 'nonaktif' }}">

                            <td class="px-5 py-4 text-xs text-gray-400">{{ $index + 1 }}</td>

                            <td class="px-5 py-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-mono font-semibold bg-gray-100 text-gray-700">
                                    {{ $formula->kode_formula }}
                                </span>
                            </td>

                            <td class="px-5 py-4">
                                <p class="font-semibold text-gray-800">{{ $formula->nama_formula }}</p>
                            </td>

                            <td class="px-5 py-4 max-w-[200px]">
                                <p class="text-xs text-gray-400 truncate">{{ $formula->deskripsi ?? '-' }}</p>
                            </td>

                            <td class="px-5 py-4">
                                @if($formula->materials->count() > 0)
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($formula->materials->take(3) as $material)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-blue-50 text-blue-700">
                                                {{ $material->nama_bahan }} ({{ $material->pivot->persentase }}%)
                                            </span>
                                        @endforeach
                                        @if($formula->materials->count() > 3)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-gray-100 text-gray-500">
                                                +{{ $formula->materials->count() - 3 }} lagi
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-xs text-gray-300">Belum ada bahan</span>
                                @endif
                            </td>

                            <td class="px-5 py-4 text-center">
                                @if($formula->is_active)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-500">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        </svg>
                                        Nonaktif
                                    </span>
                                @endif
                            </td>

                            <td class="px-5 py-4 text-xs text-gray-400">{{ $formula->created_at->format('d M Y') }}</td>

                            <td class="px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('admin.formula.edit', $formula) }}"
                                        class="inline-flex items-center gap-1.5 text-orange-600 hover:text-orange-800 font-medium text-xs transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Edit
                                    </a>
                                    @if($formula->is_active)
                                    <form action="{{ route('admin.formula.destroy', $formula) }}" method="POST"
                                          onsubmit="return confirm('Yakin ingin menonaktifkan formula ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="inline-flex items-center gap-1.5 text-red-500 hover:text-red-700 font-medium text-xs transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                            </svg>
                                            Nonaktifkan
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-5 py-16 text-center">
                                <div class="flex flex-col items-center gap-3 text-gray-400">
                                    <svg class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    <p class="text-sm font-medium">Belum ada formula tersimpan</p>
                                    <p class="text-xs">Klik "Tambah Formula" untuk membuat formula baru</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div id="no-results" class="hidden px-5 py-12 text-center text-sm text-gray-400">
                Tidak ada formula yang sesuai filter.
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const searchInput  = document.getElementById('search-input');
        const filterStatus = document.getElementById('filter-status');
        const resetBtn     = document.getElementById('reset-filter');
        const tbody        = document.getElementById('formula-tbody');
        const noResults    = document.getElementById('no-results');

        function applyFilter() {
            const q = searchInput.value.toLowerCase().trim();
            const status = filterStatus.value;
            let visible = 0;
            tbody.querySelectorAll('tr').forEach(row => {
                const matchName   = !q      || (row.dataset.name   || '').includes(q);
                const matchStatus = !status || row.dataset.status  === status;
                const show = matchName && matchStatus;
                row.classList.toggle('hidden', !show);
                if (show) visible++;
            });
            noResults.classList.toggle('hidden', visible > 0);
        }

        searchInput.addEventListener('input', applyFilter);
        filterStatus.addEventListener('change', applyFilter);
        resetBtn.addEventListener('click', () => {
            searchInput.value = '';
            filterStatus.value = '';
            applyFilter();
        });
    </script>
    @endpush
</x-admin-app-layout>