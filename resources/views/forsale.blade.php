@php
    use App\Models\Kambing;
    use App\Models\Domba;
    use App\Models\Product;
    use Illuminate\Support\Facades\Storage;

    $kategoriProduk = request('kategori_produk', 'semua');
    $jenisList = [];
    $currentProduk = collect();

    if ($kategoriProduk === 'kambing') {
        $jenisList = Kambing::whereNotNull('type_goat')->distinct()->pluck('type_goat')->toArray();
        $currentProduk = $kambings;
    } elseif ($kategoriProduk === 'domba') {
        $jenisList = Domba::whereNotNull('type_domba')->distinct()->pluck('type_domba')->toArray();
        $currentProduk = $dombas;
    } elseif ($kategoriProduk === 'produk') {
        // Kategori khusus Pakan dan Obat
        $jenisList = Product::whereNotNull('type')->distinct()->pluck('type')->toArray();
        $currentProduk = $products;
    } else {
        // Gabungkan kambing, domba, dan produk untuk kategori "semua"
        $jenisList = array_merge(
            Kambing::whereNotNull('type_goat')->distinct()->pluck('type_goat')->toArray(),
            Domba::whereNotNull('type_domba')->distinct()->pluck('type_domba')->toArray(),
            Product::whereNotNull('type')->distinct()->pluck('type')->toArray()
        );
        $currentProduk = collect()->concat($kambings)->concat($dombas)->concat($products);
    }
@endphp

