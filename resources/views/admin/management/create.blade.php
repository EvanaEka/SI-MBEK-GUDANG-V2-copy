<x-admin-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">{{ __('Tambah Admin Baru') }}</h2>
    </x-slot>

    <div class="container mx-auto mt-10 px-4">
        
        @if ($errors->any())
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.admins.store') }}" method="POST" class="bg-white shadow-md rounded-lg p-8 border border-gray-200">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Kolom Kiri --}}
                <div>
                    <label class="block font-bold mb-2">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="w-full border-gray-300 rounded focus:ring-orange-500" placeholder="Masukkan nama lengkap" required>
                    
                    <label class="block font-bold mt-4 mb-2">Alamat Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="w-full border-gray-300 rounded focus:ring-orange-500" placeholder="admin@contoh.com" required>
                </div>

                {{-- Kolom Kanan --}}
                <div>
                    <label class="block font-bold mb-2">Password Sementara</label>
                    <input type="password" name="password" autocomplete="new-password" class="w-full border-gray-300 rounded focus:ring-orange-500" placeholder="Minimal 8 karakter" required>
                    <p class="text-xs text-gray-500 mt-1 mb-3">Admin baru akan diminta untuk mengubah password saat login pertama kali.</p>
                    
                    <label class="block font-bold mb-2">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" autocomplete="new-password" class="w-full border-gray-300 rounded focus:ring-orange-500" placeholder="Ketik ulang password" required>
                </div>
            </div>
            
            <div class="mt-8">
                <button type="submit" class="bg-brand-orange text-white px-6 py-2 rounded font-bold hover:bg-orange-700">Simpan Admin</button>
                <a href="{{ route('admin.admins.index') }}" class="ml-4 text-gray-600 hover:text-gray-900 font-medium">Batal</a>
            </div>
        </form>
    </div>
</x-admin-app-layout>