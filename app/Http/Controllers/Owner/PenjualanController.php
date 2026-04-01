<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class PenjualanController extends Controller
{
    // Invoice Otomatis
    public function invoice($order_id)
    {
        $order = Order::with(['user', 'orderable'])
            ->where('order_id', $order_id)
            ->firstOrFail();

        return view('owner.invoice', compact('order'));
    }

    // Invoice Manual
    public function manualInvoice($order_id)
    {
        $order = Order::with(['user', 'orderable'])
            ->where('order_id', $order_id)
            ->firstOrFail();

        return view('owner.manual-invoice', compact('order'));
    }
}
