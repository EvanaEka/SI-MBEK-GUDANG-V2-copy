<x-admin-app-layout>
    <div class="container mx-auto my-10 px-4">
        <div class="bg-white p-6 rounded-lg shadow-lg max-w-4xl mx-auto">
            {{-- Header Invoice --}}
            <div class="border-b pb-4 mb-6">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Invoice Pembayaran Manual</h1>
                <div class="flex flex-col md:flex-row md:justify-between md:items-center">
                    <div>
                        <p class="text-lg"><span class="font-semibold">Order ID:</span> {{ $order->order_id }}</p>
                        <p class="text-sm text-gray-600">Tanggal Pesanan: {{ $order->created_at->format('d F Y, H:i') }}
                        </p>
                    </div>
                    <div class="mt-2 md:mt-0">
                        @php
                            $statusClass = match ($order->status) {
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                'success' => 'bg-green-100 text-green-800',
                                'failed' => 'bg-red-100 text-red-800',
                                default => 'bg-gray-100 text-gray-800',
                            };
                            $statusText = match ($order->status) {
                                'pending' => 'Menunggu Verifikasi',
                                'success' => 'Pembayaran Berhasil',
                                'settlement' => 'Pembayaran Berhasil',
                                'failed' => 'Pembayaran Gagal',
                                default => ucfirst($order->status),
                            };
                        @endphp
                        <span class="px-3 py-1 rounded-full text-sm font-medium {{ $statusClass }}">
                            {{ $statusText }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Detail Pelanggan --}}
            <div class="mb-6">
                <h2 class="text-xl font-semibold mb-3 text-gray-800">Detail Pelanggan</h2>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Nama</p>
                            <p class="font-medium">{{ $order->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Email</p>
                            <p class="font-medium">{{ $order->user->email ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">No. Telepon</p>
                            <p class="font-medium">{{ $order->phone }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Alamat</p>
                            <p class="font-medium">{{ $order->address }}</p>
                        </div>
                    </div>
                </div>
            </div>

          {{-- Detail Produk --}}
            <div class="mb-6">
                <h2 class="text-xl font-semibold mb-3 text-gray-800">Detail Produk</h2>
                <div class="bg-gray-50 p-4 rounded-lg">
                    @php
                        $produk = $order->orderable;
                        $kategori = $produk ? class_basename($produk) : 'Tidak ditemukan';
                        
                        // Cara ampuh deteksi apakah ini Product (Pakan/Obat) atau Ternak
                        $isProduct = false;
                        if($produk && get_class($produk) === 'App\Models\Product') {
                            $isProduct = true;
                        }
                    @endphp

                    @if($produk)
                        <div class="flex flex-col md:flex-row gap-4">
                            
                            {{-- GAMBAR PRODUK --}}
                            @if($produk->image)
                                <img src="{{ asset(str_starts_with($produk->image, 'http') ? $produk->image : (str_starts_with($produk->image, 'storage/') ? $produk->image : 'storage/'.$produk->image)) }}" alt="Gambar {{ $kategori }}"
                                    class="w-full md:w-32 h-32 object-cover rounded-lg border bg-white" onerror="this.src='{{ asset('uploads/default.png') }}'">
                            @else
                                <div class="w-full md:w-32 h-32 bg-gray-200 rounded-lg border flex items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            @endif
                            
                            {{-- INFO PRODUK --}}
                            <div class="flex-1">
                                <h3 class="font-semibold text-lg text-gray-900">
                                    {{ $isProduct ? 'Produk Pakan/Obat' : $kategori }} - {{ $produk->name ?? $produk->nama ?? 'Unnamed' }}
                                </h3>
                                <p class="text-gray-600 mb-3 text-sm leading-relaxed">{{ $produk->deskripsi ?? 'Tidak ada keterangan tambahan' }}</p>
                                
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-3 text-sm bg-white p-3 rounded-lg border border-gray-200">
                                    @if($isProduct)
                                        {{-- JIKA INI PAKAN ATAU OBAT --}}
                                        <div>
                                            <span class="text-gray-500 block text-xs">Kode Produk</span>
                                            <span class="font-semibold text-gray-900">{{ $produk->kode ?? '-' }}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-500 block text-xs">Kategori</span>
                                            <span class="font-semibold text-gray-900">{{ ucfirst($produk->type ?? '-') }}</span>
                                        </div>
                                    @else
                                        {{-- JIKA INI TERNAK (KAMBING/DOMBA) --}}
                                        <div>
                                            <span class="text-gray-500 block text-xs">Berat</span>
                                            <span class="font-semibold text-gray-900">{{ $produk->weight_now ?? '-' }} kg</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-500 block text-xs">Umur</span>
                                            <span class="font-semibold text-gray-900">
                                                @php
                                                    if(!empty($produk->tanggal_lahir)){
                                                        $birthDate = new DateTime($produk->tanggal_lahir);
                                                        $today = new DateTime();
                                                        $diff = $today->diff($birthDate);
                                                        if ($diff->y > 0 && $diff->m > 0) { echo "{$diff->y} thn {$diff->m} bln"; }
                                                        elseif($diff->y > 0) { echo "{$diff->y} tahun"; }
                                                        elseif($diff->m > 0) { echo "{$diff->m} bulan"; }
                                                        else { echo "Baru lahir"; }
                                                    } else {
                                                        echo "-";
                                                    }
                                                @endphp
                                            </span>
                                        </div>
                                        <div>
                                            <span class="text-gray-500 block text-xs">Jenis Kelamin</span>
                                            <span class="font-semibold text-gray-900">{{ $produk->jenis_kelamin ?? '-' }}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-500 block text-xs">Status Kesehatan</span>
                                            <span class="font-semibold {{ strtolower($produk->healt_status ?? '') === 'sehat' ? 'text-green-600' : 'text-red-600' }}">
                                                {{ $produk->healt_status ?? '-' }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="text-gray-600">Informasi produk tidak tersedia</p>
                            <p class="text-sm text-gray-500">Produk ID: {{ $order->produk_id ?? 'N/A' }}</p>
                        </div>
                    @endif

                    {{-- PERHITUNGAN HARGA --}}
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <div class="flex justify-between items-center text-sm text-gray-600 mb-2">
                            <span>Harga Satuan: Rp {{ number_format($order->gross_amount / max($order->qty ?? 1, 1), 0, ',', '.') }}</span>
                            <span>Qty: <strong class="text-gray-900">{{ $order->qty ?? 1 }}</strong></span>
                            <span>Metode: <strong class="text-gray-900">{{ $order->payment_method === 'midtrans' ? 'Digital Payment' : 'Transfer Manual' }}</strong></span>
                        </div>
                        <div class="flex justify-between items-center bg-green-50 p-3 rounded-lg border border-green-100">
                            <span class="text-lg font-bold text-gray-800">Total Pembayaran</span>
                            <span class="text-2xl font-black text-green-700">
                                Rp {{ number_format($order->gross_amount, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Informasi Pembayaran --}}
            <div class="mb-6">
                <h2 class="text-xl font-semibold mb-3 text-gray-800">Informasi Pembayaran</h2>
                <div class="bg-blue-50 border border-blue-200 p-4 rounded-lg mb-4">
                    <h3 class="font-semibold text-blue-900 mb-2">Rekening Tujuan Transfer:</h3>
                    <div class="text-blue-800">
                        <p><span class="font-medium">Bank:</span> BCA</p>
                        <p><span class="font-medium">No. Rekening:</span> 761801018897538</p>
                        <p><span class="font-medium">Atas Nama:</span> SI MBEK</p>
                    </div>
                </div>
            </div>

            {{-- Detail Transfer Anda --}}
            <div class="mb-6">
                <h2 class="text-xl font-semibold mb-3 text-gray-800">Detail Transfer Anda</h2>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <p class="text-sm text-gray-600">Nama Pengirim</p>
                            <p class="font-medium">{{ $order->sender_name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Bank Asal</p>
                            <p class="font-medium">{{ $order->bank_origin }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Tanggal Transfer</p>
                            <p class="font-medium">
                                {{ $order->transfer_date ? $order->transfer_date->format('d F Y') : '-' }}
                            </p>
                        </div>
                    </div>

                    @if ($order->bukti_transfer)
                        <div>
                            <p class="text-sm text-gray-600 mb-2">Bukti Transfer:</p>
                            <img src="{{ asset('storage/' . $order->bukti_transfer) }}" alt="Bukti Transfer"
                                class="max-w-md w-full h-auto rounded-lg shadow-sm cursor-pointer"
                                onclick="openImageModal(this.src)">
                            <p class="text-xs text-gray-500 mt-1">Klik gambar untuk memperbesar</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Status dan Aksi --}}
            <div class="border-t pt-6">
                @if ($order->status === 'pending')
                @elseif($order->status === 'success')
                    <div class="bg-green-50 border border-green-200 p-4 rounded-lg mb-4">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <div>
                                <h3 class="font-medium text-green-800">Pembayaran Berhasil Diverifikasi</h3>
                                <p class="text-sm text-green-700">Selamat! Pembayaran Anda telah dikonfirmasi. Tim kami
                                    akan segera menghubungi Anda.</p>
                            </div>
                        </div>
                    </div>
                @elseif($order->status === 'failed')
                    <div class="bg-red-50 border border-red-200 p-4 rounded-lg mb-4">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-red-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <div>
                                <h3 class="font-medium text-red-800">Pembayaran Ditolak</h3>
                                <p class="text-sm text-red-700">Maaf, pembayaran Anda tidak dapat diverifikasi. Silakan
                                    hubungi customer service untuk informasi lebih lanjut.</p>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="flex flex-col sm:flex-row gap-3">
                    <button onclick="window.print()"
                        class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md transition-colors duration-200">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                            </path>
                        </svg>
                        Cetak Invoice
                    </button>
                    <a href="{{ route('admin.penjualan') }}"
                        class="bg-brand-orange hover:bg-orange-700 text-white px-4 py-2 rounded-md text-center transition-colors duration-200">
                        Lihat Semua Transaksi
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal untuk memperbesar gambar --}}
    <div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 hidden z-50 flex items-center justify-center p-4">
        <div class="relative max-w-4xl max-h-full">
            <img id="modalImage" src="" alt="Bukti Transfer" class="max-w-full max-h-full object-contain">
            <button onclick="closeImageModal()"
                class="absolute top-4 right-4 text-white bg-black bg-opacity-50 rounded-full p-2 hover:bg-opacity-75">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
        </div>
    </div>

    <script>
        function openImageModal(imageSrc) {
            document.getElementById('modalImage').src = imageSrc;
            document.getElementById('imageModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeImageModal() {
            document.getElementById('imageModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Close modal when clicking outside the image
        document.getElementById('imageModal').addEventListener('click', function (e) {
            if (e.target === this) {
                closeImageModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeImageModal();
            }
        });
    </script>

    <style>
        @media print {

            /* Hilangkan layout luar */
            button,
            header,
            footer,
            nav,
            aside,
            .navbar,
            .sidebar,
            .admin-layout {
                display: none !important;
            }

            /* Hanya tampilkan container utama */
            .container {
                width: 100% !important;
                padding: 0 !important;
                margin-left: 7vh !important;
            }

            /* Pastikan tabel tetap rapi */
            table {
                border-collapse: collapse;
                width: 100%;
            }

            thead {
                display: table-header-group;
            }

            tfoot {
                display: table-footer-group;
            }

            /* Atur halaman cetak */
            @page {
                size: A4;
            }
        }
    </style>
</x-admin-app-layout>