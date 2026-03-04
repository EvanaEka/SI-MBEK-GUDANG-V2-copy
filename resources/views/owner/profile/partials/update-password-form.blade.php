@php
    $owner = auth('owner')->user();
@endphp

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Perbarui Kata Sandi') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Pastikan akun Anda menggunakan kata sandi yang panjang dan acak untuk tetap aman.') }}
        </p>
    </header>

    <form method="POST" action="{{ route('owner.password.change') }}" class="mt-6 space-y-6">
        @csrf

        {{-- 🔐 Kata sandi saat ini HANYA muncul kalau BUKAN first login --}}
        @if (!$owner->must_change_password)
            <div>
                <x-input-label for="update_password_current_password" :value="__('Kata Sandi Saat Ini')" />
                <x-text-input id="update_password_current_password" name="current_password" type="password"
                    class="mt-1 block w-full focus:ring-orange-400 focus:border-orange-400"
                    autocomplete="current-password" />
                <x-input-error :messages="$errors->get('current_password')" class="mt-2" />
            </div>
        @endif

        <div>
            <x-input-label for="update_password_password" :value="__('Kata Sandi Baru')" />
            <x-text-input id="update_password_password" name="password" type="password"
                class="mt-1 block w-full focus:ring-orange-400 focus:border-orange-400" autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" :value="__('Konfirmasi Kata Sandi')" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password"
                class="mt-1 block w-full focus:ring-orange-400 focus:border-orange-400" autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button class="bg-brand-orange hover:bg-orange-700 focus:ring-orange-500">
                {{ __('Simpan') }}
            </x-primary-button>

            @if (session('success'))
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                <script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: '{{ session('success') }}',
                        timer: 3000,
                        showConfirmButton: false
                    });
                </script>
            @endif
        </div>
    </form>
</section>