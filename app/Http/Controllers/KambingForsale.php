<?php

namespace App\Http\Controllers;

use App\Models\Kambing;
use App\Models\Domba;
use App\Models\Product;
use Illuminate\Http\Request;

class KambingForsale extends Controller
{
    public function index(Request $request)
    {
        $kategori = $request->input('kategori_produk', 'semua');
        $jenis    = $request->input('jenis');
        $q        = $request->input('q');
        $diskon   = $request->boolean('diskon');
        $sort     = $request->input('sort');
        $hargaMin = $request->input('harga_min');
        $hargaMax = $request->input('harga_max');

        if ($kategori === 'semua') {
            return $this->handleSemuaKategori($request, $diskon, $q, $sort, $hargaMin, $hargaMax);
        } else {
            return $this->handleSingleKategori($request, $kategori, $jenis, $diskon, $q, $sort, $hargaMin, $hargaMax);
        }
    }

    private function handleSemuaKategori($request, $diskon, $q, $sort, $hargaMin, $hargaMax)
    {
        $kambingQuery = Kambing::query()->where('for_sale', 'yes');
        $dombaQuery = Domba::query()->where('for_sale', 'yes');
        
        // Query untuk produk (hanya yang dialokasikan jual)
        $productQuery = Product::query()->whereHas('allocations', function ($query) {
            $query->where('type', 'jual')->where('qty', '>', 0);
        });

        // Filter berdasarkan diskon
        if ($diskon) {
            $kambingQuery->where('diskon', '>', 0);
            $dombaQuery->where('diskon', '>', 0);
            $productQuery->where('diskon', '>', 0); // Asumsi tabel product punya kolom diskon
        }

        // Filter berdasarkan pencarian
        if ($q) {
            $kambingQuery->where(function ($subQuery) use ($q) {
                $subQuery->where('name', 'like', "%{$q}%")
                    ->orWhere('jenis_kelamin', 'like', "%{$q}%")
                    ->orWhere('weight_now', 'like', "%{$q}%")
                    ->orWhere('type_goat', 'like', "%{$q}%");
            });
            $dombaQuery->where(function ($subQuery) use ($q) {
                $subQuery->where('name', 'like', "%{$q}%")
                    ->orWhere('jenis_kelamin', 'like', "%{$q}%")
                    ->orWhere('weight_now', 'like', "%{$q}%")
                    ->orWhere('type_domba', 'like', "%{$q}%");
            });
            $productQuery->where(function ($subQuery) use ($q) {
                $subQuery->where('nama', 'like', "%{$q}%")
                    ->orWhere('type', 'like', "%{$q}%")
                    ->orWhere('kode', 'like', "%{$q}%");
            });
        }

        // Sorting
        $orderBy = 'created_at';
        $orderDir = 'desc';
        if ($sort === 'oldest') {
            $orderDir = 'asc';
        } elseif ($sort === 'price_low') {
            $orderBy = 'harga';
            $orderDir = 'asc';
        } elseif ($sort === 'price_high') {
            $orderBy = 'harga';
            $orderDir = 'desc';
        }
        $kambingQuery->orderBy($orderBy, $orderDir);
        $dombaQuery->orderBy($orderBy, $orderDir);
        $productQuery->orderBy($orderBy, $orderDir);

        // Filter berdasarkan harga
        if ($hargaMin !== null && $hargaMin !== '') {
            $kambingQuery->where('harga', '>=', (int)$hargaMin);
            $dombaQuery->where('harga', '>=', (int)$hargaMin);
            $productQuery->where('harga', '>=', (int)$hargaMin);
        }
        if ($hargaMax !== null && $hargaMax !== '') {
            $kambingQuery->where('harga', '<=', (int)$hargaMax);
            $dombaQuery->where('harga', '<=', (int)$hargaMax);
            $productQuery->where('harga', '<=', (int)$hargaMax);
        }

        // Ambil data kambing, domba, dan produk
        $kambings = $kambingQuery->get();
        $dombas = $dombaQuery->get();
        $products = $productQuery->get();

        // Gabungkan ketiganya
        $allProduk = $kambings->concat($dombas)->concat($products);

        // Jika disort berdasarkan harga setelah digabung (penting agar hasil gabungan rapi)
        if ($sort === 'price_low') {
            $allProduk = $allProduk->sortBy('harga')->values();
        } elseif ($sort === 'price_high') {
            $allProduk = $allProduk->sortByDesc('harga')->values();
        } elseif ($sort === 'oldest') {
            $allProduk = $allProduk->sortBy('created_at')->values();
        } else {
            // default latest
            $allProduk = $allProduk->sortByDesc('created_at')->values();
        }

        // Pagination
        $perPage = 10;
        $page = $request->input('page', 1);
        $pagedProduk = $allProduk->slice(($page - 1) * $perPage, $perPage)->values();
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $pagedProduk,
            $allProduk->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('forsale', [
            'kambings'    => $paginator, // Memakai paginator gabungan ke variabel utama
            'dombas'      => collect(),
            'products'    => collect(),  // Harus dikirim kosong agar blade tidak error 'undefined variable'
            'totalProduk' => $allProduk->count(),
        ]);
    }


