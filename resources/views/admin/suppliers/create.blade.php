<x-admin-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-black leading-tight">{{ __('Tambah Supplier Baru') }}</h2>
    </x-slot>

    <div class="container mx-auto mt-10 px-4">
        <form action="{{ route('admin.suppliers.store') }}" method="POST" class="bg-white shadow-md rounded-lg p-8 border border-gray-200">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block font-bold mb-2 text-black text-sm">Nama Supplier</label>
                    <input type="text" name="nama_supplier" class="w-full border-gray-300 rounded text-sm" required>
                    
                    <label class="block font-bold mt-4 mb-2 text-black text-sm">Kontak (Telp/WA)</label>
                    <input type="text" name="kontak" class="w-full border-gray-300 rounded text-sm">

                    <label class="block font-bold mt-4 mb-2 text-black text-sm">Alamat Lengkap</label>
                    <textarea name="alamat" class="w-full border-gray-300 rounded text-sm" rows="3"></textarea>
                </div>
                <div>
                    <label class="block font-bold mb-2 text-black text-sm">Kota</label>
                    <input type="text" name="kota" class="w-full border-gray-300 rounded text-sm">
                    
                    <label class="block font-bold mt-4 mb-2 text-black text-sm">Provinsi</label>
                    <input type="text" name="provinsi" class="w-full border-gray-300 rounded text-sm">

                    <label class="block font-bold mt-4 mb-2 text-black text-sm">Catatan Tambahan</label>
                    <textarea name="catatan" class="w-full border-gray-300 rounded text-sm" rows="3"></textarea>
                </div>
            </div>
            <div class="mt-8">
                <button type="submit" class="bg-brand-orange text-white px-6 py-2 rounded font-bold hover:bg-orange-700 text-sm">Simpan Supplier</button>
            </div>
        </form>
    </div>
</x-admin-app-layout>