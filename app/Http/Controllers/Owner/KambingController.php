<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Kambing;
use App\Models\KambingHistory;

class KambingController extends Controller
{
    public function index()
    {
        $kambing = Kambing::with('user')->paginate(10);

        return view('owner.listkambing', compact('kambing'));
    }

    public function show(Kambing $kambing)
    {
        return view('owner.showkambing', compact('kambing'));
    }

    public function monitoring($id)
    {
        $kambing = Kambing::findOrFail($id);

        $historis = KambingHistory::where('kambing_id', $id)
            ->orderBy('bulan')
            ->get();

        $labels = $historis->pluck('bulan');
        $beratData = $historis->pluck('berat');
        $hargaData = $historis->pluck('harga');

        return view('owner.monitoringkambing', compact(
            'kambing',
            'historis',
            'labels',
            'beratData',
            'hargaData'
        ));
    }
}
