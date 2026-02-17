<x-owner-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            {{ __('Owner - Dashboard') }}
        </h2>
    </x-slot>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Poppins', sans-serif; background-color: #f9fafb; }
        .brand-orange { background-color: #e58609; }
        .hover\:brand-orange-dark:hover { background-color: #EA580C; }
        .text-brand-orange { color: #e58609; }
        .border-brand-orange { border-color: #e58609; }
        .card-gradient-1 { background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%); border-left: 4px solid #e58609; }
        .stat-card { transition: all 0.3s ease; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -1px rgba(0,0,0,0.03);}
        .stat-card:hover { transform: translateY(-5px); box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05);}
        .dashboard-section { background-color: white; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -1px rgba(0,0,0,0.03); transition: box-shadow 0.3s ease;}
        .dashboard-section:hover { box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -1px rgba(0,0,0,0.05);}
        .user-card { transition: all 0.3s ease; border: 1px solid #e5e7eb;}
        .user-card:hover { border-color: #e58609; transform: translateY(-3px);}
        .modal-bg { background: rgba(0,0,0,0.4); }
        .modal-content { animation: fadeIn .2s; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px);} to { opacity: 1; transform: none; } }
    </style>
    <div class="min-h-screen flex flex-col" x-data="{ showWeight: false, showPrice: false, showTotalLivestock: false }">
        
            <main class="max-w-6xl mx-auto px-4 py-10 space-y-8 flex-1 w-full">            {{-- Stat Cards --}}
            <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">
                @php
                    $penitipCount = $usersa->merge($usersWithDomba)->unique('id')->count();
                    $cards = [
                        [
                            'title'=>'Total Ternak', 
                            'value'=>$kambingCount+$dombaCount, 
                            'bg'=>'card-gradient-1',
                            'clickable' => true
                        ],
                        ['title'=>'Pengguna', 'value'=>$userCount, 'bg'=>'card-gradient-1', 'pct'=>$userPercentageChange],
                        ['title'=>'Penitip', 'value'=>$penitipCount, 'bg'=>'card-gradient-1'],
                        ['title'=>'Pemilik Kambing', 'value'=>$usersa->count(), 'bg'=>'card-gradient-1'],
                        ['title'=>'Pemilik Domba', 'value'=>$usersWithDomba->count(), 'bg'=>'card-gradient-1'],
                    ];
                @endphp
                @foreach($cards as $c)
                    <div 
                        class="p-5 rounded-xl stat-card {{ $c['bg'] }} hover:shadow-lg"
                        @if(isset($c['clickable']))
                            @click="showTotalLivestock = true"
                            style="cursor: pointer;"
                        @endif
                    >
                        <p class="text-xl font-semibold text-gray-800">{{ $c['value'] }}</p>
                        <p class="text-sm text-gray-600 mt-1">{{ $c['title'] }}</p>
                        
                        {{-- Tambahkan teks "Klik untuk detail" untuk card yang bisa diklik --}}
                        @if(isset($c['clickable']))
                            <span class="text-xs text-brand-orange underline cursor-pointer">Klik untuk detail</span>
                        @endif
                        
                        @if(isset($c['pct']))
                            <p class="mt-2 text-xs font-medium {{ $c['pct']>=0?'text-green-600':'text-red-600' }} flex items-center">
                                @if($c['pct'] >= 0)
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd" />
                                    </svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M12 13a1 1 0 100 2h5a1 1 0 001-1V9a1 1 0 10-2 0v2.586l-4.293-4.293a1 1 0 00-1.414 0L8 9.586 3.707 5.293a1 1 0 00-1.414 1.414l5 5a1 1 0 001.414 0L11 9.414 14.586 13H12z" clip-rule="evenodd" />
                                    </svg>
                                @endif
                                {{ number_format(abs($c['pct']),1) }}% sejak bulan lalu
                            </p>
                        @endif
                    </div>
                @endforeach
            </section>

            {{-- KPIs --}}
            <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Avg Weight --}}
                <div class="dashboard-section p-6 cursor-pointer hover:bg-orange-50"
                     @click="showWeight = true">
                    <div class="flex items-start">
                        <div class="bg-orange-100 p-3 rounded-lg mr-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-brand-orange" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-lg font-semibold text-gray-700">Rata-rata Berat (kg)</p>
                            <p class="mt-1 text-2xl font-bold text-gray-900">
                                {{ round(($kambingAvgWeight + $dombaAvgWeight)/2, 1) }}
                            </p>
                            <span class="text-xs text-brand-orange underline cursor-pointer">Klik untuk detail</span>
                        </div>
                    </div>
                </div>
                {{-- Avg Price --}}
                <div class="dashboard-section p-6 cursor-pointer hover:bg-orange-50"
                     @click="showPrice = true">
                    <div class="flex items-start">
                        <div class="bg-orange-100 p-3 rounded-lg mr-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-brand-orange" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-lg font-semibold text-gray-700">Rata-rata Harga (Rp)</p>
                            <p class="mt-1 text-2xl font-bold text-gray-900">
                                Rp{{ number_format(($kambingAvgPrice + $dombaAvgPrice)/2,0,',','.') }}
                            </p>
                            <span class="text-xs text-brand-orange underline cursor-pointer">Klik untuk detail</span>
                        </div>
                    </div>
                </div>
                {{-- Ownership Rate --}}
                <div class="dashboard-section p-6">
                    <div class="flex items-start">
                        <div class="bg-orange-100 p-3 rounded-lg mr-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-brand-orange" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-lg font-semibold text-gray-700">Persentase Pemilik</p>
                            <p class="mt-1 text-2xl font-bold text-gray-900">
                                {{ number_format($ownerPercentage, 1) }}%
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Modal Rata-rata Berat --}}
            <div x-show="showWeight" class="fixed inset-0 flex items-center justify-center modal-bg z-20" style="display: none;" x-transition>
                <div class="bg-white rounded-xl shadow-2xl p-8 modal-content w-full max-w-sm mx-4 sm:mx-auto relative">
                    <button @click="showWeight = false" class="absolute top-2 right-2 text-gray-400 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                    <h3 class="text-lg font-bold mb-4 text-brand-orange">Detail Rata-rata Berat</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span>Kambing</span>
                            <span class="font-semibold">{{ round($kambingAvgWeight, 1) }} kg</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Domba</span>
                            <span class="font-semibold">{{ round($dombaAvgWeight, 1) }} kg</span>
                        </div>
                        <div class="border-t pt-2 flex justify-between">
                            <span>Total Rata-rata</span>
                            <span class="font-bold text-brand-orange">{{ round(($kambingAvgWeight + $dombaAvgWeight)/2, 1) }} kg</span>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Modal Rata-rata Harga --}}
            <div x-show="showPrice" class="fixed inset-0 flex items-center justify-center modal-bg z-20" style="display: none;" x-transition>
                <div class="bg-white rounded-xl shadow-2xl p-8 modal-content w-full max-w-sm mx-4 sm:mx-auto relative">
                    <button @click="showPrice = false" class="absolute top-2 right-2 text-gray-400 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                    <h3 class="text-lg font-bold mb-4 text-brand-orange">Detail Rata-rata Harga</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span>Kambing</span>
                            <span class="font-semibold">Rp{{ number_format($kambingAvgPrice,0,',','.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Domba</span>
                            <span class="font-semibold">Rp{{ number_format($dombaAvgPrice,0,',','.') }}</span>
                        </div>
                        <div class="border-t pt-2 flex justify-between">
                            <span>Total Rata-rata</span>
                            <span class="font-bold text-brand-orange">Rp{{ number_format(($kambingAvgPrice + $dombaAvgPrice)/2,0,',','.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Modal Total Ternak --}}
            <div x-show="showTotalLivestock" class="fixed inset-0 flex items-center justify-center modal-bg z-20" style="display: none;" x-transition>
                <div class="bg-white rounded-xl shadow-2xl p-8 modal-content w-full max-w-sm mx-4 sm:mx-auto relative">
                    <button @click="showTotalLivestock = false" class="absolute top-2 right-2 text-gray-400 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                    <h3 class="text-lg font-bold mb-4 text-brand-orange">Detail Total Ternak</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center p-3 bg-orange-50 rounded-lg">
                            <div class="flex items-center">
                                <span class="font-medium">Kambing</span>
                            </div>
                            <span class="font-bold text-lg">{{ $kambingCount }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center p-3 bg-orange-50 rounded-lg">
                            <div class="flex items-center">
                                <span class="font-medium">Domba</span>
                            </div>
                            <span class="font-bold text-lg">{{ $dombaCount }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center p-3 border-t-2 border-brand-orange mt-3">
                            <span class="font-semibold">Total Ternak</span>
                            <span class="font-bold text-xl text-brand-orange">{{ $kambingCount + $dombaCount }}</span>
                        </div>
                    </div>
                </div>
            </div>

{{-- Produk For Sale Terlama --}}
<section class="dashboard-section p-6">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-bold text-gray-800">Produk For Sale Terlama</h3>
        <span class="text-xs text-brand-orange font-medium">Status: For Sale = Yes</span>
    </div>
    <div class="h-80"> {{-- Tinggi chart --}}
        <canvas id="forSaleChart"></canvas>
    </div>
    <div class="mt-4 text-xs text-gray-600">
        <p>Menampilkan 10 produk dengan status for_sale terlama (kambing & domba)</p>
    </div>
</section>

            {{-- Overview Table + Chart --}}
            <section class="grid grid-cols-1 xl:grid-cols-5 gap-6">
                <div class="xl:col-span-2 dashboard-section p-6 overflow-x-auto">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-800">Top Users</h3>
                        <span class="text-xs text-brand-orange font-medium">Pemilik Ternak Terbanyak</span>
                    </div>
                    <table class="w-full text-sm">
                        <thead class="text-left bg-gray-50 text-gray-600 uppercase text-xs">
                            <tr>
                                <th class="p-3 font-medium">User</th>
                                <th class="p-3 font-medium text-center">Kambing</th>
                                <th class="p-3 font-medium text-center">Domba</th>
                                <th class="p-3 font-medium text-center">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($users as $u)
                                <tr class="hover:bg-orange-50">
                                    <td class="p-3 font-medium text-gray-700">{{ $u->name }}</td>
                                    <td class="p-3 text-center">{{ $u->kambings_count }}</td>
                                    <td class="p-3 text-center">{{ $u->domba_count }}</td>
                                    <td class="p-3 text-center font-semibold text-brand-orange">{{ $u->kambings_count + $u->domba_count }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="xl:col-span-3 dashboard-section p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-800">Pertumbuhan Ternak Mingguan</h3>
                    </div>
                    <canvas id="weeklyGrowthChart" class="w-full" height="250"></canvas>
                </div>
            </section>

            {{-- Recent Users --}}
            <section>
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-900">Penitip Terbaru</h3>
                    <a href="{{ route('owner.penitip') }}" class="text-sm text-brand-orange hover:underline font-medium flex items-center">
                        Lihat semua
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </div>
                <ul class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach($usersa as $u)
                        <li class="user-card bg-white p-4 rounded-xl shadow-sm flex items-center space-x-4">
                            <div class="flex-shrink-0">
                               <img src="{{ $u->profile_picture 
                                      ? asset('uploads/profilImage/'.$u->profile_picture)
                                      : asset('uploads/1721131815_default.png') }}"
                             class="border-2 rounded-xl w-12 h-12 object-cover"/>
                            </div>
                            <div class="min-w-0">
                                <p class="font-semibold text-gray-800 truncate">{{ $u->name }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ $u->email }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </section>
        </main>
    </div>
    {{-- Chart.js script --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('weeklyGrowthChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($weeklyLabels) !!},
                datasets: [
                    {
                        label: 'Kambing',
                        data: {!! json_encode($weeklyKambingCounts) !!},
                        fill: false,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        tension: 0.1
                    },
                    {
                        label: 'Domba',
                        data: {!! json_encode($weeklyDombaCounts) !!},
                        fill: false,
                        borderColor: 'rgba(255, 99, 132, 1)',
                        tension: 0.1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 3,
                scales: {
                    x: { grid: { display: false } },
                    y: { beginAtZero: true }
                },
                elements: {
                    point: { radius: 3 }
                }
            }
        });
        // For Sale Chart - Format Sederhana
    const forSaleCtx = document.getElementById('forSaleChart').getContext('2d');
    
    const forSaleData = @json($forSaleChartData);
    
    const colors = forSaleData.product_types.map(type => 
        type === 'kambing' ? 'rgba(229, 134, 9, 0.8)' : 'rgba(229, 134, 9, 0.8)'
    );

    new Chart(forSaleCtx, {
        type: 'bar',
        data: {
            labels: forSaleData.labels,
            datasets: [{
                label: 'Lama For Sale (Hari)',
                data: forSaleData.days_on_sale,
                backgroundColor: colors,
                borderColor: forSaleData.product_types.map(type => 
                    type === 'kambing' ? 'rgba(229, 134, 9, 1)' : 'rgba(229, 134, 9, 1)'
                ),
                borderWidth: 2,
                borderRadius: 4,
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        afterLabel: function(context) {
                            const index = context.dataIndex;
                            const date = forSaleData.dates[index];
                            const type = forSaleData.product_types[index];
                            return `Tanggal: ${date}\nTipe: ${type === 'kambing' ? 'Kambing' : 'Domba'}`;
                        }
                    }
                }
            },
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Lama For Sale (Hari)'
                    },
                    grid: {
                        display: false
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Nama Produk'
                    },
                    ticks: {
                        autoSkip: false
                    }
                }
            }
        }
    });
    </script>
</x-owner-app-layout>