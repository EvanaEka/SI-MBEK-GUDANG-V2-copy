<x-admin-app-layout>
    <div class="p-6 max-w-7xl mx-auto">

        {{-- Header --}}
        <div class="flex items-start justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Proses Produksi</h1>
                <p class="text-sm text-gray-500 mt-1">Kelola produksi produk dari formula bahan baku</p>
            </div>
            <a href="{{ route('admin.productions.create') }}"
                class="inline-flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white font-semibold px-5 py-2.5 rounded-lg shadow text-sm transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Buat Produksi
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
        @if(session('warning'))
            <div class="mb-6 flex items-center gap-3 bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-lg text-sm">
                <svg class="w-5 h-5 text-yellow-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                {{ session('warning') }}
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

        {{-- Stats --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-xl border border-yellow-100 shadow-sm p-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-yellow-50 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-yellow-600 font-medium">Sedang Diproses</p>
                        <p class="text-2xl font-bold text-yellow-700">{{ $productions->where('status', 'diproses')->count() }}</p>
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
                        <p class="text-xs text-green-600 font-medium">Selesai</p>
                        <p class="text-2xl font-bold text-green-700">{{ $productions->where('status', 'selesai')->count() }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl border border-red-100 shadow-sm p-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-red-50 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-red-500 font-medium">Ditolak</p>
                        <p class="text-2xl font-bold text-red-600">{{ $productions->where('status', 'rejected')->count() }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-orange-50 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 font-medium">Total Produksi</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $productions->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-5 py-3.5 text-xs font-semibold text-gray-600 uppercase tracking-wide">#</th>
                            <th class="px-5 py-3.5 text-xs font-semibold text-gray-600 uppercase tracking-wide">Produk</th>
                            <th class="px-5 py-3.5 text-xs font-semibold text-gray-600 uppercase tracking-wide">Formula</th>
                            <th class="px-5 py-3.5 text-xs font-semibold text-gray-600 uppercase tracking-wide text-right">Qty</th>
                            <th class="px-5 py-3.5 text-xs font-semibold text-gray-600 uppercase tracking-wide">Tgl Produksi</th>
                            <th class="px-5 py-3.5 text-xs font-semibold text-gray-600 uppercase tracking-wide text-center">Status</th>
                            <th class="px-5 py-3.5 text-xs font-semibold text-gray-600 uppercase tracking-wide text-center">QC</th>
                            <th class="px-5 py-3.5 text-xs font-semibold text-gray-600 uppercase tracking-wide text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($productions as $index => $production)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-5 py-4 text-xs text-gray-400">{{ $index + 1 }}</td>
                            <td class="px-5 py-4">
                                <p class="font-semibold text-gray-800">{{ $production->product->nama ?? '-' }}</p>
                                <p class="text-xs text-gray-400 mt-0.5 font-mono">{{ $production->product->kode ?? '' }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-mono font-semibold bg-gray-100 text-gray-700">
                                    {{ $production->formula->kode_formula ?? '-' }}
                                </span>
                                <p class="text-xs text-gray-400 mt-0.5">{{ $production->formula->nama_formula ?? '' }}</p>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <span class="font-bold text-gray-800">{{ number_format($production->qty_produksi) }}</span>
                                <span class="text-xs text-gray-400 ml-0.5">kg</span>
                            </td>
                            <td class="px-5 py-4 text-xs text-gray-500">
                                {{ \Carbon\Carbon::parse($production->production_date)->format('d M Y') }}
                            </td>
                            <td class="px-5 py-4 text-center">
                                @if($production->status === 'diproses')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Diproses
                                    </span>
                                @elseif($production->status === 'selesai')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                        Selesai
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                                        Ditolak
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-center">
                                @if($production->qc_status === 'layak')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                        Layak {{ $production->qc_percentage ? '('.$production->qc_percentage.'%)' : '' }}
                                    </span>
                                @elseif($production->qc_status === 'tidak_layak')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">Tidak Layak</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-400">Belum QC</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('admin.productions.show', $production) }}"
                                    class="inline-flex items-center gap-1.5 text-orange-600 hover:text-orange-800 font-medium text-xs transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Detail / Aksi
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
                                    <p class="text-sm font-medium">Belum ada data produksi</p>
                                    <a href="{{ route('admin.productions.create') }}" class="text-xs text-orange-500 hover:underline">Buat produksi pertama</a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-admin-app-layout>