<x-admin-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">{{ __('Admin - Master Produk') }}</h2>
    </x-slot>

    <div class="px-4 sm:px-6 lg:px-8 mt-10">
        <button class="mt-8 bg-brand-orange hover:bg-orange-700 p-3 rounded-md mb-2 text-white">
            <a href="{{ route('admin.products.create') }}">Tambah Produk</a>
        </button>

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
                <a href="{{ route('admin.products.update', $p->id) }}" class="bg-blue-600 text-white p-2 rounded hover:bg-blue-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </a>
                <form action="{{ route('admin.products.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Hapus produk ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-600 text-white p-2 rounded hover:bg-red-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </form>
            </td>
        </tr>
    @endforeach
</tbody>
                </table>
            </div>
        </div>
    </div>
</x-admin-app-layout>