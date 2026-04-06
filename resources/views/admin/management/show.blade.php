<x-admin-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-black leading-tight">
            Detail Admin: {{ $admin->name }}
        </h2>
    </x-slot>

    <div class="container mx-auto mt-10 px-4" x-data="{ editMode: false }">
        
        @if ($errors->any())
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.admins.update', $admin->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="bg-white shadow-lg rounded-lg overflow-hidden border border-gray-200">
                {{-- Header Card --}}
                <div class="p-6 bg-brand-orange text-white flex justify-between items-center">
                    <h3 class="text-lg font-bold text-white">Informasi Akun Admin</h3>
                    <div class="flex gap-2">
                        {{-- Tombol Edit (Hanya muncul saat Mode Lihat) --}}
                        <button type="button" x-show="!editMode" @click="editMode = true" class="bg-orange-300 text-white px-4 py-1 rounded text-sm font-bold shadow hover:bg-orange-400">
                            Edit Data
                        </button>
                        
                        {{-- Tombol Batal & Simpan (Hanya muncul saat Mode Edit) --}}
                        <button type="button" x-show="editMode" @click="editMode = false" class="bg-orange-300 text-white px-4 py-1 rounded text-sm font-bold shadow hover:bg-orange-400">
                            Batal
                        </button>
                        <button type="submit" x-show="editMode" class="bg-white text-brand-orange px-4 py-1 rounded text-sm font-bold shadow hover:bg-orange-400">
                            Simpan Perubahan
                        </button>

                        <a href="{{ route('admin.admins.index') }}" x-show="!editMode" class="bg-white text-brand-orange px-4 py-1 rounded text-sm font-bold shadow hover:bg-white-100"> 
                            Kembali 
                        </a>
                    </div>
                </div>
                
                {{-- Content Card --}}
                <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                    
                    {{-- Kolom Kiri --}}
                    <div>
                        <div class="mb-4">
                            <p class="text-xs text-black font-bold uppercase mb-1">Nama Lengkap</p>
                            <p x-show="!editMode" class="text-lg text-gray-800 font-semibold">{{ $admin->name }}</p>
                            <input x-show="editMode" type="text" name="name" value="{{ old('name', $admin->name) }}" class="w-full border-gray-300 rounded focus:ring-orange-500 text-black">
                        </div>

                        <div class="mb-4">
                            <p class="text-xs text-black font-bold uppercase mb-1">Status Role</p>
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800 uppercase inline-block mt-1">
                                {{ $admin->role }}
                            </span>
                        </div>
                    </div>

                    {{-- Kolom Kanan --}}
                    <div>
                        <div class="mb-4">
                            <p class="text-xs text-black font-bold uppercase mb-1">Alamat Email</p>
                            <p x-show="!editMode" class="text-lg text-gray-800">{{ $admin->email }}</p>
                            <input x-show="editMode" type="email" name="email" value="{{ old('email', $admin->email) }}" class="w-full border-gray-300 rounded focus:ring-orange-500 text-black">
                            <p x-show="editMode" class="text-xs text-red-500 mt-1">* Pastikan email ini aktif dan valid</p>
                        </div>
                    </div>

                </div>

                {{-- Footer Info --}}
                <div class="px-8 py-4 bg-gray-50 border-t border-gray-100 flex justify-between text-xs text-gray-400">
                    <span>Terdaftar pada: {{ $admin->created_at->format('d M Y H:i') }}</span>
                    <span>Terakhir diperbarui: {{ $admin->updated_at->format('d M Y H:i') }}</span>
                </div>
            </div>
        </form>
    </div>
</x-admin-app-layout>