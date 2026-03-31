<x-owner-app-layout>

<div class="p-6 max-w-7xl mx-auto">

    {{-- ── Header ── --}}
    <div class="flex items-start justify-between mb-6">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('owner.warehouse.dashboard') }}"
                    class="text-gray-400 hover:text-gray-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <h1 class="text-2xl font-bold text-gray-900">Log Aktivitas</h1>
            </div>
            <p class="text-sm text-gray-500 ml-7">Seluruh riwayat aktivitas operasional sistem</p>
        </div>
        <div class="text-right">
            <p class="text-2xl font-bold text-gray-800">{{ number_format($logs->total()) }}</p>
            <p class="text-xs text-gray-400">total log</p>
        </div>
    </div>

    {{-- ── Filter ── --}}
    <form method="GET" action="{{ route('owner.warehouse.activity-log') }}"
        class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 mb-6">
        <div class="flex flex-wrap items-end gap-3">
            <div class="flex-1 min-w-[160px]">
                <label class="block text-xs font-semibold text-gray-500 mb-1.5">Cari</label>
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/></svg>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari deskripsi aktivitas..."
                        class="w-full pl-9 pr-3 py-2 border border-gray-200 rounded-lg text-sm bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-300">
                </div>
            </div>
            <div class="min-w-[150px]">
                <label class="block text-xs font-semibold text-gray-500 mb-1.5">Tipe</label>
                <select name="type" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-300">
                    <option value="">Semua Tipe</option>
                    @foreach($types as $t)
                        <option value="{{ $t }}" {{ request('type')===$t?'selected':'' }}>
                            {{ ucfirst(str_replace('_', ' ', $t)) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-[140px]">
                <label class="block text-xs font-semibold text-gray-500 mb-1.5">Modul</label>
                <select name="module" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-300">
                    <option value="">Semua Modul</option>
                    @foreach($modules as $mod)
                        <option value="{{ $mod }}" {{ request('module')===$mod?'selected':'' }}>
                            {{ ucfirst(str_replace('_', ' ', $mod)) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-[120px]">
                <label class="block text-xs font-semibold text-gray-500 mb-1.5">Dari</label>
                <input type="date" name="dari" value="{{ request('dari') }}"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-300">
            </div>
            <div class="min-w-[120px]">
                <label class="block text-xs font-semibold text-gray-500 mb-1.5">Sampai</label>
                <input type="date" name="sampai" value="{{ request('sampai') }}"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-300">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold rounded-lg transition-colors">
                    Filter
                </button>
                <a href="{{ route('owner.warehouse.activity-log') }}"
                    class="px-3 py-2 text-sm text-gray-400 hover:text-gray-700 rounded-lg hover:bg-gray-100 transition-colors">
                    Reset
                </a>
            </div>
        </div>
    </form>

    {{-- ── Log List ── --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">

        @if($logs->isEmpty())
            <div class="py-20 text-center">
                <div class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <p class="text-sm font-medium text-gray-400">Tidak ada log yang cocok dengan filter.</p>
            </div>
        @else
            {{-- Table header --}}
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide w-44">Waktu</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide w-32">Tipe</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide w-28">Modul</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Deskripsi</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide w-36">Oleh</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($logs as $log)
                            @php
                                $typeCfg = match(true) {
                                    str_contains($log->type, 'po_')         => ['bg'=>'bg-blue-100',   'text'=>'text-blue-700'],
                                    str_contains($log->type, 'production_') => ['bg'=>'bg-purple-100', 'text'=>'text-purple-700'],
                                    str_contains($log->type, 'order_')      => ['bg'=>'bg-green-100',  'text'=>'text-green-700'],
                                    str_contains($log->type, 'disposal_')   => ['bg'=>'bg-red-100',    'text'=>'text-red-600'],
                                    str_contains($log->type, 'qc_')         => ['bg'=>'bg-yellow-100', 'text'=>'text-yellow-700'],
                                    str_contains($log->type, 'allocation_') => ['bg'=>'bg-teal-100',   'text'=>'text-teal-700'],
                                    default                                  => ['bg'=>'bg-gray-100',   'text'=>'text-gray-600'],
                                };
                                $actorName = null;
                                if ($log->actor) {
                                    $actorName = $log->actor->name ?? $log->actor->email ?? null;
                                }
                            @endphp
                            <tr class="hover:bg-gray-50/70 transition-colors">
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    <p class="text-xs font-medium text-gray-700">{{ $log->created_at->format('d M Y') }}</p>
                                    <p class="text-xs text-gray-400">{{ $log->created_at->format('H:i:s') }}</p>
                                    <p class="text-xs text-gray-300 mt-0.5">{{ $log->created_at->diffForHumans() }}</p>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold {{ $typeCfg['bg'] }} {{ $typeCfg['text'] }} whitespace-nowrap">
                                        {{ str_replace('_', ' ', $log->type) }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5">
                                    @if($log->module)
                                        <span class="text-xs text-gray-500 capitalize bg-gray-100 px-2 py-0.5 rounded">
                                            {{ str_replace('_', ' ', $log->module) }}
                                        </span>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-gray-700 max-w-sm">
                                    <p class="leading-snug">{{ $log->description }}</p>
                                </td>
                                <td class="px-5 py-3.5">
                                    @if($actorName)
                                        <div class="flex items-center gap-2">
                                            <div class="w-6 h-6 rounded-full bg-orange-100 flex items-center justify-center flex-shrink-0">
                                                <span class="text-xs font-bold text-orange-600">{{ strtoupper(substr($actorName, 0, 1)) }}</span>
                                            </div>
                                            <span class="text-xs text-gray-700 font-medium truncate max-w-[100px]">{{ $actorName }}</span>
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-300 italic">System</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($logs->hasPages())
                <div class="px-5 py-4 border-t border-gray-100 flex items-center justify-between">
                    <p class="text-xs text-gray-400">
                        Menampilkan {{ $logs->firstItem() }}–{{ $logs->lastItem() }} dari {{ number_format($logs->total()) }} log
                    </p>
                    <div>{{ $logs->links() }}</div>
                </div>
            @else
                <div class="px-5 py-3 border-t border-gray-100">
                    <p class="text-xs text-gray-400">{{ number_format($logs->total()) }} log ditampilkan</p>
                </div>
            @endif
        @endif
    </div>

</div>

</x-owner-app-layout>