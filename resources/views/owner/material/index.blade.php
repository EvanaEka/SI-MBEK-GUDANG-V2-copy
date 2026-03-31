<x-owner-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">{{ __('Owner - Master Material') }}</h2>
    </x-slot>

    <div class="px-4 sm:px-6 lg:px-8 mt-10">
        <div class="flex flex-col">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-300">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Nama Bahan</th>
                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Kategori</th>
                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Satuan</th>
                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Deskripsi</th>
                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Aksi</th>
                        </tr>
                    </thead>
                   <tbody class="divide-y divide-gray-200 bg-white">
    @foreach ($materials as $m)
        <tr>
            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-900">{{ $m->nama_bahan }}</td>
            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 uppercase">{{ $m->kategori }}</td>
            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $m->satuan }}</td>
            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $m->deskripsi }}</td>
            <td class="whitespace-nowrap px-3 py-4 text-sm flex gap-2">
                {{-- Icon Edit --}}
                <a href="{{ route('owner.materials.update', $m->id) }}" class="bg-blue-600 text-white p-2 rounded hover:bg-blue-700">
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