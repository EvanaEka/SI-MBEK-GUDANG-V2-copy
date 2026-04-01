<x-owner-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-black leading-tight">
            Detail Material: {{ $material->nama_bahan }}
        </h2>
    </x-slot>

    <div class="container mx-auto mt-10 px-4" x-data="{ editMode: false }">
        <form action="{{ route('owner.materials.update', $material->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="bg-white shadow-lg rounded-lg overflow-hidden border border-gray-200">
                {{-- Header Card --}}
                <div class="p-6 bg-brand-orange text-white flex justify-between items-center">
                    <h3 class="text-lg font-bold text-white">Informasi Master Material</h3>
                    <div class="flex gap-2">

                        <a href="{{ route('owner.materials.index') }}" x-show="!editMode" class="bg-white text-brand-orange px-4 py-1 rounded text-sm font-bold shadow hover:bg-white-100"> 
                            Kembali 
                        </a>
                    </div>
                </div>
                
                {{-- Content Card --}}
                <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                    <div>
                        <div class="mb-4">
                            <p class="text-xs text-black font-bold uppercase mb-1">Nama Bahan Baku</p>
                            {{-- Teks (Mode Lihat) --}}
                            <p x-show="!editMode" class="text-lg text-gray-800 font-semibold">{{ $material->nama_bahan }}</p>
                            {{-- Input (Mode Edit) --}}
                            <input x-show="editMode" type="text" name="nama_bahan" value="{{ $material->nama_bahan }}" class="w-full border-gray-300 rounded focus:ring-orange-500 text-black">
                        </div>

                        <div class="mb-4">
                            <p class="text-xs text-black font-bold uppercase mb-1">Kategori</p>
                            <p x-show="!editMode" class="text-lg text-gray-800 uppercase">{{ $material->kategori }}</p>
                            <select x-show="editMode" name="kategori" class="w-full border-gray-300 rounded text-black">
                                <option value="pakan" {{ $material->kategori == 'pakan' ? 'selected' : '' }}>PAKAN</option>
                                <option value="obat" {{ $material->kategori == 'obat' ? 'selected' : '' }}>OBAT</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <p class="text-xs text-black font-bold uppercase mb-1">Satuan</p>
                            <p x-show="!editMode" class="text-lg text-gray-800">{{ $material->satuan }}</p>
                            <input x-show="editMode" type="text" name="satuan" value="{{ $material->satuan }}" class="w-full border-gray-300 rounded text-black">
                        </div>
                    </div>

                    <div>
                        <div class="mb-4">
                            <p class="text-xs text-black font-bold uppercase mb-1">Stok Sistem Saat Ini</p>
                            <p class="text-3xl font-bold text-orange-600">{{ $material->stok }} <span class="text-sm text-gray-500">{{ $material->satuan }}</span></p>
                            <p class="text-xs text-red-500 mt-1">* Stok hanya bisa berubah melalui transaksi/adjustment</p>
                        </div>
                        
                        <div class="mb-4">
                            <p class="text-xs text-black font-bold uppercase mb-1">Deskripsi / Catatan</p>
                            <p x-show="!editMode" class="text-gray-700 italic">{{ $material->deskripsi ?? '-' }}</p>
                            <textarea x-show="editMode" name="deskripsi" class="w-full border-gray-300 rounded text-black">{{ $material->deskripsi }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Footer Info --}}
                <div class="px-8 py-4 bg-gray-50 border-t border-gray-100 flex justify-between text-xs text-gray-400">
                    <span>Dibuat pada: {{ $material->created_at->format('d M Y H:i') }}</span>
                    <span>Terakhir diperbarui: {{ $material->updated_at->format('d M Y H:i') }}</span>
                </div>
            </div>
        </form>
    </div>
</x-owner-app-layout>