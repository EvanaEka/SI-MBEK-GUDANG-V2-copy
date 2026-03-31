@props(['active'])

@php
// Jika aktif (diklik): Background abu-abu, teks hitam/abu gelap
// Jika tidak aktif: Background transparan (tapi bisa di-hover jadi abu), teks hitam/abu gelap
$classes = ($active ?? false)
            ? 'flex items-center p-2 text-base font-medium text-gray-900 bg-gray-100 rounded-lg group w-full transition-colors'
            : 'flex items-center p-2 text-base font-normal text-gray-900 rounded-lg hover:bg-gray-100 group w-full transition-colors';

// Jika aktif (diklik): Icon jadi abu-abu/hitam
// Jika tidak aktif: Icon warna Orange (tapi kalau di-hover berubah jadi abu-abu/hitam)
$iconClasses = ($active ?? false)
            ? 'text-gray-900'
            : 'text-brand-orange group-hover:text-gray-900';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{-- Slot untuk Icon --}}
    <div class="flex-shrink-0 {{ $iconClasses }} transition duration-75">
        {{ $icon }}
    </div>
    
    {{-- Slot untuk Teks Menu --}}
    <span class="ml-3 flex-1 whitespace-nowrap">
        {{ $slot }}
    </span>
</a>