<?php

namespace App\Http\Controllers;

use App\Models\Sewa;
use Illuminate\Http\Request;

class PelangganController extends Controller
{
    public function riwayat()
    {
        $sewas = Sewa::with(['items.produk', 'denda'])
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('pelanggan.riwayat', compact('sewas'));
    }
}
