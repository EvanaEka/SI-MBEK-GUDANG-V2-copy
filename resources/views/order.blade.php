@php
    use App\Models\Kambing;
    use App\Models\Domba;
    use App\Models\Product;

    $kategoriProduk = request('kategori_produk', 'semua');
    $jenisList = [];
    $currentProduk = collect();

@endphp
<x-home-layout>
    <x-navbar-v2 />

    {{-- Add CSRF token to meta for JavaScript access --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Midtrans Script --}}
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="flex flex-col items-center justify-center min-h-screen">
        <div class="bg-white rounded-lg shadow-md p-6 w-full max-w-4xl my-10">

            {{-- Banner Gambar --}}
            <div class="mb-6">
                <img src="{{ asset($item->image ?? 'uploads/default.png') }}" alt="Gambar Produk"
                    class="w-full h-48 object-cover rounded">
            </div>

            {{-- Pilihan Metode Pembayaran --}}
            <div class="flex justify-center gap-4 mb-6">
                <button type="button" id="btnMidtrans"
                    class="payment-btn bg-brand-orange text-white px-4 py-2 rounded font-semibold active">
                    Bayar via Midtrans
                </button>
                <button type="button" id="btnManual"
                    class="payment-btn bg-gray-200 text-gray-700 px-4 py-2 rounded font-semibold">
                    Transfer Bank Manual
                </button>
            </div>
            <input type="hidden" id="payment_method" name="payment_method" value="midtrans">

            <div class="flex flex-col md:flex-row md:gap-6">
                {{-- Form Pesanan --}}
                <div class="w-full md:w-2/3">
                    <h2 class="text-xl font-bold mb-4">ISI DATA PENERIMA</h2>
                    <form id="checkoutForm" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="produk_id" name="produk_id" value="{{ $item->id }}">
                        <input type="hidden" id="category" name="category" value="{{ $category }}">

                        {{-- Email --}}
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">*Email:</label>
                            <input type="email" id="email" name="email"
                                value="{{ old('email', Auth::user()->email ?? '') }}"
                                class="mt-1 block w-full border border-gray-300 rounded-md p-2" required>
                        </div>

                        {{-- Nama --}}
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">*Name/Nama:</label>
                            <input type="text" id="name" name="name" value="{{ old('name', Auth::user()->name ?? '') }}"
                                class="mt-1 block w-full border border-gray-300 rounded-md p-2" required>
                        </div>

                        {{-- Alamat --}}
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">*Alamat:</label>
                            <textarea id="address" name="address" rows="3"
                                class="mt-1 block w-full border border-gray-300 rounded-md p-2"
                                required>{{ old('address', Auth::user()->alamat ?? '') }}</textarea>
                        </div>

                        @auth
                            {{-- Kota --}}
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">*Kota:</label>
                                <input type="text" id="city" name="city" value="{{ old('city', Auth::user()->kota ?? '') }}"
                                    class="mt-1 block w-full border border-gray-300 rounded-md p-2" required>
                            </div>

                            {{-- Kecamatan --}}
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">*Kecamatan:</label>
                                <input type="text" id="district" name="district"
                                    value="{{ old('district', Auth::user()->kecamatan ?? '') }}"
                                    class="mt-1 block w-full border border-gray-300 rounded-md p-2" required>
                            </div>

                            {{-- Kelurahan --}}
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">*Kelurahan:</label>
                                <input type="text" id="village" name="village"
                                    value="{{ old('village', Auth::user()->kelurahan ?? '') }}"
                                    class="mt-1 block w-full border border-gray-300 rounded-md p-2" required>
                            </div>
                        @endauth

                        {{-- Nomor Telepon --}}
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">*No HP:</label>
                            <input type="tel" id="phone" name="phone"
                                value="{{ old('phone', Auth::user()->no_telepon ?? '') }}"
                                class="mt-1 block w-full border border-gray-300 rounded-md p-2" required>
                        </div>

                        {{-- Form khusus transfer manual --}}
                        <div id="manualFields" class="hidden">
                            <hr class="my-4">
                            <h3 class="text-lg font-semibold mb-3">Informasi Transfer Manual</h3>
                            <div class="bg-blue-50 p-4 rounded-lg mb-4">
                                <p class="text-sm text-gray-700 mb-2">Silakan transfer ke rekening berikut:</p>
                                <div class="font-semibold text-blue-900">
                                    <p>Bank BRI</p>
                                    <p>No. Rekening: 761801018897538</p>
                                    <p>Atas Nama: SI MBEK</p>
                                    <p class="text-green-600 mt-2">Jumlah Transfer: Rp
                                        {{ number_format($item->harga, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">*Nama Pengirim:</label>
                                <input type="text" name="sender_name"
                                    class="mt-1 block w-full border border-gray-300 rounded-md p-2"
                                    placeholder="Nama yang tertera di rekening pengirim">
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">*Bank Asal:</label>
                                <input type="text" name="bank_origin"
                                    class="mt-1 block w-full border border-gray-300 rounded-md p-2"
                                    placeholder="Contoh: Bank Mandiri">
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">*Tanggal Transfer:</label>
                                <input type="date" name="transfer_date"
                                    class="mt-1 block w-full border border-gray-300 rounded-md p-2"
                                    max="{{ date('Y-m-d') }}">
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">*Jumlah Transfer (Rp):</label>
                                <input type="number" name="transfer_amount" value="{{ $item->harga }}"
                                    class="mt-1 block w-full border border-gray-300 rounded-md p-2" readonly>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">*Upload Bukti Transfer:</label>
                                <input type="file" name="transfer_proof" accept="image/*"
                                    class="mt-1 block w-full border border-gray-300 rounded-md p-2">
                                <p class="text-xs text-gray-500 mt-1">Format: JPG, JPEG, PNG. Maksimal 2MB.</p>
                            </div>
                        </div>

                        <button type="submit" id="submitBtn"
                            class="w-full bg-brand-orange hover:bg-orange-700 text-white font-bold py-2 px-4 rounded disabled:opacity-50">
                            <span id="submitText">Bayar Sekarang</span>
                            <span id="loadingText" class="hidden">Memproses...</span>
                        </button>
                    </form>
                </div>

                {{-- Detail Produk --}}
                <div class="w-full md:w-1/3 mt-6 md:mt-0">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">Yang Anda Dapatkan</h3>
                    <ul class="list-disc pl-6 space-y-2 text-sm text-gray-700">
                        <li>
                            <span class="font-medium">
                                {{ $category === 'product' ? '1 Produk' : '1 Ekor ' . ucfirst($category) }}
                            </span>
                            {{ $category === 'product' ? '' : 'sesuai syariat Islam' }}
                        </li>
                        <li>
                            <span class="font-medium">Status Kesehatan:</span>
                            @if($category === 'kambing' || $category === 'domba')
                                <span
                                    class="{{ strtolower($item->faksin_status ?? '') === 'tidak aktif' ? 'text-red-600' : 'text-green-600' }}">
                                    {{ strtolower($item->faksin_status ?? '') === 'tidak' ? 'Belum divaksin' : 'Sudah divaksin' }}
                                </span>
                                &
                                <span
                                    class="{{ strtolower($item->healt_status ?? '') === 'sehat' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ strtolower($item->healt_status ?? '') === 'sehat' ? 'Sehat' : 'Tidak sehat' }}
                                </span>
                            @else
                                <span class="text-green-600">Produk Berkualitas</span>
                            @endif
                        </li>

                        <li>Garansi tukar jika sakit saat diterima <span class="text-xs text-gray-500">(S&K
                                berlaku)</span></li>
                        <li>Sertifikat kesehatan tersedia <span class="text-xs text-gray-500">(jika diminta)</span>
                        </li>
                    </ul>
                    <div class="mt-6 bg-gray-50 border border-gray-200 rounded-lg p-4 shadow-sm">
                        <h4 class="text-base font-semibold text-gray-800 mb-2">Deskripsi Produk</h4>
                        <ul class="list-disc pl-6 space-y-2 text-sm text-gray-700">
                            <li><span class="font-medium"></span> {{ $item->name ?? ucfirst($kategoriProduk) }}</li>
                            @if($category === 'kambing' || $category === 'domba')
                                <li><span class="font-medium">Jenis Kelamin:</span> {{ $item->jenis_kelamin ?? '-' }}</li>
                                <li><span class="font-medium">Berat Saat Ini:</span> {{ $item->weight_now ?? '-' }} kg
                                </li>
                            @endif
                            @if(($category === 'kambing' || $category === 'domba') && !empty($item->tanggal_lahir))
                                @php
                                    $birthDate = new DateTime($item->tanggal_lahir);
                                    $today = new DateTime();
                                    $diff = $today->diff($birthDate);
                                    $years = $diff->y;
                                    $months = $diff->m;
                                @endphp
                            @endif

                            @if($category === 'kambing' || $category === 'domba')
                                <li>
                                    <span class="font-medium">Perkiraan Umur:</span>
                                    @if(isset($years))
                                        @if ($years > 0 && $months > 0)
                                            {{ $years }} tahun {{ $months }} bulan
                                        @elseif($years > 0)
                                            {{ $years }} tahun
                                        @elseif($months > 0)
                                            {{ $months }} bulan
                                        @else
                                            Baru lahir
                                        @endif
                                    @else
                                        -
                                    @endif
                                </li>
                            @endif

                        </ul>
                    </div>
                    <div class="mt-6 bg-gray-50 border border-gray-200 rounded-lg p-4 shadow-sm">
                        <h4 class="text-base font-semibold text-gray-800 mb-2">Informasi Pengiriman</h4>
                        <p class="text-sm text-gray-700">Produk dapat diambil langsung di lokasi atau dikirim
                            ke alamat Anda dengan menghubungi admin</p>
                    </div>
                    <div class="mt-6 bg-gray-50 border border-gray-200 rounded-lg p-4 shadow-sm">
                        <h4 class="text-base font-semibold text-gray-800 mb-2">Rincian Pesanan</h4>
                        <div class="text-sm text-gray-700 space-y-1">
                            <p>Harga {{ $category === 'product' ? 'Produk' : ucfirst($category) }}:
                                <span class="font-semibold text-gray-900">Rp
                                    {{ number_format($item->harga, 0, ',', '.') }}</span>
                            </p>
                            <p>Total:
                                <span class="font-semibold text-green-700">Rp
                                    {{ number_format($item->harga, 0, ',', '.') }}</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Script yang sudah diperbaiki --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const btnMidtrans = document.getElementById('btnMidtrans');
            const btnManual = document.getElementById('btnManual');
            const paymentMethodInput = document.getElementById('payment_method');
            const manualFields = document.getElementById('manualFields');
            const form = document.getElementById('checkoutForm');
            const submitBtn = document.getElementById('submitBtn');
            const submitText = document.getElementById('submitText');
            const loadingText = document.getElementById('loadingText');

            // Function untuk toggle loading state
            function toggleLoading(isLoading) {
                if (isLoading) {
                    submitBtn.disabled = true;
                    submitText.classList.add('hidden');
                    loadingText.classList.remove('hidden');
                } else {
                    submitBtn.disabled = false;
                    submitText.classList.remove('hidden');
                    loadingText.classList.add('hidden');
                }
            }

            // Add this function for showing success message
            function showSuccessMessage(message, redirectUrl) {
                Swal.fire({
                    title: 'Berhasil!',
                    text: message,
                    icon: 'success',
                    timer: 2000,
                    timerProgressBar: true,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = redirectUrl;
                });
            }

            // Function untuk validate manual transfer fields
            function validateManualFields() {
                const requiredFields = ['sender_name', 'bank_origin', 'transfer_date', 'transfer_amount',
                    'transfer_proof'
                ];
                const missingFields = [];

                for (let fieldName of requiredFields) {
                    const field = document.querySelector(`[name="${fieldName}"]`);
                    if (!field || (!field.value && !field.files?.[0])) {
                        missingFields.push(fieldName.replace('_', ' ').replace('proof', 'bukti'));
                    }
                }

                if (missingFields.length > 0) {
                    Swal.fire({
                        title: 'Validasi Error!',
                        html: `Field berikut harus diisi:<br>${missingFields.join('<br>')}`,
                        icon: 'error'
                    });
                    return false;
                }

                // Validate file size (max 2MB)
                const fileInput = document.querySelector('[name="transfer_proof"]');
                if (fileInput.files[0] && fileInput.files[0].size > 2 * 1024 * 1024) {
                    Swal.fire({
                        title: 'File Terlalu Besar',
                        text: 'Ukuran file bukti transfer maksimal 2MB',
                        icon: 'error'
                    });
                    return false;
                }

                return true;
            }

            // Event listener untuk switch payment method
            btnMidtrans.addEventListener('click', function () {
                paymentMethodInput.value = 'midtrans';
                manualFields.classList.add('hidden');
                btnMidtrans.classList.add('active', 'bg-brand-orange', 'text-white');
                btnMidtrans.classList.remove('bg-gray-200', 'text-gray-700');
                btnManual.classList.remove('active', 'bg-brand-orange', 'text-white');
                btnManual.classList.add('bg-gray-200', 'text-gray-700');
                submitText.textContent = 'Bayar Sekarang';
            });

            btnManual.addEventListener('click', function () {
                paymentMethodInput.value = 'manual';
                manualFields.classList.remove('hidden');
                btnManual.classList.add('active', 'bg-brand-orange', 'text-white');
                btnManual.classList.remove('bg-gray-200', 'text-gray-700');
                btnMidtrans.classList.remove('active', 'bg-brand-orange', 'text-white');
                btnMidtrans.classList.add('bg-gray-200', 'text-gray-700');
                submitText.textContent = 'Kirim Bukti Transfer';
            });

            // Form submission handler
            form.addEventListener('submit', function (e) {
                e.preventDefault();

                const paymentMethod = paymentMethodInput.value;
                const formData = new FormData(form);

                // Validasi form dasar
                const requiredBasicFields = ['email', 'name', 'address', 'phone'];
                for (let fieldName of requiredBasicFields) {
                    const field = document.querySelector(`[name="${fieldName}"]`);
                    if (!field || !field.value.trim()) {
                        alert(`Field ${fieldName} harus diisi`);
                        return;
                    }
                }

                console.log('Payment method:', paymentMethod);
                console.log('Form data entries:');
                for (let [key, value] of formData.entries()) {
                    console.log(key, ':', value instanceof File ? `File: ${value.name}` : value);
                }

                toggleLoading(true);

                if (paymentMethod === 'midtrans') {
                    // Process Midtrans payment
                    fetch(`${window.location.origin}/midtrans/token`, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                ?.getAttribute('content')
                        },
                        body: formData
                    })
                        .then(response => {
                            console.log('Midtrans response status:', response.status);
                            if (!response.ok) {
                                return response.text().then(text => {
                                    throw new Error(`HTTP ${response.status}: ${text}`);
                                });
                            }
                            return response.json();
                        })
                        .then(data => {
                            toggleLoading(false);
                            console.log('Midtrans response data:', data);
                            if (data.error) {
                                alert('Error: ' + data.error);
                                return;
                            }

                            // Open Midtrans popup
                            window.snap.pay(data.snap_token, {
                                onSuccess: function (result) {
                                    console.log('Payment success:', result);
                                    showSuccessMessage(
                                        'Pembayaran berhasil! Anda akan dialihkan ke halaman invoice.',
                                        `${window.location.origin}/order/invoice/${result.order_id}`
                                    );
                                },
                                onPending: function (result) {
                                    console.log('Payment pending:', result);
                                    Swal.fire({
                                        title: 'Pembayaran Tertunda',
                                        text: 'Silakan selesaikan pembayaran Anda',
                                        icon: 'info'
                                    }).then(() => {
                                        window.location.href =
                                            `${window.location.origin}/order/invoice/${result.order_id}`;
                                    });
                                },
                                onError: function (result) {
                                    console.log('Payment error:', result);
                                    Swal.fire({
                                        title: 'Error!',
                                        text: 'Terjadi kesalahan dalam pembayaran. Silakan coba lagi.',
                                        icon: 'error'
                                    });
                                },
                                onClose: function () {
                                    console.log('Payment popup closed');
                                    Swal.fire({
                                        title: 'Pembayaran Dibatalkan',
                                        text: 'Popup pembayaran ditutup. Silakan coba lagi jika belum selesai.',
                                        icon: 'warning'
                                    });
                                }
                            });
                        })
                        .catch(error => {
                            toggleLoading(false);
                            console.error('Midtrans Error:', error);
                            alert(`Terjadi kesalahan sistem Midtrans: ${error.message}`);
                        });

                } else {
                    // Process manual transfer
                    if (!validateManualFields()) {
                        toggleLoading(false);
                        return;
                    }

                    console.log('Processing manual transfer...');

                    fetch(`${window.location.origin}/manual/transfer`, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                ?.getAttribute('content')
                        },
                        body: formData
                    })
                        .then(response => {
                            console.log('Manual transfer response status:', response.status);
                            console.log('Manual transfer response headers:', response.headers);

                            // Log response untuk debugging
                            return response.text().then(text => {
                                console.log('Raw response text:', text);

                                if (!response.ok) {
                                    throw new Error(`HTTP ${response.status}: ${text}`);
                                }

                                try {
                                    return JSON.parse(text);
                                } catch (e) {
                                    console.error('Failed to parse JSON:', e);
                                    throw new Error(`Invalid JSON response: ${text}`);
                                }
                            });
                        })
                        .then(data => {
                            toggleLoading(false);
                            console.log('Manual transfer response data:', data);

                            if (data.error) {
                                Swal.fire({
                                    title: 'Error!',
                                    text: data.error,
                                    icon: 'error'
                                });
                                return;
                            }

                            if (data.order_id) {
                                showSuccessMessage(
                                    'Bukti transfer berhasil dikirim! Pesanan Anda sedang diverifikasi.',
                                    `${window.location.origin}/order/manual-invoice/${data.order_id}`
                                );
                            } else {
                                Swal.fire({
                                    title: 'Error!',
                                    text: 'Response tidak valid dari server',
                                    icon: 'error'
                                });
                            }
                        })
                        .catch(error => {
                            toggleLoading(false);
                            console.error('Manual Transfer Error:', error);
                            Swal.fire({
                                title: 'Error!',
                                text: `Terjadi kesalahan sistem manual transfer: ${error.message}`,
                                icon: 'error'
                            });
                        });
                }
            });
        });
    </script>

    <style>
        .payment-btn.active {
            background-color: #e58609 !important;
            color: white !important;
        }

        .payment-btn:hover {
            transform: translateY(-1px);
            transition: transform 0.2s ease-in-out;
        }

        #manualFields {
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                height: 0;
            }

            to {
                opacity: 1;
                height: auto;
            }
        }

        .loading-overlay {
            background: rgba(255, 255, 255, 0.8);
        }
    </style>
</x-home-layout>