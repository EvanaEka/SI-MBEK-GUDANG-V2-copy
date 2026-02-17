<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Kambing;
use App\Models\Domba;
use App\Models\Order; // Pastikan import Order
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    /**
     * Display owner dashboard (Overview)
     */
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

        // === OVERVIEW TOP USERS (Pemilik Ternak Terbanyak) ===
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

        // === STATISTIK KEPEMILIKAN ===
        $usersWithDomba = User::has('domba')->get();
        $usersWithOwnership = User::whereHas('kambings')
            ->orWhereHas('domba')
            ->distinct()
            ->count();

        $ownerPercentage = $userCount > 0
            ? ($usersWithOwnership / $userCount) * 100
            : 0;

        // === RATA-RATA BERAT & HARGA (FIXED: Pake for_sale) ===
        $kambingAvgWeight = Kambing::avg('weight_now');
        $dombaAvgWeight = Domba::avg('weight_now');
        
        // Perbaikan Logic: Menggunakan for_sale = 'yes'
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

        // === DATA PRODUK FOR_SALE TERLAMA (FIXED: Pake for_sale) ===
        $kambingForSale = Kambing::where('for_sale', 'yes')
            ->select('id', 'name', 'created_at', 'updated_at', DB::raw("'kambing' as product_type"))
            ->get();

        $dombaForSale = Domba::where('for_sale', 'yes')
            ->select('id', 'name', 'created_at', 'updated_at', DB::raw("'domba' as product_type"))
            ->get();

        // Gabungkan dan urutkan
        $allForSale = $kambingForSale->concat($dombaForSale)
            ->map(function ($item) {
                // Asumsi jika tidak ada kolom spesifik tanggal mulai jual, gunakan created_at
                // Jika kamu punya kolom 'for_sale_at', ganti created_at dengan itu
                $startDate = $item->created_at; 
                $item->days_on_sale = $startDate->diffInDays(now());
                $item->sale_date = $startDate;
                return $item;
            })
            ->sortBy('sale_date') // Urutkan dari yang terlama
            ->take(10)
            ->values();

        $forSaleChartData = [
            'labels' => $allForSale->pluck('name')->toArray(),
            'dates' => $allForSale->map(function ($item) {
                return $item->sale_date->format('Y-m-d');
            })->toArray(),
            'days_on_sale' => $allForSale->pluck('days_on_sale')->toArray(),
            'product_types' => $allForSale->pluck('product_type')->toArray()
        ];

        return view('owner.dashboard', compact(
            'kambingCount', 'kambingThisMonth', 'kambingLastMonth', 'kambingPercentageChange',
            'dombaCount', 'dombaThisMonth', 'dombaLastMonth', 'dombaPercentageChange',
            'userCount', 'userThisMonth', 'userLastMonth', 'userPercentageChange',
            'users', 'usersa', 'usersWithDomba', 'ownerPercentage',
            'kambingAvgWeight', 'dombaAvgWeight', 'kambingAvgPrice', 'dombaAvgPrice',
            'weeklyLabels', 'weeklyKambingCounts', 'weeklyDombaCounts', 'forSaleChartData'
        ));
    }

    /**
     * Halaman Perjanjian (View Only untuk Owner)
     */
    public function perjanjian()
    {
        $users = User::withCount('kambings')->orderBy('kambings_count', 'desc')->take(7)->get();
        return view('owner.perjanjian', compact('users')); // Arahkan ke view owner
    }

    /**
     * Halaman Penjualan (Analytics & Monitoring)
     */
    public function penjualan(Request $request)
    {
        // Query dasar
        $ordersQuery = Order::with('user', 'kambing', 'domba')->latest();

        // === FILTER ===
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
                $q->where('order_id', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', function ($q2) use ($request) {
                      $q2->where('name', 'like', '%' . $request->search . '%')
                         ->orWhere('email', 'like', '%' . $request->search . '%');
                  });
            });
        }

        // === PAGINATION ===
        $orders = $ordersQuery->paginate(10);

        // === STATISTIK ===
        $filteredQuery = clone $ordersQuery;
        // Reset limit/offset dari pagination untuk hitung total
        // Note: clone paginate query kadang membawa limit, jadi hati-hati.
        // Cara aman hitung total tanpa pagination:
        
        $totalPenjualan = $orders->total(); 
        
        // Total Pendapatan (Hanya yang sukses)
        $revenueQuery = clone $ordersQuery;
        // Hapus pagination limit dari query builder jika ada (biasanya di instance paginate udah bersih)
        $totalPendapatan = Order::whereIn('status', ['settlement', 'capture', 'success'])
            ->when($request->start_date, fn($q) => $q->whereDate('created_at', '>=', $request->start_date))
            ->when($request->end_date, fn($q) => $q->whereDate('created_at', '<=', $request->end_date))
            ->sum('gross_amount');

        $pembeliAktif = Order::whereIn('status', ['settlement', 'capture', 'success'])
            ->distinct('user_id')
            ->count('user_id');

        // === DATA TREND PENJUALAN ===
        $salesTrendQuery = Order::query(); // Buat query baru bersih
        if ($request->status && $request->status !== 'all') $salesTrendQuery->where('status', $request->status);

        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->subDays(30);
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now();
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

        if (empty($counts)) {
            $counts = [0]; $revenues = [0]; $labels = ['No Data'];
        }

        $salesTrend = [
            'labels' => $labels,
            'counts' => $counts,
            'revenues' => $revenues
        ];

        return view('owner.penjualan', compact( // Arahkan ke view owner
            'orders', 'totalPenjualan', 'totalPendapatan', 'pembeliAktif', 'salesTrend'
        ));
    }

    // ==========================================
    // REPORT METHODS (Sesuai Route Owner)
    // ==========================================

    public function kambingReport()
    {
        $kambings = Kambing::with(['user', 'histories'])
            ->latest()
            ->get();

        $stats = [
            'total' => $kambings->count(),
            // Logic for_sale = yes
            'dijual' => $kambings->where('for_sale', 'yes')->count(),
            // Logic terjual (misal for_sale=no dan ada user pemiliknya selain admin/peternak)
            // Atau logic sederhana: Total - Dijual
            'terjual' => $kambings->where('for_sale', 'no')->count(), 
        ];

        return view('owner.reports.kambing', compact('kambings', 'stats'));
    }

    public function dombaReport()
    {
        $dombas = Domba::with(['user', 'histories'])
            ->latest()
            ->get();

        $stats = [
            'total' => $dombas->count(),
            'dijual' => $dombas->where('for_sale', 'yes')->count(),
            'terjual' => $dombas->where('for_sale', 'no')->count(),
        ];

        return view('owner.reports.domba', compact('dombas', 'stats'));
    }

    public function penjualanReport()
    {
        $orders = Order::with(['user', 'kambing', 'domba'])
            ->whereIn('status', ['settlement', 'success', 'capture']) // Gunakan kolom status yang benar
            ->latest()
            ->get();

        $stats = [
            'total_orders' => $orders->count(),
            'total_revenue' => $orders->sum('gross_amount'),
            'average_order' => $orders->avg('gross_amount'),
            'kambing_sold' => $orders->whereNotNull('kambing_id')->count(),
            'domba_sold' => $orders->whereNotNull('domba_id')->count(),
        ];

        return view('owner.reports.penjualan', compact('orders', 'stats'));
    }
}