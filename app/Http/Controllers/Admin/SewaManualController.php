<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Models\Sewa;
use App\Models\SewaItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SewaManualController extends Controller
{
    public function create()
    {
        $produks = Produk::with('kategori')->orderBy('nama')->get();
        return view('admin.sewa-manual.create', compact('produks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_pelanggan'  => 'required|string|max:255',
            'no_hp_pelanggan' => 'required|string|max:20',
            'tanggal_sewa'    => 'required|date',
            'tanggal_kembali' => 'required|date|after:tanggal_sewa',
            'produk_id'       => 'required|array|min:1',
            'produk_id.*'     => 'exists:produks,id',
            'jumlah'          => 'required|array',
            'jumlah.*'        => 'integer|min:1',
        ]);

        DB::transaction(function () use ($request) {
            $totalHarga = 0;
            $items = [];

            foreach ($request->produk_id as $idx => $produkId) {
                $produk = Produk::findOrFail($produkId);
                $jumlah = $request->jumlah[$idx] ?? 1;
                $tglSewa    = \Carbon\Carbon::parse($request->tanggal_sewa);
                $tglKembali = \Carbon\Carbon::parse($request->tanggal_kembali);
                $hari       = max(1, $tglSewa->diffInDays($tglKembali));
                $subtotal   = $produk->harga_per_hari * $jumlah * $hari;
                $totalHarga += $subtotal;

                $items[] = [
                    'produk_id'     => $produkId,
                    'jumlah'        => $jumlah,
                    'harga_per_hari'=> $produk->harga_per_hari,
                ];
            }

            $sewa = Sewa::create([
                'user_id'         => null,
                'nama_pelanggan'  => $request->nama_pelanggan,
                'no_hp_pelanggan' => $request->no_hp_pelanggan,
                'tanggal_sewa'    => $request->tanggal_sewa,
                'tanggal_kembali' => $request->tanggal_kembali,
                'total_harga'     => $totalHarga,
                'metode_bayar'    => 'cash',
                'status'          => 'lunas',
            ]);

            foreach ($items as $item) {
                $item['sewa_id'] = $sewa->id;
                SewaItem::create($item);
            }

            session()->put('last_manual_sewa_id', $sewa->id);
        });

        $sewaId = session()->pull('last_manual_sewa_id');
        $sewa = Sewa::find($sewaId);

        return redirect()->route('admin.transaksi.struk.ambil', $sewa)->with('success', 'Sewa manual berhasil dibuat.');
    }
}
