<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Informasi Profil') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Perbarui foto profil, nama, dan alamat email Anda.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    {{-- 🔥 WAJIB TAMBAH ENCTYPE BIAR BISA UPLOAD GAMBAR 🔥 --}}
    <form method="post" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        {{-- 📸 Bagian Upload Foto Profil --}}
        <div class="flex items-center gap-6">
            <div class="w-20 h-20 rounded-full overflow-hidden bg-gray-100 border border-gray-200 shadow-sm flex-shrink-0">
                @php
                    // Pastikan variabel $user di-load, fallback ke logo kalau belum ada foto
                    $avatar = $user->profile_picture ? asset('storage/admin_avatars/' . $user->profile_picture) : asset('logo/logosiembek.png');
                @endphp
                <img src="{{ $avatar }}" alt="Profile Picture" class="w-full h-full object-cover">
            </div>
            <div class="flex-1">
                <x-input-label for="profile_picture" :value="__('Foto Profil')" />
                <input type="file" id="profile_picture" name="profile_picture" accept="image/*" 
                    class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-orange-50 file:text-brand-orange hover:file:bg-orange-100 transition-colors">
                <p class="text-xs text-gray-400 mt-1">Format: JPG, PNG. Maksimal 2MB.</p>
                <x-input-error class="mt-2" :messages="$errors->get('profile_picture')" />
            </div>
        </div>

        <div>
            <x-input-label for="name" :value="__('Nama')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full focus:ring-orange-400 focus:border-orange-400" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full focus:ring-orange-400 focus:border-orange-400" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Email Anda belum terverifikasi.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Klik di sini untuk mengirim ulang email verifikasi.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('Tautan verifikasi baru telah dikirim ke alamat email Anda.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button class="bg-brand-orange hover:bg-orange-700 focus:ring-orange-500">{{ __('Simpan') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-green-600"
                >{{ __('Tersimpan.') }}</p>
            @endif
        </div>
    </form>
</section>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if (session('status') === 'profile-updated')
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Profil Diperbarui',
            text: 'Data profil admin berhasil diperbarui.',
            timer: 3000,
            showConfirmButton: false
        });
    </script>
@endif