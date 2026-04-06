<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;

class AdminManagementController extends Controller
{
    // Hanya menampilkan list admin (Read-Only)
    public function index()
    {
        // Ambil semua admin, kecuali super_admin
        $admins = Admin::where('role', 'admin')->get();
        
        return view('owner.management.index', compact('admins'));
    }
}