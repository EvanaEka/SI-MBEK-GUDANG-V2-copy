<x-owner-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-black leading-tight">{{ __('Owner - Master Supplier') }}</h2>
    </x-slot>

    <div class="px-4 sm:px-6 lg:px-8 mt-10">
        <div class="flex flex-col">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-300">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-black">Nama Supplier</th>
                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-black">Kontak</th>
                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-black">Kota</th>
                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-black">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @foreach ($suppliers as $s)
                            <tr>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-900 font-medium">{{ $s->nama_supplier }}</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $s->kontak ?? '-' }}</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $s->kota ?? '-' }}</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm flex gap-2">
                                    <a href="{{ route('owner.suppliers.show', $s->id) }}" class="bg-blue-600 text-white p-2 rounded hover:bg-blue-700">
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