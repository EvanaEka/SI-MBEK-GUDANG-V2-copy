<x-admin-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">{{ __('Tambah Material Baru') }}</h2>
    </x-slot>

    <div class="container mx-auto mt-10 px-4">
        <form action="{{ route('admin.materials.store') }}" method="POST" class="bg-white shadow-md rounded-lg p-8 border border-gray-200">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block font-bold mb-2">Nama Bahan</label>
                    <input type="text" name="nama_bahan" class="w-full border-gray-300 rounded focus:ring-orange-500" placeholder="Contoh: Jagung Giling" required>
                    
                    <label class="block font-bold mt-4 mb-2">Kategori</label>
                    <select name="kategori" class="w-full border-gray-300 rounded" required>
                        <option value="pakan">Pakan</option>
                        <option value="obat">Obat</option>
                    </select>

                    <label class="block font-bold mt-4 mb-2">Satuan</label>
                    <input type="text" name="satuan" class="w-full border-gray-300 rounded" placeholder="kg, botol, liter" required>
                </div>

                <div>
                    <label class="block font-bold mb-2">Deskripsi</label>
                    <textarea name="deskripsi" class="w-full border-gray-300 rounded" rows="4"></textarea>
                </div>
            </div>
            <div class="mt-8">
                <button type="submit" class="bg-brand-orange text-white px-6 py-2 rounded font-bold hover:bg-orange-700">Simpan Material</button>
            </div>
        </form>
    </div>
</x-admin-app-layout>