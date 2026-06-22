<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Produk;
use Illuminate\Http\Request;

class KatalogController extends Controller
{
    public function index(Request $request)
    {
        $kategoris = Kategori::orderBy('nama')->get();
        $query     = Produk::with('kategori')->where('stok', '>', 0);

        if ($request->filled('kategori')) {
            $query->where('kategori_id', $request->kategori);
        }

        if ($request->filled('cari')) {
            $query->where('nama', 'like', '%' . $request->cari . '%');
        }

        $produks = $query->orderBy('nama')->paginate(12)->withQueryString();

        // Hitung stok tersedia untuk setiap produk berdasarkan hari ini
        $today    = now()->toDateString();
        $tomorrow = now()->addDay()->toDateString();
        foreach ($produks as $produk) {
            $produk->stok_tersedia_sekarang = $produk->stokTersedia($today, $tomorrow);
        }

        return view('katalog.index', compact('produks', 'kategoris'));
    }

    public function show(Produk $produk)
    {
        $produk->load('kategori');
        return view('katalog.show', compact('produk'));
    }

    public function cekStokAjax(Request $request)
    {
        $request->validate([
            'produk_id'      => 'required|exists:produks,id',
            'tanggal_sewa'   => 'required|date',
            'tanggal_kembali'=> 'required|date|after:tanggal_sewa',
        ]);

        $produk = Produk::findOrFail($request->produk_id);
        $tersedia = $produk->stokTersedia($request->tanggal_sewa, $request->tanggal_kembali);

        return response()->json(['stok_tersedia' => $tersedia]);
    }
}
