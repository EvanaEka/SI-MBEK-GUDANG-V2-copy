<x-owner-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-black leading-tight">
            Detail Produk: {{ $product->nama }}
        </h2>
    </x-slot>

    <div class="container mx-auto mt-10 px-4" x-data="{ editMode: false }">
        <form action="{{ route('owner.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="bg-white shadow-lg rounded-lg overflow-hidden border border-gray-200">
                {{-- Header Card --}}
                <div class="p-6 bg-brand-orange text-white flex justify-between items-center">
                    <h3 class="text-lg font-bold text-white">Informasi Master Produk</h3>
                    <div class="flex gap-2">
                        <a href="{{ route('owner.products.index') }}" x-show="!editMode" class="bg-white text-brand-orange px-4 py-1 rounded text-sm font-bold shadow hover:bg-white-100"> 
                            Kembali 
                        </a>
                    </div>
                </div>
                
                {{-- Content Card --}}
                <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                    <div>
                        {{-- Foto View --}}
                       <div class="mb-6 flex justify-center bg-gray-50 p-4 rounded-lg">
   <img src="{{ $product->image ? asset($product->image) : asset('logo/logosimbek.png') }}" 
     class="h-40 w-auto rounded shadow-sm object-cover"
     onerror="this.onerror=null; this.src='{{ asset('logo/logosimbek.png') }}';">
</div>
                         <div class="mb-4">
                            <p class="text-xs text-black font-bold uppercase mb-1">Kode</p>
                        </div>

                        <div class="mb-4">
                            <p class="text-xs text-black font-bold uppercase mb-1">Nama Produk</p>
                        </div>
                    </div>

                    <div>
                        <div class="mb-4">
                            <p class="text-xs text-black font-bold uppercase mb-1">Stok Sistem Saat Ini</p>
                            <p class="text-3xl font-bold text-orange-600">{{ $product->stok }} <span class="text-sm text-gray-500">Unit</span></p>
                        </div>

                        <div class="mb-4">
                            <p class="text-xs text-black font-bold uppercase mb-1">Tipe</p>
                        </div>

                        <div class="mb-4">
                            <p class="text-xs text-black font-bold uppercase mb-1">Sumber</p>
                        </div>

                        <div class="mb-4">
                            <p class="text-xs text-black font-bold uppercase mb-1">Harga Jual (Rp)</p>
                        </div>

                        <div class="mb-4">
                            <p class="text-xs text-black font-bold uppercase mb-1">Resep / Formula</p>
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
</x-owner-app-layout>