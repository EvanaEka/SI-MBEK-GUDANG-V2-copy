<x-app-layout>
    <div class="container mx-auto my-10 px-4">
        <div class="bg-white p-6 rounded-lg shadow-lg max-w-4xl mx-auto">

            {{-- Debug Info (hanya di development) --}}
            @if (app()->environment('local'))
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
                                default => 'bg-gray-100 text-gray-800 border-gray-200',
                            };
                            $statusText = match ($order->status) {
                                'pending' => 'Menunggu Pembayaran',
                                'success' => 'Pembayaran Berhasil',
                                'settlement' => 'Pembayaran Berhasil',
                                'failed' => 'Pembayaran Gagal',
                                'expired' => 'Pembayaran Kedaluwarsa',
                                default => ucfirst($order->status),
                            };
                        @endphp
                        <span
                            class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium border {{ $statusClass }}">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                @if ($order->status === 'success')
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

                @if ($order->payment_method === 'midtrans')
                    <div class="bg-blue-50 border border-blue-200 p-4 rounded-lg">
                        <div class="flex items-start space-x-3">
                            <svg class="w-6 h-6 text-blue-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z" />
                                <path fill-rule="evenodd"
                                    d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z"
                                    clip-rule="evenodd" />
                            </svg>
                            <div>
                                <h3 class="font-semibold text-blue-900 mb-2">Pembayaran Digital via Midtrans</h3>
                                <div class="text-blue-800 space-y-1">
                                    <p><span class="font-medium">Snap Token:</span>
                                        {{ $order->snap_token ? 'Generated' : 'Not Available' }}</p>
                                    <p><span class="font-medium">Payment Gateway:</span> Midtrans Payment Gateway</p>
                                    <p><span class="font-medium">Supported Methods:</span> Credit Card, Bank Transfer,
                                        E-Wallet, dll</p>
                                    @if ($order->status === 'pending' && $order->snap_token)
                                        <div class="mt-3 p-3 bg-blue-100 rounded-lg">
                                            <p class="text-sm font-medium text-blue-900 mb-2">Lanjutkan Pembayaran:</p>
                                            <button id="pay-button"
                                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm transition-colors">
                                                Bayar Sekarang
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="bg-blue-50 border border-blue-200 p-4 rounded-lg">
                        <h3 class="font-semibold text-blue-900 mb-2">Rekening Tujuan Transfer Manual:</h3>
                        <div class="text-blue-800">
                            <p><span class="font-medium">Bank:</span> BCA</p>
                            <p><span class="font-medium">No. Rekening:</span> 1234567890</p>
                            <p><span class="font-medium">Atas Nama:</span> PT Contoh Nama</p>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Status Information --}}
            <div class="mb-6">
                <div
                    class="p-4 rounded-lg border {{ $order->status === 'pending' ? 'bg-yellow-50 border-yellow-200' : ($order->status === 'success' ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200') }}">
                    @if ($order->status === 'pending')
                        <div class="flex items-start space-x-3">
                            <svg class="w-5 h-5 text-yellow-600 mt-0.5 flex-shrink-0" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                    clip-rule="evenodd" />
                            </svg>
                            <div>
                                <h3 class="font-medium text-yellow-800">
                                    {{ $order->payment_method === 'midtrans' ? 'Menunggu Pembayaran' : 'Menunggu Verifikasi' }}
                                </h3>
                                <p class="text-sm text-yellow-700 mt-1">
                                    @if ($order->payment_method === 'midtrans')
                                        Silakan lanjutkan pembayaran melalui Midtrans. Pembayaran akan otomatis
                                        terverifikasi setelah berhasil.
                                    @else
                                        Pembayaran manual Anda sedang diverifikasi. Proses verifikasi biasanya memakan
                                        waktu 1x24 jam.
                                    @endif
                                </p>
                            </div>
                        </div>
                    @elseif($order->status === 'success')
                        <div class="flex items-start space-x-3">
                            <svg class="w-5 h-5 text-green-600 mt-0.5 flex-shrink-0" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                            <div>
                                <h3 class="font-medium text-green-800">Pembayaran Berhasil!</h3>
                                <p class="text-sm text-green-700 mt-1">
                                    Selamat! Pembayaran Anda telah berhasil dikonfirmasi. Admin akan segera menghubungi
                                    Anda untuk pengaturan pengiriman atau pengambilan produk.
                                </p>
                            </div>
                        </div>
                    @else
                        <div class="flex items-start space-x-3">
                            <svg class="w-5 h-5 text-red-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd" />
                            </svg>
                            <div>
                                <h3 class="font-medium text-red-800">
                                    {{ $order->status === 'expired' ? 'Pembayaran Kedaluwarsa' : 'Pembayaran Gagal' }}
                                </h3>
                                <p class="text-sm text-red-700 mt-1">
                                    @if ($order->status === 'expired')
                                        Waktu pembayaran telah habis. Silakan buat pesanan baru jika masih berminat.
                                    @else
                                        Pembayaran tidak dapat diproses. Silakan hubungi customer service atau coba
                                        lagi.
                                    @endif
                                </p>
                            </div>
                        </div>
                    @endif
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

                        <a href="{{ route('order.transaksi') }}"
                            class="inline-flex items-center justify-center px-6 py-3 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-orange-600 hover:bg-orange-700 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                            Lihat Semua Transaksi
                        </a>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3">
                        <a href="{{ route('forsale') }}"
                            class="inline-flex items-center justify-center px-6 py-3 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                            Belanja Lagi
                        </a>

                        <a href="{{ route('home') }}"
                            class="inline-flex items-center justify-center px-6 py-3 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                            </svg>
                            Kembali ke Beranda
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Midtrans Script untuk pembayaran pending --}}
    @if ($order->status === 'pending' && $order->snap_token && $order->payment_method === 'midtrans')
        <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}">
        </script>
        <script>
            document.getElementById('pay-button').addEventListener('click', function () {
                window.snap.pay('{{ $order->snap_token }}', {
                    onSuccess: function (result) {
                        console.log('Payment success:', result);
                        window.location.reload();
                    },
                    onPending: function (result) {
                        console.log('Payment pending:', result);
                        window.location.reload();
                    },
                    onError: function (result) {
                        console.log('Payment error:', result);
                        alert('Terjadi kesalahan dalam pembayaran. Silakan coba lagi.');
                    },
                    onClose: function () {
                        console.log('Payment popup closed');
                    }
                });
            });
        </script>
    @endif

    <style>
        @media print {
            body * {
                visibility: hidden;
            }

            .container,
            .container * {
                visibility: visible;
            }

            .container {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }

            button,
            .no-print {
                display: none !important;
            }
        }
    </style>
</x-app-layout>