    private function handleSingleKategori($request, $kategori, $jenis, $diskon, $q, $sort, $hargaMin, $hargaMax)
    {
        $modelMap = [
            'kambing' => Kambing::class,
            'domba'   => Domba::class,
            'produk'  => Product::class, // Tambahkan map untuk produk
        ];

        if (!array_key_exists($kategori, $modelMap)) {
            abort(404, 'Kategori tidak ditemukan');
        }

        $model = $modelMap[$kategori];
        $query = $model::query();

        // Filter For Sale (Ternak pakai 'yes', Produk pakai 'allocations')
        if ($kategori === 'produk') {
            $query->whereHas('allocations', function ($q) {
                $q->where('type', 'jual')->where('qty', '>', 0);
            });
        } else {
            $query->where('for_sale', 'yes');
        }

        // Filter berdasarkan diskon
        if ($diskon) {
            $query->where('diskon', '>', 0);
        }

        // Filter berdasarkan jenis
        if ($jenis) {
            if ($kategori === 'kambing') {
                $query->where('type_goat', $jenis);
            } elseif ($kategori === 'domba') {
                $query->where('type_domba', $jenis);
            } elseif ($kategori === 'produk') {
                $query->where('type', $jenis);
            }
        }

        // Filter berdasarkan pencarian
        if ($q) {
            $query->where(function ($subQuery) use ($q, $kategori) {
                if ($kategori === 'produk') {
                    $subQuery->where('nama', 'like', "%{$q}%")
                             ->orWhere('kode', 'like', "%{$q}%")
                             ->orWhere('type', 'like', "%{$q}%");
                } else {
                    $subQuery->where('name', 'like', "%{$q}%")
                             ->orWhere('jenis_kelamin', 'like', "%{$q}%")
                             ->orWhere('weight_now', 'like', "%{$q}%");

                    if ($kategori === 'kambing') {
                        $subQuery->orWhere('type_goat', 'like', "%{$q}%");
                    } elseif ($kategori === 'domba') {
                        $subQuery->orWhere('type_domba', 'like', "%{$q}%");
                    }
                }
            });
        }

        // Sorting
        switch ($sort) {
            case 'latest':
                $query->orderByDesc('created_at');
                break;
            case 'oldest':
                $query->orderBy('created_at');
                break;
            case 'price_low':
                $query->orderBy('harga', 'asc');
                break;
            case 'price_high':
                $query->orderBy('harga', 'desc');
                break;
            default:
                $query->orderByDesc('created_at');
        }

        // Filter berdasarkan harga
        if ($hargaMin !== null && $hargaMin !== '') {
            $query->where('harga', '>=', (int)$hargaMin);
        }
        if ($hargaMax !== null && $hargaMax !== '') {
            $query->where('harga', '<=', (int)$hargaMax);
        }

        // Ambil produk dengan pagination
        $hasil = $query->paginate(10)->withQueryString();

        return view('forsale', [
            'kambings'    => $kategori === 'kambing' ? $hasil : collect(),
            'dombas'      => $kategori === 'domba' ? $hasil : collect(),
            'products'    => $kategori === 'produk' ? $hasil : collect(), // Kirim variabel products
            'totalProduk' => $hasil->total(),
        ]);
    }
}