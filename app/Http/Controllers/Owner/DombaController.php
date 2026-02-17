<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Domba;
use App\Models\DombaHistory;

class DombaController extends Controller
{
    public function index()
    {
        $dombas = Domba::with('user')->paginate(10);

        return view('owner.listdomba', compact('dombas'));
    }

    public function show(Domba $domba)
    {
        return view('owner.showdomba', compact('domba'));
    }

    public function monitoring($id)
    {
        $domba = Domba::findOrFail($id);

        $historis = DombaHistory::where('domba_id', $id)
            ->orderBy('bulan')
            ->get();

        $labels = $historis->pluck('bulan');
        $beratData = $historis->pluck('berat');
        $hargaData = $historis->pluck('harga');

        return view('owner.monitoringdomba', compact(
            'domba',
            'historis',
            'labels',
            'beratData',
            'hargaData'
        ));
    }
}
