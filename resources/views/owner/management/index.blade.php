<x-owner-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">{{ __('Owner - Master Data Admin') }}</h2>
    </x-slot>

    <div class="px-4 sm:px-6 lg:px-8 mt-10">
        
        <div class="mb-4">
            <h3 class="text-lg font-bold text-gray-800">Daftar Admin Sistem</h3>
            <p class="text-sm text-gray-500">Berikut adalah daftar admin yang mengelola sistem SI MBEK.</p>
        </div>

        <div class="flex flex-col mt-4">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-300 shadow-sm rounded-lg overflow-hidden border border-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">#</th>
                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Nama Lengkap</th>
                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Email</th>
                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status Role</th>
                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Terdaftar Pada</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse ($admins as $index => $admin)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-400">{{ $index + 1 }}</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm font-medium text-gray-900">{{ $admin->name }}</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $admin->email }}</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    <span class="px-2 py-1 rounded text-xs bg-blue-100 text-blue-800 uppercase font-semibold">
                                        {{ $admin->role }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    {{ $admin->created_at->format('d M Y') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-8 text-gray-500">Belum ada admin yang terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-owner-app-layout>