<x-home-layout>
    <x-navbar-v2 />

    <main class="max-w-7xl mx-auto mt-12 px-4">
        {{-- FILTER ATAS --}}
        @php
            $baseParams = request()->except('kategori_produk', 'jenis', 'page');
        @endphp

        <div class="flex flex-col lg:flex-row gap-6">
            {{-- SIDEBAR --}}
            <aside class="w-full lg:w-1/5">
                <div class="bg-white rounded-xl shadow p-4 sticky top-20">
                    <h2 class="text-lg font-bold mb-4">Etalase Toko ({{ $currentProduk->count() }})</h2>
                    <ul class="space-y-2 text-gray-700 text-sm">
                        {{-- Semua Produk --}}
                        <li>
                            <a href="{{ url()->current() . '?' . http_build_query(array_merge($baseParams, ['kategori_produk' => 'semua'])) }}"
                                class="block px-3 py-2 rounded font-bold {{ $kategoriProduk === 'semua' ? 'bg-brand-orange text-white font-bold' : 'hover:bg-orange-50' }}">
                                Semua Produk
                            </a>
                        </li>
                        
                        {{-- Kambing --}}
                        <li>
                            @php
                                $jenisKambingList = Kambing::where('for_sale', 'yes')
                                    ->whereNotNull('type_goat')
                                    ->distinct()
                                    ->pluck('type_goat');
                                $isKambingActive =
                                    $kategoriProduk === 'kambing' ||
                                    ($kategoriProduk === 'kambing' && request('jenis')) ||
                                    ($kategoriProduk === 'kambing' && $jenisKambingList->contains(request('jenis')));
                            @endphp
                            <a href="{{ url()->current() . '?' . http_build_query(array_merge($baseParams, ['kategori_produk' => 'kambing'])) }}"
                                class="block px-3 py-2 rounded font-bold {{ $isKambingActive ? 'bg-brand-orange text-white font-bold' : 'hover:bg-orange-50' }}">
                                Kambing
                            </a>
                            @if ($jenisKambingList->count())
                                <details class="group mt-1" {{ $isKambingActive && request('jenis') ? 'open' : '' }}>
                                    <summary class="cursor-pointer hover:text-brand-orange py-1 pl-4">
                                        Jenis Kambing
                                    </summary>
                                    <ul class="ml-6 mt-2 space-y-1 text-sm text-gray-600">
                                        @foreach ($jenisKambingList as $jenis)
                                            <li>
                                                <a href="{{ url()->current() . '?' . http_build_query(array_merge($baseParams, ['kategori_produk' => 'kambing', 'jenis' => $jenis])) }}"
                                                    class="block px-2 py-1 rounded {{ $kategoriProduk === 'kambing' && request('jenis') === $jenis ? 'bg-orange-100 text-brand-orange font-bold' : 'hover:bg-orange-50' }}">
                                                    {{ $jenis }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </details>
                            @endif
                        </li>
                        
                        {{-- Domba --}}
                        <li>
                            @php
                                $jenisDombaList = Domba::where('for_sale', 'yes')
                                    ->whereNotNull('type_domba')
                                    ->distinct()
                                    ->pluck('type_domba');
                                $isDombaActive =
                                    $kategoriProduk === 'domba' ||
                                    ($kategoriProduk === 'domba' && request('jenis')) ||
                                    ($kategoriProduk === 'domba' && $jenisDombaList->contains(request('jenis')));
                            @endphp
                            <a href="{{ url()->current() . '?' . http_build_query(array_merge($baseParams, ['kategori_produk' => 'domba'])) }}"
                                class="block px-3 py-2 rounded font-bold {{ $isDombaActive ? 'bg-brand-orange text-white font-bold' : 'hover:bg-orange-50' }}">
                                Domba
                            </a>
                            @if ($jenisDombaList->count())
                                <details class="group mt-1" {{ $isDombaActive && request('jenis') ? 'open' : '' }}>
                                    <summary class="cursor-pointer hover:text-brand-orange py-1 pl-4">
                                        Jenis Domba
                                    </summary>
                                    <ul class="ml-6 mt-2 space-y-1 text-sm text-gray-600">
                                        @foreach ($jenisDombaList as $jenis)
                                            <li>
                                                <a href="{{ url()->current() . '?' . http_build_query(array_merge($baseParams, ['kategori_produk' => 'domba', 'jenis' => $jenis])) }}"
                                                    class="block px-2 py-1 rounded {{ $kategoriProduk === 'domba' && request('jenis') === $jenis ? 'bg-orange-100 text-brand-orange font-bold' : 'hover:bg-orange-50' }}">
                                                    {{ $jenis }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </details>
                            @endif
                        </li>

                        {{-- Produk Pakan/Obat --}}
                        <li>
                            @php
                                $jenisProdukList = Product::whereHas('allocations', function ($q) {
                                        $q->where('type', 'jual')->where('qty', '>', 0);
                                    })
                                    ->whereNotNull('type')
                                    ->distinct()
                                    ->pluck('type');
                                $isProdukActive = $kategoriProduk === 'produk';
                            @endphp
                            <a href="{{ url()->current() . '?' . http_build_query(array_merge($baseParams, ['kategori_produk' => 'produk'])) }}"
                                class="block px-3 py-2 rounded font-bold {{ $isProdukActive ? 'bg-brand-orange text-white font-bold' : 'hover:bg-orange-50' }}">
                                Produk
                            </a>
                            @if ($jenisProdukList->count())
                                <details class="group mt-1" {{ $isProdukActive && request('jenis') ? 'open' : '' }}>
                                    <summary class="cursor-pointer hover:text-brand-orange py-1 pl-4">
                                        Jenis Produk
                                    </summary>
                                    <ul class="ml-6 mt-2 space-y-1 text-sm text-gray-600">
                                        @foreach ($jenisProdukList as $jenis)
                                            <li>
                                                <a href="{{ url()->current() . '?' . http_build_query(array_merge($baseParams, ['kategori_produk' => 'produk', 'jenis' => $jenis])) }}"
                                                    class="block px-2 py-1 rounded {{ $kategoriProduk === 'produk' && request('jenis') === $jenis ? 'bg-orange-100 text-brand-orange font-bold' : 'hover:bg-orange-50' }}">
                                                    {{ ucfirst($jenis) }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </details>
                            @endif
                        </li>
                    </ul>
                </div>
            </aside>

            {{-- KONTEN UTAMA --}}
            <section class="w-full lg:flex-1">
                <form method="GET" class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                    <input type="hidden" name="kategori_produk" value="{{ request('kategori_produk', 'semua') }}">
                    @foreach (request()->except(['kategori_produk', 'sort', 'page', 'harga_min', 'harga_max']) as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach

                    <div class="flex gap-2 w-full md:w-2/3">

                        <input type="text" name="q" value="{{ request('q') }}"
                            class="w-full rounded border-gray-300 focus:ring-2 focus:ring-blue-500"
                            placeholder="Cari...">
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-brand-orange text-white font-semibold rounded hover:bg-orange-700 transition">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <circle cx="11" cy="11" r="8" />
                                <line x1="21" y1="21" x2="16.65" y2="16.65" />
                            </svg>
                        </button>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-2 w-full md:w-auto items-center">
                        <select name="sort" onchange="this.form.submit()"
                            class="rounded border-gray-300 focus:ring-2 focus:ring-blue-500 w-full sm:w-auto">
                            <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>Terbaru</option>
                            <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Terlama</option>
                            <option value="price_low" {{ request('sort') === 'price_low' ? 'selected' : '' }}>Harga Terendah</option>
                            <option value="price_high" {{ request('sort') === 'price_high' ? 'selected' : '' }}>Harga Tertinggi</option>
                        </select>
                        <div class="flex items-center gap-1 bg-gray-50 px-2 py-1 rounded border border-gray-200 w-full sm:w-auto min-w-0">
                            <label class="text-gray-500 text-sm whitespace-nowrap">Harga</label>
                            <input type="text" name="harga_min" value="{{ request('harga_min') }}"
                                class="flex-1 min-w-0 rounded border-gray-300 focus:ring-2 focus:ring-blue-500 px-2"
                                placeholder="Min" pattern="[0-9]*" inputmode="numeric"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            <span class="text-gray-400">-</span>
                            <input type="text" name="harga_max" value="{{ request('harga_max') }}"
                                class="flex-1 min-w-0 rounded border-gray-300 focus:ring-2 focus:ring-blue-500 px-2"
                                placeholder="Max" pattern="[0-9]*" inputmode="numeric"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        </div>
                    </div>
                </form>

                @if ($errors->has('harga_min'))
                    <div class="w-full md:w-auto mb-4">
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded text-sm flex items-center gap-2">
                            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none" />
                                <line x1="12" y1="8" x2="12" y2="12" stroke="currentColor" stroke-width="2" />
                                <circle cx="12" cy="16" r="1" fill="currentColor" />
                            </svg>
                            {{ $errors->first('harga_min') }}
                        </div>
                    </div>
                @endif

                <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-5 gap-6">
                    @forelse($currentProduk as $produk)
                        @php
                            // Cek class_basename untuk bedain ternak dan produk kemasan
                            $modelType = class_basename($produk);
                            $isTernak = in_array($modelType, ['Kambing', 'Domba']);
                            
                            $isPending = \App\Models\Order::isProductPending($produk->id, get_class($produk));
                            
                            // Untuk Produk Pakan/Obat, ambil stok dari alokasi
                            $stokTersedia = 1; // Default ternak 1
                            if (!$isTernak) {
                                $alokasi = $produk->allocations->where('type', 'jual')->first();
                                $stokTersedia = $alokasi ? $alokasi->qty : 0;
                            }

                            // Cek Path Gambar Biar Gak Error
                            $imgPath = asset('uploads/default.png');
                            
                            if (!empty($produk->image)) {
                                $imgPath = asset('storage/' . $produk->image);
                            }
                        @endphp

                        <div class="bg-white border rounded-xl shadow-sm hover:shadow-md transition overflow-hidden flex flex-col {{ $isPending ? 'opacity-75' : '' }}">
                            
                            <div class="aspect-[4/3] bg-gray-50 flex-shrink-0">
                                <img src="{{ $imgPath }}" alt="{{ $produk->name ?? $produk->nama }}" 
                                    class="w-full h-full object-cover" 
                                    onerror="this.src='{{ asset('uploads/default.png') }}'">
                            </div>
                            
                            <div class="p-4 flex flex-col flex-1">
                                <h3 class="font-semibold text-gray-800 truncate mb-2">
                                    {{ $produk->name ?? $produk->nama ?? ucfirst($kategoriProduk) }}
                                </h3>
                                
                                @if($isTernak)
                                    {{-- Info khusus Ternak --}}
                                    <p class="text-xs text-gray-500 mb-1">Jenis: {{ $produk->type_goat ?? $produk->type_domba ?? '-' }}</p>
                                    <p class="text-xs text-gray-500 mb-2">Berat: {{ $produk->weight_now ? $produk->weight_now . ' kg' : '-' }}</p>
                                    <div class="flex items-center text-sm gap-1.5 text-gray-600 mb-2">
                                        @if (($produk->jenis_kelamin ?? null) === 'Jantan')
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-gender-male" viewBox="0 0 16 16">
                                                <path fill-rule="evenodd" d="M9.5 2a.5.5 0 0 1 0-1h5a.5.5 0 0 1 .5.5v5a.5.5 0 0 1-1 0V2.707L9.871 6.836a5 5 0 1 1-.707-.707L13.293 2zM6 6a4 4 0 1 0 0 8 4 4 0 0 0 0-8" />
                                            </svg>
                                        @elseif (($produk->jenis_kelamin ?? null) === 'Betina')
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-gender-female" viewBox="0 0 16 16">
                                                <path fill-rule="evenodd" d="M8 1a4 4 0 1 0 0 8 4 4 0 0 0 0-8M3 5a5 5 0 1 1 5.5 4.975V12h2a.5.5 0 0 1 0 1h-2v2.5a.5.5 0 0 1-1 0V13h-2a.5.5 0 0 1 0-1h2V9.975A5 5 0 0 1 3 5" />
                                            </svg>
                                        @endif
                                        <p class="text-xs">{{ $produk->jenis_kelamin ?? '-' }}</p>
                                    </div>
                                @else
                                    {{-- Info khusus Produk (Pakan/Obat) --}}
                                    <p class="text-xs text-gray-500 mb-1">Tipe: {{ ucfirst($produk->type) }}</p>
                                    <p class="text-xs text-gray-500 mb-1">Kode: {{ $produk->kode }}</p>
                                    <p class="text-xs text-orange-600 font-bold mb-2">Tersedia: {{ $stokTersedia }} unit</p>
                                @endif

                                <div class="mt-auto pt-3 border-t border-gray-100">
                                    <p class="text-sm font-bold text-gray-900 mb-3">
                                        Rp {{ number_format($produk->harga ?? 0, 0, ',', '.') }}
                                    </p>
                                    
                                    @if($isPending)
                                        <button disabled class="w-full text-center text-sm px-3 py-2 rounded-lg bg-orange-100 text-gray-600 font-medium cursor-not-allowed">
                                            Sedang Diproses
                                        </button>
                                    @else
                                        @php
                                            $catRoute = strtolower($modelType) === 'product' ? 'product' : strtolower($modelType);
                                        @endphp
                                        <a href="{{ route('order.show', ['category' => $catRoute, 'id' => $produk->id]) }}"
                                            class="block w-full text-center text-white text-sm bg-brand-orange hover:bg-orange-700 px-3 py-2 rounded-lg font-semibold transition-colors shadow-sm">
                                            Beli
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center text-gray-500 py-10">
                            Tidak ada produk yang tersedia.
                        </div>
                    @endforelse
                </div>

                <div class="mt-10 flex justify-center p-4">
                    @if ($kategoriProduk === 'kambing' && method_exists($kambings, 'links'))
                        {{ $kambings->appends(request()->query())->links() }}
                    @elseif ($kategoriProduk === 'domba' && method_exists($dombas, 'links'))
                        {{ $dombas->appends(request()->query())->links() }}
                    @endif
                </div>
            </section>
        </div>
    </main>
    <script>
        function clearJenisAndSubmit(selectElement) {
            const form = selectElement.form;
            const jenisInput = form.querySelector('input[name="jenis"]');
            if (jenisInput) jenisInput.remove();
            form.submit();
        }
    </script>
</x-home-layout>