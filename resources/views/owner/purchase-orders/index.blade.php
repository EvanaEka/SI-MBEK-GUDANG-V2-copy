<x-owner-app-layout>
    <div class="p-6 max-w-7xl mx-auto">

        {{-- Header --}}
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Purchase Order</h1>
                <p class="text-sm text-gray-500 mt-1">Kelola pemesanan bahan baku & produk</p>
            </div>
            <a href="{{ route('owner.purchase-orders.create') }}"
                class="inline-flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white font-semibold px-5 py-2.5 rounded-lg shadow transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Buat PO Baru
            </a>
        </div>

        {{-- Alert --}}
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

        {{-- Stats Cards --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
            @php
                $total    = $purchaseOrders->count();
                $draft    = $purchaseOrders->where('status','draft')->count();
                $dipesan  = $purchaseOrders->where('status','dipesan')->count();
                $diterima = $purchaseOrders->where('status','diterima')->count();
            @endphp
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Total PO</p>
                <p class="text-3xl font-bold text-gray-800 mt-1">{{ $total }}</p>
            </div>
            <div class="bg-white rounded-xl border border-yellow-100 shadow-sm p-4">
                <p class="text-xs text-yellow-600 font-medium uppercase tracking-wide">Draft</p>
                <p class="text-3xl font-bold text-yellow-500 mt-1">{{ $draft }}</p>
            </div>
            <div class="bg-white rounded-xl border border-blue-100 shadow-sm p-4">
                <p class="text-xs text-blue-600 font-medium uppercase tracking-wide">Dipesan</p>
                <p class="text-3xl font-bold text-blue-500 mt-1">{{ $dipesan }}</p>
            </div>
            <div class="bg-white rounded-xl border border-green-100 shadow-sm p-4">
                <p class="text-xs text-green-600 font-medium uppercase tracking-wide">Diterima</p>
                <p class="text-3xl font-bold text-green-500 mt-1">{{ $diterima }}</p>
            </div>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-5 py-3.5 font-semibold text-gray-600">Kode PO</th>
                            <th class="px-5 py-3.5 font-semibold text-gray-600">Supplier</th>
                            <th class="px-5 py-3.5 font-semibold text-gray-600">Tipe</th>
                            <th class="px-5 py-3.5 font-semibold text-gray-600">Dipesan Oleh</th>
                            <th class="px-5 py-3.5 font-semibold text-gray-600">Tgl Pesan</th>
                            <th class="px-5 py-3.5 font-semibold text-gray-600">Tgl Diterima</th>
                            <th class="px-5 py-3.5 font-semibold text-gray-600">Status</th>
                            <th class="px-5 py-3.5 font-semibold text-gray-600 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($purchaseOrders as $po)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-4 font-mono text-orange-600 font-semibold text-xs">{{ $po->kode_po }}</td>
                                <td class="px-5 py-4 text-gray-800">{{ $po->supplier->nama_supplier ?? '-' }}</td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium
                                        {{ $po->type === 'material' ? 'bg-purple-50 text-purple-700' : 'bg-cyan-50 text-cyan-700' }}">
                                        {{ ucfirst($po->type ?? '-') }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-gray-700">{{ $po->dipesanOleh->name ?? '-' }}</td>
                                {{-- Tanggal Pesan --}}
            <td class="px-5 py-4 text-gray-600">
                {{ \Carbon\Carbon::parse($po->tanggal_pesan)->format('d/m/y') }}
            </td>
                               {{-- Tanggal Diterima  --}}
            <td class="px-5 py-4 text-gray-600 font-medium">
                {{ $po->tanggal_diterima ? \Carbon\Carbon::parse($po->tanggal_diterima)->format('d/m/y') : '-' }}
            </td>
                                <td class="px-5 py-4">
                                    @php
                                        $statusConfig = [
                                            'draft'    => 'bg-yellow-100 text-yellow-700',
                                            'dipesan'  => 'bg-blue-100 text-blue-700',
                                            'diterima' => 'bg-green-100 text-green-700',
                                        ];
                                        $cls = $statusConfig[$po->status] ?? 'bg-gray-100 text-gray-600';
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $cls }}">
                                        {{ ucfirst($po->status) }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <a href="{{ route('owner.purchase-orders.show', $po->id) }}"
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
                                        <svg class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <p class="text-sm font-medium">Belum ada Purchase Order</p>
                                        <a href="{{ route('owner.purchase-orders.create') }}" class="text-orange-500 hover:underline text-sm">Buat PO pertama</a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-owner-app-layout>