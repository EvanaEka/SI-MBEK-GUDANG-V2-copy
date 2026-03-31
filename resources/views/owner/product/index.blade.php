<x-owner-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">{{ __('Owner - Master Produk') }}</h2>
    </x-slot>

    <div class="px-4 sm:px-6 lg:px-8 mt-10">
        <div class="flex flex-col">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-300">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Kode</th>
                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Nama Produk</th>
                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Tipe</th>
                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Sumber</th>
                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Harga Jual</th>
                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Aksi</th>
                        </tr>
                    </thead>
                   <tbody class="divide-y divide-gray-200 bg-white">
    @foreach ($products as $p)
        <tr>
            <td class="whitespace-nowrap px-3 py-4 text-sm font-medium text-gray-900">{{ $p->kode }}</td>
            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $p->nama }}</td>
            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 uppercase">{{ $p->type }}</td>
            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                <span class="px-2 py-1 rounded text-xs {{ $p->source == 'produksi' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                    {{ ucfirst($p->source) }}
                </span>
            </td>
            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">Rp {{ number_format($p->harga, 0, ',', '.') }}</td>
            <td class="whitespace-nowrap px-3 py-4 text-sm flex gap-2">
                <a href="{{ route('owner.products.update', $p->id) }}" class="bg-blue-600 text-white p-2 rounded hover:bg-blue-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </a>
            </td>
        </tr>
    @endforeach
</tbody>
                </table>
            </div>
        </div>
    </div>
</x-owner-app-layout>