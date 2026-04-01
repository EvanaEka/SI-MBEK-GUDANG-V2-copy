<x-admin-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            {{ __('Admin - List Kambing') }}
        </h2>
    </x-slot>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f9fafb;
        }

        .brand-orange {
            background-color: #e58609;
        }

        .hover\:brand-orange-dark:hover {
            background-color: #d97b08;
        }

        .text-brand-orange {
            color: #e58609;
        }

        .border-brand-orange {
            border-color: #e58609;
        }

        .stat-card {
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        }

        .dashboard-section {
            background-color: white;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            transition: box-shadow 0.3s ease;
        }

        .dashboard-section:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .user-card {
            transition: all 0.3s ease;
            border: 1px solid #e5e7eb;
        }

        .user-card:hover {
            border-color: #e58609;
            transform: translateY(-3px);
        }

        .header-gradient {
            background: linear-gradient(135deg, #FFF 0%, #FFEDD5 100%);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .info-card {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
        }

        .status-yes {
            background-color: #dcfce7;
            color: #166534;
        }

        .status-no {
            background-color: #fee2e2;
            color: #b91c1c;
        }

        /* Responsive tweaks */
        @media (max-width: 768px) {
            .dashboard-section {
                border-radius: 8px;
            }

            .info-card {
                padding: 1rem;
            }
        }
    </style>

    <div class="min-h-screen flex flex-col bg-gray-50" x-data="{ open: false, kambing: null }">
        <main class="max-w-5xl mx-auto py-8 w-full">
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
                    {{ session('success') }}
                </div>
            @endif

            <div class="dashboard-section overflow-hidden">
                <div class="brand-orange p-5 flex flex-col md:flex-row justify-between items-center gap-4">
                    <div class="flex items-center w-full md:w-auto">
                        <a href="{{ route('admin.listkambing') }}"
                            class="text-white hover:text-orange-200 font-medium flex items-center mr-3">
                            <svg class="w-5 h-5 mr-1" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                <path stroke="#FFFFFF" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 12h14M5 12l4-4m-4 4 4 4" />
                            </svg>
                        </a>
                        <h3 class="text-lg font-medium text-white">
                            Detail Kambing ID: {{ $kambings->id }}
                        </h3>
                    </div>
                    <div class="flex gap-2 w-full md:w-auto justify-end">
                        <a href="{{ route('admin.kambing.monitoring', $kambings->id) }}"
                            class="bg-white text-brand-orange px-4 py-2 rounded-md shadow hover:bg-gray-100 flex items-center">
                            <svg class="w-5 h-5 mr-1" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M4 4.5V19a1 1 0 0 0 1 1h15M7 14l4-4 4 4 5-5m0 0h-3.207M20 9v3.207" />
                            </svg>
                            Monitoring
                        </a>
                        <button
                            class="bg-white text-brand-orange px-4 py-2 rounded-md shadow hover:bg-gray-100 flex items-center"
                            @click="open = true; kambing = {{ $kambings }}">
                            <svg class="w-5 h-5 mr-1" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="m14.3 4.8 2.9 2.9M7 7H4a1 1 0 0 0-1 1v10c0 .6.4 1 1 1h11c.6 0 1-.4 1-1v-4.5m2.4-10a2 2 0 0 1 0 3l-6.8 6.8L8 14l.7-3.6 6.9-6.8a2 2 0 0 1 2.8 0Z" />
                            </svg>
                            Edit
                        </button>
                    </div>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Informasi Utama -->
                        <div>
                            <div class="info-card">
                                <h4 class="text-xl font-bold text-gray-800 mb-4 pb-2 border-b border-gray-200">
                                    Informasi Utama</h4>
                                <div class="space-y-4">
                                    <div class="flex items-start">
                                        <span class="text-gray-600 font-medium w-1/3">Nama</span>
                                        <span class="text-gray-600">:</span>
                                        <span class="text-gray-800 font-medium ml-2">{{ $kambings->name }}</span>
                                    </div>
                                    <div class="flex items-start">
                                        <span class="text-gray-600 font-medium w-1/3">Pemilik</span>
                                        <span class="text-gray-600">:</span>
                                        <span class="text-gray-800 font-medium ml-2">
                                            {{ $kambings->user ? $kambings->user->name : '-' }}
                                        </span>
                                    </div>
                                    <div class="flex items-start">
                                        <span class="text-gray-600 font-medium w-1/3">Tanggal Lahir</span>
                                        <span class="text-gray-600">:</span>
                                        <span class="text-gray-800 font-medium ml-2">
                                            {{ \Carbon\Carbon::parse($kambings->tanggal_lahir)->format('d M Y') }}
                                        </span>
                                    </div>
                                    @if ($kambings->umurAwal())
                                        <div class="flex items-start">
                                            <span class="text-gray-600 font-medium w-1/3">Umur Awal</span>
                                            <span class="text-gray-600">:</span>
                                            <span class="text-gray-800 font-medium ml-2">
                                                {{ $kambings->umurAwal() }}
                                            </span>
                                        </div>
                                    @endif
                                    <div class="flex items-start">
                                        <span class="text-gray-600 font-medium w-1/3">Umur Sekarang</span>
                                        <span class="text-gray-600">:</span>
                                        <span class="text-gray-800 font-medium ml-2">
                                            {{ $kambings->hitungUmur() }}
                                        </span>
                                    </div>
                                    <div class="flex items-start">
                                        <span class="text-gray-600 font-medium w-1/3">Jenis</span>
                                        <span class="text-gray-600">:</span>
                                        <span class="text-gray-800 font-medium ml-2">
                                            {{ $kambings->type_goat }}
                                        </span>
                                    </div>
                                    <div class="flex items-start">
                                        <span class="text-gray-600 font-medium w-1/3">Jenis Kelamin</span>
                                        <span class="text-gray-600">:</span>
                                        <span class="text-gray-800 font-medium ml-2">
                                            {{ $kambings->jenis_kelamin }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <!-- Informasi Berat -->
                            <div class="info-card">
                                <h4 class="text-xl font-bold text-gray-800 mb-4 pb-2 border-b border-gray-200">
                                    Informasi Berat</h4>
                                <div class="space-y-4">
                                    <div class="flex items-start">
                                        <span class="text-gray-600 font-medium w-1/3">Berat Awal</span>
                                        <span class="text-gray-600">:</span>
                                        <span class="text-gray-800 font-medium ml-2">
                                            {{ $kambings->weight }} kg
                                        </span>
                                    </div>
                                    <div class="flex items-start">
                                        <span class="text-gray-600 font-medium w-1/3">Berat Sekarang</span>
                                        <span class="text-gray-600">:</span>
                                        <span class="text-gray-800 font-medium ml-2">
                                            {{ $kambings->weight_now }} kg
                                        </span>
                                    </div>
                                    @php
                                        $selisih = $kambings->weight_now - $kambings->weight;
                                    @endphp
                                    <div class="flex items-start">
                                        <span class="text-gray-600 font-medium w-1/3">Perkembangan</span>
                                        <span class="text-gray-600">:</span>
                                        <span
                                            class="font-medium ml-2 {{ $selisih >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $selisih >= 0 ? '+' : '-' }}{{ abs($selisih) }} kg
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Foto dan Status -->
                        <div>
                            <!-- Foto Kambing -->
                            <div class="info-card">
                                <h4 class="text-xl font-bold text-gray-800 mb-4 pb-2 border-b border-gray-200">Foto
                                    Kambing</h4>
                                <div class="flex justify-center">
                                    @if ($kambings->image)
                                        <img src="{{ asset('storage/' . $kambings->image) }}" 
                                            loading="lazy"
                                            alt="gambar kambing"
                                            class="w-full max-w-xs h-64 object-cover rounded-lg shadow-md cursor-pointer"
                                            onclick="showImagePopup('{{ asset('storage/' . $kambings->image) }}')" />
                                    @else
                                        <img 
                                            src="{{ asset('uploads/default.png') }}"
                                            loading="lazy"
                                            alt="gambar kambing"
                                            class="w-full max-w-xs h-64 object-cover rounded-lg shadow-md" />
                                    @endif
                                </div>
                            </div>
                            <!-- Informasi Status -->
                            <div class="info-card">
                                <h4 class="text-xl font-bold text-gray-800 mb-4 pb-2 border-b border-gray-200">
                                    Status</h4>
                                <div class="space-y-4">
                                    <div class="flex items-start">
                                        <span class="text-gray-600 font-medium w-1/3">Status Vaksin</span>
                                        <span class="text-gray-600">:</span>
                                        <span class="text-gray-800 font-medium ml-2">
                                            {{ $kambings->faksin_status }}
                                        </span>
                                    </div>
                                    <div class="flex items-start">
                                        <span class="text-gray-600 font-medium w-1/3">Status Kesehatan</span>
                                        <span class="text-gray-600">:</span>
                                        <span class="text-gray-800 font-medium ml-2">
                                            {{ $kambings->healt_status }}
                                        </span>
                                    </div>
                                    <div class="flex items-start">
                                        <span class="text-gray-600 font-medium w-1/3">Status Dijual</span>
                                        <span class="text-gray-600">:</span>
                                        <span class="ml-2">
                                            @if ($kambings->for_sale === 'yes')
                                                <span class="status-badge status-yes">Ya</span>
                                            @else
                                                <span class="status-badge status-no">Tidak</span>
                                            @endif
                                        </span>
                                    </div>
                                    @if ($kambings->for_sale === 'yes')
                                        <div class="flex items-start">
                                            <span class="text-gray-600 font-medium w-1/3">Harga</span>
                                            <span class="text-gray-600">:</span>
                                            <span class="text-xl font-bold text-brand-orange ml-2">
                                                Rp {{ number_format($kambings->harga, 0, ',', '.') }}
                                            </span>
                                        </div>
                                    @endif
                                    <div class="flex items-start">
                                        <span class="text-gray-600 font-medium w-1/3">Tanggal Dibuat</span>
                                        <span class="text-gray-600">:</span>
                                        <span class="text-gray-800 font-medium ml-2">
                                            {{ $kambings->created_at->format('d M Y') }}
                                        </span>
                                    </div>
                                    <div class="flex items-start">
                                        <span class="text-gray-600 font-medium w-1/3">Terakhir Diperbarui</span>
                                        <span class="text-gray-600">:</span>
                                        <span class="text-gray-800 font-medium ml-2">
                                            {{ $kambings->updated_at->format('d M Y') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Modal Edit -->
        <div x-show="open"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 transition-opacity duration-300"
            x-cloak>
            <div class="bg-white rounded-lg shadow-xl w-full max-w-lg mx-auto max-h-[90vh] overflow-y-auto"
                @click.outside="open = false">
                <div class="brand-orange p-4 rounded-t-lg">
                    <h2 class="text-xl font-bold text-white">Edit Kambing</h2>
                </div>
                <div class="p-6">
                    <form method="POST"
                        action="{{ route('admin.kambings.update', ['kambing' => $kambings->id]) }}"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kambing</label>
                                    <input type="text" name="name" x-model="kambing.name" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-orange">
                                </div>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                                    <input type="date" name="tanggal_lahir" x-model="kambing.tanggal_lahir"
                                        max="{{ date('Y-m-d') }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-orange">
                                </div>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Pemilik</label>
                                    <select name="user_id" x-model="kambing.user_id" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-orange">
                                        <option value="" disabled>Pilih Pemilik</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}">
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kambing</label>
                                    <select name="type_goat" x-model="kambing.type_goat" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-orange">
                                        <option value="Etawa">Etawa</option>
                                        <option value="Boer">Boer</option>
                                        <option value="Skeang">Skeang</option>
                                        <option value="Saaren">Saaren</option>
                                    </select>
                                </div>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                                    <select name="jenis_kelamin" x-model="kambing.jenis_kelamin" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-orange">
                                        <option value="Jantan">Jantan</option>
                                        <option value="Betina">Betina</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Berat Awal (kg)</label>
                                    <input type="number" step="0.1" name="weight" x-model="kambing.weight"
                                        required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-orange">
                                </div>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Berat Sekarang
                                        (kg)</label>
                                    <input type="number" step="0.1" name="weight_now"
                                        x-model="kambing.weight_now" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-orange">
                                </div>
                                <div>
                                    <!-- Pilih Status -->
                                    <div class="mb-4">
                                        <label for="faksin_status" class="block text-sm font-bold mb-2">Status
                                            Vaksin</label>
                                        <select id="faksin_status"
                                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:ring-orange-400 focus:border-orange-400 focus:outline-none focus:shadow-outline"
                                            required>
                                            <option value="">-- Pilih Status --</option>
                                            <option value="Aktif"
                                                {{ $kambings->faksin_status != 'Tidak Aktif' ? 'selected' : '' }}>Aktif
                                            </option>
                                            <option value="Tidak Aktif"
                                                {{ $kambings->faksin_status == 'Tidak Aktif' ? 'selected' : '' }}>Tidak
                                                Aktif</option>
                                        </select>
                                    </div>

                                    <!-- Pilih Jenis Vaksin (muncul kalau status = Aktif) -->
                                    <div class="mb-4" id="jenis_vaksin_wrapper" style="display: none;">
                                        <label for="jenis_vaksin" class="block text-sm font-bold mb-2">Jenis
                                            Vaksin</label>
                                        <select id="jenis_vaksin"
                                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:ring-orange-400 focus:border-orange-400 focus:outline-none focus:shadow-outline">
                                            <option value="">-- Pilih Jenis Vaksin --</option>
                                            <option value="Vaksin PMK"
                                                {{ $kambings->faksin_status == 'Vaksin PMK' ? 'selected' : '' }}>Vaksin PMK
                                            </option>
                                            <option value="Vaksin Antraks"
                                                {{ $kambings->faksin_status == 'Vaksin Antraks' ? 'selected' : '' }}>Vaksin Antraks
                                            </option>
                                            <option value="Vaksin Brucellosis"
                                                {{ $kambings->faksin_status == 'Vaksin Brucellosis' ? 'selected' : '' }}>Vaksin Brucellosis
                                            </option>
                                        </select>
                                    </div>

                                    <!-- Hidden input yang dikirim ke server -->
                                    <input type="hidden" name="faksin_status" id="faksin_status_hidden">

                                </div>

                                <script>
                                    const statusSelect = document.getElementById('faksin_status');
                                    const jenisWrapper = document.getElementById('jenis_vaksin_wrapper');
                                    const jenisSelect = document.getElementById('jenis_vaksin');
                                    const hiddenInput = document.getElementById('faksin_status_hidden');

                                    function updateHiddenInput() {
                                        if (statusSelect.value === "Aktif") {
                                            jenisWrapper.style.display = "block";
                                            hiddenInput.value = jenisSelect.value; // hanya simpan jenis vaksin
                                        } else {
                                            jenisWrapper.style.display = "none";
                                            hiddenInput.value = statusSelect.value; // simpan "Tidak Aktif"
                                        }
                                    }

                                    // Event listener
                                    statusSelect.addEventListener('change', updateHiddenInput);
                                    jenisSelect.addEventListener('change', updateHiddenInput);

                                    // Jalankan pertama kali supaya data lama muncul
                                    updateHiddenInput();
                                </script>

                                <div class="mb-4">
                                    <label for="health_status" class="block text-sm font-bold mb-2">Status
                                        Kesehatan</label>

                                    <!-- Dropdown -->
                                    <select id="health_status_select"
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:ring-orange-400 focus:border-orange-400 focus:outline-none focus:shadow-outline"
                                        onchange="toggleOtherHealthStatus(this)" required>
                                        <option value="">-- Pilih Status --</option>
                                        <option value="Sehat"
                                            {{ $kambings->healt_status == 'Sehat' ? 'selected' : '' }}>Sehat</option>
                                        <option value="Tidak Sehat"
                                            {{ $kambings->healt_status == 'Tidak Sehat' ? 'selected' : '' }}>Tidak
                                            Sehat</option>
                                        <option value="Lainnya"
                                            {{ $kambings->healt_status != 'Sehat' && $kambings->healt_status != 'Tidak Sehat' ? 'selected' : '' }}>
                                            Lainnya</option>
                                    </select>

                                    <!-- Input teks custom -->
                                    <input type="text" id="health_status_custom"
                                        placeholder="Masukkan status kesehatan"
                                        value="{{ $kambings->healt_status != 'Sehat' && $kambings->healt_status != 'Tidak Sehat' ? $kambings->healt_status : '' }}"
                                        class="mt-2 hidden shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:ring-orange-400 focus:border-orange-400 focus:outline-none focus:shadow-outline" />

                                    <!-- Hidden input yang dikirim ke server -->
                                    <input type="hidden" name="healt_status" id="health_status_final"
                                        value="{{ $kambings->healt_status }}">
                                </div>
                                <script>
                                    const healthSelect = document.getElementById('health_status_select');
                                    const healthCustom = document.getElementById('health_status_custom');
                                    const healthHidden = document.getElementById('health_status_final');

                                    function toggleOtherHealthStatus(el) {
                                        if (el.value === "Lainnya") {
                                            healthCustom.classList.remove("hidden");
                                            healthHidden.value = healthCustom.value; // simpan input custom
                                        } else {
                                            healthCustom.classList.add("hidden");
                                            healthHidden.value = el.value; // simpan langsung dari dropdown
                                        }
                                    }

                                    // Saat user mengetik di input custom, update hidden input
                                    healthCustom.addEventListener("input", function() {
                                        healthHidden.value = this.value;
                                    });

                                    // Jalankan pertama kali (agar old()/edit data tetap muncul)
                                    toggleOtherHealthStatus(healthSelect);
                                </script>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Untuk Dijual</label>
                                    <select name="for_sale" x-model="kambing.for_sale" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-orange">
                                        <option value="yes">Ya</option>
                                        <option value="no">Tidak</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <template x-if="kambing.for_sale === 'yes'">
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Harga (Rp)</label>
                                    <input type="number" name="harga" x-model="kambing.harga"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-orange"
                                        placeholder="Masukkan harga">
                                </div>
                            </template>
                        </div>
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Foto Kambing</label>
                            <input type="file" name="image"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-orange">
                        </div>
                        <div class="flex justify-end space-x-3 mt-6">
                            <button type="button" @click="open = false"
                                class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-100 transition-colors">
                                Batal
                            </button>
                            <button type="submit"
                                class="px-4 py-2 bg-brand-orange text-white rounded-md hover:bg-orange-700 transition-colors">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            function showImagePopup(src) {
                const popup = document.createElement('div');
                popup.className = 'fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-80 p-4';
                popup.innerHTML = `
                <div class="relative max-w-4xl w-full">
                    <button onclick="this.parentElement.parentElement.remove()" 
                        class="absolute top-4 right-4 text-white bg-black bg-opacity-50 rounded-full p-2 hover:bg-opacity-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                    <img src="${src}" class="max-h-[90vh] w-auto mx-auto" alt="Preview">
                </div>
            `;
                document.body.appendChild(popup);
            }

            function toggleOtherHealtStatus(select) {
                const customInput = document.getElementById('healt_status_custom');
                const finalInput = document.getElementById('healt_status_final');

                if (select.value === 'Lainnya') {
                    customInput.classList.remove('hidden');
                    customInput.value = '';
                    finalInput.value = '';
                    customInput.addEventListener('input', function() {
                        finalInput.value = this.value;
                    });
                } else {
                    customInput.classList.add('hidden');
                    finalInput.value = select.value;
                }
            }
        </script>
    </div>
</x-admin-app-layout>
