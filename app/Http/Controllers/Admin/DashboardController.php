<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Models\Sewa;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_produk'       => Produk::count(),
            'total_pelanggan'    => User::where('role', 'pelanggan')->count(),
            'transaksi_hari_ini' => Sewa::whereDate('created_at', today())->count(),
            'menunggu_konfirmasi'=> Sewa::where('status', 'menunggu_konfirmasi')->count(),
            'total_pendapatan'   => Sewa::whereIn('status', ['lunas', 'sudah_diambil', 'dikembalikan'])->sum('total_harga'),
        ];

        $sewasTerbaru = Sewa::with(['user', 'items'])->latest()->limit(5)->get();

        return view('admin.dashboard', compact('stats', 'sewasTerbaru'));
    }
}
