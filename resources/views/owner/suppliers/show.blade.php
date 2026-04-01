<x-owner-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-black leading-tight">Detail Supplier: {{ $supplier->nama_supplier }}</h2>
    </x-slot>

    <div class="container mx-auto mt-10 px-4" x-data="{ editMode: false }">
        <form action="{{ route('owner.suppliers.update', $supplier->id) }}" method="POST">
            @csrf @method('PUT')

            <div class="bg-white shadow-lg rounded-lg overflow-hidden border border-gray-200">
                <div class="p-6 bg-brand-orange text-white flex justify-between items-center">
                    <h3 class="text-lg font-bold text-white">Informasi Supplier</h3>
                    <div class="flex gap-2 text-black">
                        <a href="{{ route('owner.suppliers.index') }}" x-show="!editMode" class="bg-white text-brand-orange px-4 py-1 rounded text-sm font-bold shadow">Kembali</a>
                    </div>
                </div>
                
                <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                    <div>
                        <div class="mb-4">
                            <p class="text-xs text-black font-bold uppercase mb-1">Nama Supplier</p>
                            <p x-show="!editMode" class="text-lg text-gray-800 font-semibold">{{ $supplier->nama_supplier }}</p>
                            <input x-show="editMode" type="text" name="nama_supplier" value="{{ $supplier->nama_supplier }}" class="w-full border-gray-300 rounded text-black">
                        </div>
                        <div class="mb-4">
                            <p class="text-xs text-black font-bold uppercase mb-1">Kontak</p>
                            <p x-show="!editMode" class="text-lg text-gray-800">{{ $supplier->kontak ?? '-' }}</p>
                            <input x-show="editMode" type="text" name="kontak" value="{{ $supplier->kontak }}" class="w-full border-gray-300 rounded text-black">
                        </div>
                        <div class="mb-4">
                            <p class="text-xs text-black font-bold uppercase mb-1">Alamat</p>
                            <p x-show="!editMode" class="text-gray-700 italic">{{ $supplier->alamat ?? '-' }}</p>
                            <textarea x-show="editMode" name="alamat" class="w-full border-gray-300 rounded text-black">{{ $supplier->alamat }}</textarea>
                        </div>
                    </div>
                    <div>
                        <div class="mb-4">
                            <p class="text-xs text-black font-bold uppercase mb-1">Kota / Provinsi</p>
                            <p x-show="!editMode" class="text-lg text-gray-800">{{ $supplier->kota }}, {{ $supplier->provinsi }}</p>
                            <div x-show="editMode" class="flex gap-2">
                                <input type="text" name="kota" value="{{ $supplier->kota }}" class="w-1/2 border-gray-300 rounded text-black" placeholder="Kota">
                                <input type="text" name="provinsi" value="{{ $supplier->provinsi }}" class="w-1/2 border-gray-300 rounded text-black" placeholder="Provinsi">
                            </div>
                        </div>
                        <div class="mb-4">
                            <p class="text-xs text-black font-bold uppercase mb-1">Catatan</p>
                            <p x-show="!editMode" class="text-gray-700 italic">{{ $supplier->catatan ?? '-' }}</p>
                            <textarea x-show="editMode" name="catatan" class="w-full border-gray-300 rounded text-black">{{ $supplier->catatan }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-owner-app-layout>