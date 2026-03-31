<x-admin-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-black leading-tight">
            Detail Produk: {{ $product->nama }}
        </h2>
    </x-slot>

    <div class="container mx-auto mt-10 px-4" x-data="{ editMode: false }">
        <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="bg-white shadow-lg rounded-lg overflow-hidden border border-gray-200">
                {{-- Header Card --}}
                <div class="p-6 bg-brand-orange text-white flex justify-between items-center">
                    <h3 class="text-lg font-bold text-white">Informasi Master Produk</h3>
                    <div class="flex gap-2">
                        <button type="button" x-show="!editMode" @click="editMode = true"
                            class="bg-blue-600 text-white px-4 py-1 rounded text-sm font-bold shadow hover:bg-blue-700">
                            Edit Data
                        </button>

                        <button type="button" x-show="editMode" @click="editMode = false"
                            class="bg-gray-500 text-white px-4 py-1 rounded text-sm font-bold shadow hover:bg-gray-600">
                            Batal
                        </button>
                        <button type="submit" x-show="editMode"
                            class="bg-green-600 text-white px-4 py-1 rounded text-sm font-bold shadow hover:bg-green-700">
                            Simpan Perubahan
                        </button>

                        <a href="{{ route('admin.products.index') }}" x-show="!editMode"
                            class="bg-white text-brand-orange px-4 py-1 rounded text-sm font-bold shadow hover:bg-white-100">
                            Kembali
                        </a>
                    </div>
                </div>

                {{-- Content Card --}}
                <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                    <div>
                        {{-- Foto View --}}
                        <div class="mb-6 flex justify-center bg-gray-50 p-4 rounded-lg">
                            <img src="{{ $product->image_url }}">
                        </div>

                        <div class="mb-4">
                            <p class="text-xs text-black font-bold uppercase mb-1">Kode</p>
                            <p x-show="!editMode" class="text-lg text-gray-800 font-semibold">{{ $product->kode }}</p>
                            <input x-show="editMode" type="text" name="kode" value="{{ $product->kode }}"
                                class="w-full border-gray-300 rounded focus:ring-orange-500 text-black">
                        </div>

                        <div class="mb-4">
                            <p class="text-xs text-black font-bold uppercase mb-1">Nama Produk</p>
                            <p x-show="!editMode" class="text-lg text-gray-800 font-semibold">{{ $product->nama }}</p>
                            <input x-show="editMode" type="text" name="nama" value="{{ $product->nama }}"
                                class="w-full border-gray-300 rounded focus:ring-orange-500 text-black">
                        </div>

                        <div class="mb-4" x-show="editMode">
                            <p class="text-xs text-black font-bold uppercase mb-1">Ganti Foto Produk</p>
                            <input type="file" name="image" class="w-full border-gray-300 rounded p-1 text-black"
                                accept="image/*">
                        </div>
                    </div>

                    <div>
                        <div class="mb-4">
                            <p class="text-xs text-black font-bold uppercase mb-1">Stok Sistem Saat Ini</p>
                            <p class="text-3xl font-bold text-orange-600">{{ $product->stok }} <span
                                    class="text-sm text-gray-500">Unit</span></p>
                        </div>

                        <div class="mb-4">
                            <p class="text-xs text-black font-bold uppercase mb-1">Tipe</p>
                            <p x-show="!editMode" class="text-lg text-gray-800 uppercase">{{ $product->type }}</p>
                            <select x-show="editMode" name="type" class="w-full border-gray-300 rounded text-black">
                                <option value="pakan" {{ $product->type == 'pakan' ? 'selected' : '' }}>PAKAN</option>
                                <option value="obat" {{ $product->type == 'obat' ? 'selected' : '' }}>OBAT</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <p class="text-xs text-black font-bold uppercase mb-1">Sumber</p>
                            <p x-show="!editMode" class="text-lg text-gray-800 uppercase">{{ $product->source }}</p>
                            <select x-show="editMode" name="source" class="w-full border-gray-300 rounded text-black">
                                <option value="pembelian" {{ $product->source == 'pembelian' ? 'selected' : '' }}>
                                    Pembelian</option>
                                <option value="produksi" {{ $product->source == 'produksi' ? 'selected' : '' }}>Produksi
                                </option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <p class="text-xs text-black font-bold uppercase mb-1">Deskripsi Produk</p>
                            <p x-show="!editMode" class="text-sm text-gray-600 italic whitespace-pre-wrap">{{ $product->deskripsi ?? 'Tidak ada deskripsi' }}</p>
                            <textarea x-show="editMode" name="deskripsi" rows="3" class="w-full border-gray-300 rounded focus:ring-orange-500 text-black">{{ $product->deskripsi }}</textarea>
                        </div>

                        <div class="mb-4">
                            <p class="text-xs text-black font-bold uppercase mb-1">Harga Jual (Rp)</p>
                            <p x-show="!editMode" class="text-lg text-gray-800">Rp
                                {{ number_format($product->harga, 0, ',', '.') }}
                            </p>
                            <input x-show="editMode" type="number" name="harga" value="{{ $product->harga }}"
                                class="w-full border-gray-300 rounded text-black">
                        </div>

                        <div class="mb-4">
                            <p class="text-xs text-black font-bold uppercase mb-1">Resep / Formula</p>
                            <p x-show="!editMode" class="text-lg text-gray-800">
                                {{ $product->formula->nama_formula ?? '-- Tanpa Formula --' }}
                            </p>
                            <select x-show="editMode" name="formula_id"
                                class="w-full border-gray-300 rounded text-black">
                                <option value="">-- Tanpa Formula --</option>
                                @foreach($formulas as $f)
                                    <option value="{{ $f->id }}" {{ $product->formula_id == $f->id ? 'selected' : '' }}>
                                        {{ $f->nama_formula }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Footer Info --}}
                <div class="px-8 py-4 bg-gray-50 border-t border-gray-100 flex justify-between text-xs text-gray-400">
                    <span>Dibuat pada: {{ $product->created_at->format('d M Y H:i') }}</span>
                    <span>Terakhir diperbarui: {{ $product->updated_at->format('d M Y H:i') }}</span>
                </div>
            </div>
        </form>
    </div>
</x-admin-app-layout>