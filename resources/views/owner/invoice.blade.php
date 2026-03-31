<x-owner-app-layout>
    <div class="container mx-auto my-10 px-4">
        <div class="bg-white p-6 rounded-lg shadow-lg max-w-4xl mx-auto">

            {{-- Debug Info (hanya di development) --}}
            @if(app()->environment('local'))
                <div class="bg-blue-50 border border-blue-200 rounded p-4 mb-6">
                    <h4 class="font-semibold text-blue-800">Debug Info:</h4>
                    <p class="text-sm text-blue-700">Order ID: {{ $order->order_id ?? 'NULL' }}</p>
                    <p class="text-sm text-blue-700">Payment Method: {{ $order->payment_method ?? 'NULL' }}</p>
                    <p class="text-sm text-blue-700">Status: {{ $order->status ?? 'NULL' }}</p>
                </div>
            @endif

            {{-- Header Invoice --}}
            <div class="border-b pb-4 mb-6">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Invoice Pembayaran</h1>
                <div class="flex flex-col md:flex-row md:justify-between md:items-center">
                    <div>
                        <p class="text-lg"><span class="font-semibold">Order ID:</span> {{ $order->order_id }}</p>
                        <p class="text-sm text-gray-600">Tanggal Pesanan: {{ $order->created_at->format('d F Y, H:i') }}
                        </p>
                    </div>
                    <div class="mt-2 md:mt-0">
                        @php
                            $statusClass = match ($order->status) {
                                'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                'success' => 'bg-green-100 text-green-800 border-green-200',
                                'failed', 'expired' => 'bg-red-100 text-red-800 border-red-200',
                                default => 'bg-gray-100 text-gray-800 border-gray-200'
                            };
                            $statusText = match ($order->status) {
                                'pending' => 'Menunggu Pembayaran',
                                'success' => 'Pembayaran Berhasil',
                                'settlement' => 'Pembayaran Berhasil',
                                'failed' => 'Pembayaran Gagal',
                                'expired' => 'Pembayaran Kedaluwarsa',
                                default => ucfirst($order->status)
                            };
                        @endphp
                        <span
                            class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium border {{ $statusClass }}">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                @if($order->status === 'success')
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                @elseif($order->status === 'pending')
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                        clip-rule="evenodd" />
                                @else
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                        clip-rule="evenodd" />
                                @endif
                            </svg>
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
                        $isProduct = strtolower($kategori) === 'product';
                    @endphp
                    @if($produk)
                        <div class="flex flex-col md:flex-row gap-4">
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
                            <div class="flex-1">
                                <h3 class="font-semibold text-lg text-gray-900">{{ $isProduct ? 'Produk Pakan/Obat' : $kategori }} -
                                    {{ $produk->name ?? $produk->nama ?? 'Unnamed' }}
                                </h3>
                                <p class="text-gray-600 mb-3 text-sm leading-relaxed">{{ $produk->deskripsi ?? 'Tidak ada keterangan tambahan' }}</p>
                                
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-3 text-sm bg-white p-3 rounded-lg border border-gray-200">
                                    @if($isProduct)
                                        {{-- Info khusus Pakan / Obat --}}
                                        <div>
                                            <span class="text-gray-500 block text-xs">Kode Produk</span>
                                            <span class="font-semibold text-gray-900">{{ $produk->kode ?? '-' }}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-500 block text-xs">Tipe</span>
                                            <span class="font-semibold text-gray-900">{{ ucfirst($produk->type ?? '-') }}</span>
                                        </div>
                                    @else
                                        {{-- Info khusus Ternak (Kambing/Domba) --}}
                                        <div>
                                            <span class="text-gray-500 block text-xs">Berat</span>
                                            <span class="font-semibold text-gray-900">{{ $produk->weight_now ?? '-' }} kg</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-500 block text-xs">Umur</span>
                                            <span class="font-semibold text-gray-900">
                                                @php
                                                    $birthDate = new DateTime($produk->tanggal_lahir);
                                                    $today = new DateTime();
                                                    $diff = $today->diff($birthDate);
                                                    $years = $diff->y;
                                                    $months = $diff->m;
                                                @endphp
                                                @if ($years > 0 && $months > 0) {{ $years }} tahun {{ $months }} bulan
                                                @elseif($years > 0) {{ $years }} tahun
                                                @elseif($months > 0) {{ $months }} bulan
                                                @else Baru lahir @endif
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


            {{-- Action Buttons --}}
            <div class="border-t pt-6">
                <div class="flex flex-col sm:flex-row gap-3 justify-between">
                    <div class="flex flex-col sm:flex-row gap-3">
                        <button onclick="window.print()"
                            class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a2 2 0 002 2h6a2 2 0 002-2v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm8 0H7v3h6V4zm0 8H7v4h6v-4z"
                                    clip-rule="evenodd" />
                            </svg>
                            Cetak Invoice
                        </button>

                        <a href="{{ route('owner.penjualan') }}"
                            class="inline-flex items-center justify-center px-6 py-3 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-orange-600 hover:bg-orange-700 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                            Lihat Semua Transaksi
                        </a>
                    </div>


                </div>
            </div>
        </div>
    </div>


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
            .owner-layout {
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
</x-owner-app-layout>