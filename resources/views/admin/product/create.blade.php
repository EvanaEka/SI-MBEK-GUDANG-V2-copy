<x-admin-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">{{ __('Tambah Produk Baru') }}</h2>
    </x-slot>

    <div class="container mx-auto mt-10 px-4">
        <form action="{{ route('admin.products.store') }}" method="POST" class="bg-white shadow-md rounded-lg p-8 border border-gray-200">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block font-bold mb-2">Kode Produk</label>
                    <input type="text" name="kode" class="w-full border-gray-300 rounded" placeholder="PKN-001" required>

                    <label class="block font-bold mt-4 mb-2">Nama Produk</label>
                    <input type="text" name="nama" class="w-full border-gray-300 rounded" placeholder="Pakan Penggemukan A" required>

                    <label class="block font-bold mt-4 mb-2">Sumber Produk</label>
                    <select name="source" class="w-full border-gray-300 rounded" required>
                        <option value="produksi">Hasil Produksi Sendiri</option>
                        <option value="pembelian">Beli Jadi</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold mb-2">Tipe</label>
                    <select name="type" class="w-full border-gray-300 rounded" required>
                        <option value="pakan">Pakan</option>
                        <option value="obat">Obat</option>
                    </select>

                    <label class="block font-bold mt-4 mb-2">Harga Jual (Rp)</label>
                    <input type="number" name="harga" class="w-full border-gray-300 rounded" placeholder="0">

                    <label class="block font-bold mt-4 mb-2">Pilih Formula (Jika Hasil Produksi)</label>
                    <select name="formula_id" class="w-full border-gray-300 rounded">
                        <option value="">-- Tanpa Formula --</option>
                        @foreach($formulas as $f)
                            <option value="{{ $f->id }}">{{ $f->nama_formula }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="mt-8">
                <button type="submit" class="bg-brand-orange text-white px-6 py-2 rounded font-bold hover:bg-orange-700">Simpan Produk</button>
            </div>
        </form>
    </div>
</x-admin-app-layout>