<x-owner-app-layout>
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
                        $produk = $order->kambing ?? $order->domba;
                        $kategori = $order->kambing ? 'Kambing' : 'Domba';
                    @endphp
                    @if ($produk)
                        <div class="flex flex-col md:flex-row gap-4">
                            @if ($produk->image)
                                <img src="{{ asset($produk->image) }}" alt="Gambar {{ $kategori }}"
                                    class="md:w-32 h-32 object-cover rounded-lg">
                            @endif
                            <div class="flex-1">
                                <h3 class="font-semibold text-lg">{{ $kategori }} -
                                    {{ $produk->name ?? 'Unnamed' }}</h3>
                                <p class="text-gray-600 mb-2">{{ $produk->deskripsi ?? 'Tidak ada deskripsi' }}</p>
                                <div class="grid grid-cols-2 gap-2 text-sm">
                                    <div>
                                        <span class="text-gray-600">Berat:</span>
                                        <span class="font-medium">{{ $produk->weight_now ?? '-' }} kg</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-600">Umur:</span>
                                        <span class="font-medium">
                                            @php
                                            $birthDate = new DateTime($produk->tanggal_lahir);
                                            $today = new DateTime();
                                            $diff = $today->diff($birthDate);

                                            $years = $diff->y;
                                            $months = $diff->m;
                                        @endphp
                                            @if ($years > 0 && $months > 0)
                                                {{ $years }} tahun {{ $months }} bulan
                                            @elseif($years > 0)
                                                {{ $years }} tahun
                                            @elseif($months > 0)
                                                {{ $months }} bulan
                                            @else
                                                Baru lahir
                                            @endif
                                        </span>
                                    </div>
                                    <div>
                                        <span class="text-gray-600">Jenis Kelamin:</span>
                                        <span class="font-medium">{{ $produk->jenis_kelamin ?? '-' }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-600">Status Kesehatan:</span>
                                        <span
                                            class="font-medium {{ strtolower($produk->healt_status ?? '') === 'sehat' ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $produk->healt_status ?? '-' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="mt-4 pt-4 border-t">
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-semibold">Total Pembayaran:</span>
                            <span class="text-2xl font-bold text-green-600">
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
                                {{ $order->transfer_date ? $order->transfer_date->format('d F Y') : '-' }}</p>
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

                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('owner.penjualan') }}"
                        class="bg-brand-orange hover:bg-orange-700 text-white px-4 py-2 rounded-md text-center transition-colors duration-200">
                        Lihat Semua Transaksi
                    </a>
                </div>
            </div>
        </div>

    {{-- Modal untuk memperbesar gambar --}}
    <div id="imageModal"
        class="fixed inset-0 bg-black bg-opacity-75 hidden z-50 flex items-center justify-center p-4">
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
        document.getElementById('imageModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeImageModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeImageModal();
            }
        });
    </script>
</x-owner-app-layout>
