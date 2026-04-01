<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Kambing;
use App\Models\Domba;
use App\Models\Perjanjian;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // === KAMBING & PERUBAHAN BULANAN ===
        $kambingCount = Kambing::count();
        $kambingThisMonth = Kambing::whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->count();
        $kambingLastMonth = Kambing::whereBetween('created_at', [Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()])->count();
        $kambingPercentageChange = $kambingLastMonth > 0 ? (($kambingThisMonth - $kambingLastMonth) / $kambingLastMonth) * 100 : ($kambingThisMonth > 0 ? 100 : 0);

        // === DOMBA & PERUBAHAN BULANAN ===
        $dombaCount = Domba::count();
        $dombaThisMonth = Domba::whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->count();
        $dombaLastMonth = Domba::whereBetween('created_at', [Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()])->count();
        $dombaPercentageChange = $dombaLastMonth > 0 ? (($dombaThisMonth - $dombaLastMonth) / $dombaLastMonth) * 100 : ($dombaThisMonth > 0 ? 100 : 0);

        // === USER & PERUBAHAN BULANAN ===
        $userCount = User::count();
        $userThisMonth = User::whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->count();
        $userLastMonth = User::whereBetween('created_at', [Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()])->count();
        $userPercentageChange = $userLastMonth > 0 ? (($userThisMonth - $userLastMonth) / $userLastMonth) * 100 : ($userThisMonth > 0 ? 100 : 0);

        // === OVERVIEW TOP USERS ===
        $users = User::withCount(['kambings', 'domba'])
            ->orderBy('kambings_count', 'desc')
            ->orderBy('domba_count', 'desc')
            ->take(7)
            ->get();

        // === PENITIP TERBARU ===
        $usersa = User::where(function ($q) {
            $q->has('kambings')->orHas('domba');
        })
            ->with([
                'kambings' => fn($q) => $q->orderBy('created_at', 'desc'),
                'domba' => fn($q) => $q->orderBy('created_at', 'desc'),
            ])
            ->get()
            ->sortByDesc(function ($user) {
                $lastKambing = $user->kambings->first();
                $lastDomba = $user->domba->first();
                return max($lastKambing?->created_at, $lastDomba?->created_at);
            })
            ->take(5);

        // === PENGGUNA YANG PUNYA DOMBA ===
        $usersWithDomba = User::has('domba')->get();

        // === USER PEMILIK (yang punya kambing atau domba) ===
        $usersWithOwnership = User::whereHas('kambings')
            ->orWhereHas('domba')
            ->distinct()
            ->count();

        $ownerPercentage = $userCount > 0
            ? ($usersWithOwnership / $userCount) * 100
            : 0;

        // === RATA-RATA BERAT & HARGA ===
        $kambingAvgWeight = Kambing::avg('weight_now');
        $dombaAvgWeight = Domba::avg('weight_now');
        $kambingAvgPrice = Kambing::where('for_sale', 'yes')->avg('harga');
        $dombaAvgPrice = Domba::where('for_sale', 'yes')->avg('harga');

        // === DATA CHART MINGGUAN (4 MINGGU TERAKHIR) ===
        $weeklyLabels = [];
        $weeklyKambingCounts = [];
        $weeklyDombaCounts = [];
        $now = Carbon::now()->startOfWeek();

        for ($i = 4; $i >= 1; $i--) {
            $weekStart = $now->copy()->subWeeks($i);
            $weekEnd = $weekStart->copy()->endOfWeek();

            $weeklyLabels[] = 'Minggu ke-' . (5 - $i);

            $weeklyKambingCounts[] = Kambing::whereBetween('created_at', [$weekStart, $weekEnd])->count();
            $weeklyDombaCounts[] = Domba::whereBetween('created_at', [$weekStart, $weekEnd])->count();
        }
        // === DATA PRODUK FOR_SALE TERLAMA ===
        $kambingForSale = Kambing::where('for_sale', 'yes')
            ->select('id', 'name', 'created_at', 'updated_at', DB::raw("'kambing' as product_type"))
            ->get();

        $dombaForSale = Domba::where('for_sale', 'yes')
            ->select('id', 'name', 'created_at', 'updated_at', DB::raw("'domba' as product_type"))
            ->get();

        // Gabungkan dan urutkan berdasarkan tanggal for_sale terlama
        $allForSale = $kambingForSale->concat($dombaForSale)
            ->map(function ($item) {
                // Gunakan for_sale_date jika ada, jika tidak gunakan created_at
                $item->for_sale_date = $item->for_sale_date ? Carbon::parse($item->for_sale_date) : $item->created_at;
                $item->days_on_sale = $item->for_sale_date->diffInDays(now());
                return $item;
            })
            ->sortBy('for_sale_date') // Urutkan dari yang terlama
            ->take(10) // Ambil 10 teratas
            ->values();

        // Siapkan data untuk chart
        $forSaleChartData = [
            'labels' => $allForSale->pluck('name')->toArray(),
            'dates' => $allForSale->map(function ($item) {
                return $item->for_sale_date->format('Y-m-d');
            })->toArray(),
            'days_on_sale' => $allForSale->pluck('days_on_sale')->toArray(),
            'product_types' => $allForSale->pluck('product_type')->toArray()
        ];

        return view('owner.dashboard', compact('kambingCount', 'kambingThisMonth', 'kambingLastMonth', 'kambingPercentageChange', 'dombaCount', 'dombaThisMonth', 'dombaLastMonth', 'dombaPercentageChange', 'userCount', 'userThisMonth', 'userLastMonth', 'userPercentageChange', 'users', 'usersa', 'usersWithDomba', 'ownerPercentage', 'kambingAvgWeight', 'dombaAvgWeight', 'kambingAvgPrice', 'dombaAvgPrice', 'weeklyLabels', 'weeklyKambingCounts', 'weeklyDombaCounts', 'forSaleChartData'));
    }

    public function perjanjian()
    {
        $users = User::withCount('kambings')->orderBy('kambings_count', 'desc')->take(7)->get();
        return view('owner.perjanjian', compact('users'));
    }
    public function penjualan(Request $request)
    {
        // Query dasar (existing code)
        $ordersQuery = \App\Models\Order::with(['user', 'orderable'])->latest();

        // === FILTER === (existing code)
        if ($request->status && $request->status !== 'all') {
            $ordersQuery->where('status', $request->status);
        }
        if ($request->payment_method && $request->payment_method !== 'all') {
            $ordersQuery->where('payment_method', $request->payment_method);
        }
        if ($request->start_date) {
            $ordersQuery->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $ordersQuery->whereDate('created_at', '<=', $request->end_date);
        }
        if ($request->search) {
            $ordersQuery->where(function ($q) use ($request) {
                $q->where('order_id', 'like', '%' . $request->search . '%')->orWhereHas('user', function ($q2) use ($request) {
                    $q2->where('name', 'like', '%' . $request->search . '%')->orWhere('email', 'like', '%' . $request->search . '%');
                });
            });
        }

        // === PAGINATION === (existing code)
        $orders = $ordersQuery->paginate(10);

        // === STATISTIK (existing code) ===
        $filteredQuery = clone $ordersQuery;
        $totalPenjualan = $orders->total();
        $totalPendapatan = (clone $filteredQuery)->whereIn('status', ['settlement', 'capture', 'success'])->sum('gross_amount');
        $pembeliAktif = (clone $filteredQuery)
            ->whereIn('status', ['settlement', 'capture', 'success'])
            ->distinct('user_id')
            ->count('user_id');

        // === DATA TREND PENJUALAN (fixed code) ===
        $salesTrendQuery = clone $ordersQuery;

        // Hapus semua ORDER BY yang ada dari query dasar
        $salesTrendQuery->reorder();

        // Tentukan rentang tanggal
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->subDays(30);
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now();

        // Hitung selisih hari untuk menentukan grouping
        $diffInDays = $startDate->diffInDays($endDate);

        if ($diffInDays <= 31) {
            // Grouping per hari
            $salesTrendData = $salesTrendQuery
                ->select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('COUNT(*) as count'),
                    DB::raw('SUM(gross_amount) as revenue')
                )
                ->whereBetween('created_at', [$startDate, $endDate])
                ->groupBy('date')
                ->orderBy('date', 'asc')
                ->get();

            $labels = $salesTrendData->map(function ($item) {
                return Carbon::parse($item->date)->format('d M');
            });
        } else {
            // Grouping per minggu
            $salesTrendData = $salesTrendQuery
                ->select(
                    DB::raw('YEAR(created_at) as year'),
                    DB::raw('WEEK(created_at) as week'),
                    DB::raw('COUNT(*) as count'),
                    DB::raw('SUM(gross_amount) as revenue')
                )
                ->whereBetween('created_at', [$startDate, $endDate])
                ->groupBy('year', 'week')
                ->orderBy('year', 'asc')
                ->orderBy('week', 'asc')
                ->get();

            $labels = $salesTrendData->map(function ($item) {
                return 'Minggu ' . $item->week . ' ' . $item->year;
            });
        }

        $counts = $salesTrendData->pluck('count')->toArray();
        $revenues = $salesTrendData->pluck('revenue')->toArray();

        // Validasi jika data kosong
        if (empty($counts)) {
            $counts = [0];
            $revenues = [0];
            $labels = ['No Data'];
        }

        $salesTrend = [
            'labels' => $labels,
            'counts' => $counts,
            'revenues' => $revenues
        ];

        return view('owner.penjualan', compact(
            'orders',
            'totalPenjualan',
            'totalPendapatan',
            'pembeliAktif',
            'salesTrend'
        ));
    }

    public function perjanjianstore(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'goat_id' => 'required|integer',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'annual_offspring' => 'required|integer',
            'create_at' => 'required|date',
            'update_at' => 'required|date',
        ]);

        Perjanjian::create($request->all());
        // return redirect()->route('owner.perjanjian');
    }
    public function updateNotes(Request $request, $orderId)
    {
        try {
            $order = \App\Models\Order::findOrFail($orderId);

            $request->validate([
                'notes' => 'required|string|max:1000',
            ]);

            $order->owner_notes = $request->notes;
            $order->save();

            return response()->json([
                'success' => true,
                'message' => 'Catatan berhasil diperbarui',
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat memperbarui catatan',
                ],
                500,
            );
        }
    }

    public function updateStatus(Request $request, $orderId)
    {
        try {
            $order = \App\Models\Order::with(['user', 'orderable'])->findOrFail($orderId);

            $status = $request->input('status');
            $notes = $request->input('notes');

            if (!in_array($status, ['settlement', 'cancel'])) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Status tidak valid',
                    ],
                    400,
                );
            }

            $order->status = $status;
            if ($notes) {
                $order->owner_notes = $notes;
            }
            $order->save();

            // Update product status
            $item = $order->orderable;

            if ($item instanceof Kambing || $item instanceof Domba) {

                if ($status === 'settlement') {
                    $item->update([
                        'for_sale' => 'no',
                        'is_locked' => false
                    ]);
                }

                if ($status === 'cancel') {
                    $item->update([
                        'for_sale' => 'yes',
                        'is_locked' => false
                    ]);
                }

            }

            return response()->json([
                'success' => true,
                'message' => $status === 'settlement' ? 'Pembayaran berhasil diterima' : 'Pembayaran ditolak',
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat mengupdate status',
                ],
                500,
            );
        }
    }
}
