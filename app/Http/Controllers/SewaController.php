<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Sewa;
use App\Models\SewaItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class SewaController extends Controller
{
    public function tambahKeranjang(Request $request)
    {
        $request->validate([
            'produk_id'      => 'required|exists:produks,id',
            'jumlah'         => 'required|integer|min:1',
            'tanggal_sewa'   => 'required|date|after_or_equal:today',
            'tanggal_kembali'=> 'required|date|after:tanggal_sewa',
        ]);

        $produk = Produk::findOrFail($request->produk_id);

        if (!$produk->cekStok($request->tanggal_sewa, $request->tanggal_kembali, $request->jumlah)) {
            return back()->withErrors(['stok' => 'Stok tidak mencukupi untuk tanggal yang dipilih.']);
        }

        $keranjang = session()->get('keranjang', []);
        $key = $request->produk_id . '_' . $request->tanggal_sewa . '_' . $request->tanggal_kembali;

        $keranjang[$key] = [
            'produk_id'      => $produk->id,
            'nama'           => $produk->nama,
            'foto'           => $produk->foto,
            'harga_per_hari' => $produk->harga_per_hari,
            'jumlah'         => $request->jumlah,
            'tanggal_sewa'   => $request->tanggal_sewa,
            'tanggal_kembali'=> $request->tanggal_kembali,
        ];

        session()->put('keranjang', $keranjang);

        return redirect()->route('sewa.keranjang')->with('success', 'Produk berhasil ditambahkan ke keranjang.');
    }

    public function hapusKeranjang(string $key)
    {
        $keranjang = session()->get('keranjang', []);
        unset($keranjang[$key]);
        session()->put('keranjang', $keranjang);

        return back()->with('success', 'Item dihapus dari keranjang.');
    }

    public function keranjang()
    {
        $keranjang = session()->get('keranjang', []);
        return view('sewa.keranjang', compact('keranjang'));
    }

    public function checkout()
    {
        $keranjang = session()->get('keranjang', []);
        if (empty($keranjang)) {
            return redirect()->route('katalog.index')->with('error', 'Keranjang kosong.');
        }
        return view('sewa.checkout', compact('keranjang'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'metode_bayar' => 'required|in:qris,transfer,cash',
            'bukti_bayar'  => 'nullable|image|max:2048',
        ]);

        $keranjang = session()->get('keranjang', []);
        if (empty($keranjang)) {
            return redirect()->route('katalog.index');
        }

        // Hitung total
        $totalHarga = 0;
        foreach ($keranjang as $item) {
            $tglSewa   = \Carbon\Carbon::parse($item['tanggal_sewa']);
            $tglKembali = \Carbon\Carbon::parse($item['tanggal_kembali']);
            $hari       = max(1, $tglSewa->diffInDays($tglKembali));
            $totalHarga += $item['harga_per_hari'] * $item['jumlah'] * $hari;
        }

        // Ambil tanggal dari item pertama
        $firstItem = array_values($keranjang)[0];

        // Status berdasarkan metode
        $status = $request->metode_bayar === 'cash'
            ? 'menunggu_bayar_tempat'
            : 'menunggu_konfirmasi';

        $buktiBayarPath = null;
        if ($request->hasFile('bukti_bayar')) {
            $buktiBayarPath = $request->file('bukti_bayar')->storeOnCloudinary('bukti_bayar')->getSecurePath();
        }

        DB::transaction(function () use ($request, $keranjang, $totalHarga, $firstItem, $status, $buktiBayarPath) {
            $sewa = Sewa::create([
                'user_id'        => auth()->id(),
                'nama_pelanggan' => auth()->user()->name,
                'no_hp_pelanggan'=> auth()->user()->no_hp,
                'tanggal_sewa'   => $firstItem['tanggal_sewa'],
                'tanggal_kembali'=> $firstItem['tanggal_kembali'],
                'total_harga'    => $totalHarga,
                'metode_bayar'   => $request->metode_bayar,
                'status'         => $status,
                'bukti_bayar'    => $buktiBayarPath,
            ]);

            foreach ($keranjang as $item) {
                SewaItem::create([
                    'sewa_id'       => $sewa->id,
                    'produk_id'     => $item['produk_id'],
                    'jumlah'        => $item['jumlah'],
                    'harga_per_hari'=> $item['harga_per_hari'],
                ]);
            }

            session()->forget('keranjang');
            session()->put('last_kode_booking', $sewa->kode_booking);
        });

        $kode = session()->get('last_kode_booking');
        return redirect()->route('sewa.bukti', $kode)->with('success', 'Pesanan berhasil dibuat!');
    }

    public function buktiSewa(string $kode)
    {
        $sewa = Sewa::with(['items.produk', 'user', 'denda'])
            ->where('kode_booking', $kode)
            ->firstOrFail();

        // Pastikan hanya pemilik yang bisa lihat
        if ($sewa->user_id && $sewa->user_id !== auth()->id()) {
            abort(403);
        }

        $qrCode = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
            ->size(200)
            ->generate($kode);

        return view('sewa.bukti', compact('sewa', 'qrCode'));
    }

    public function downloadPdf(string $kode)
    {
        $sewa = Sewa::with(['items.produk', 'user', 'denda'])
            ->where('kode_booking', $kode)
            ->firstOrFail();

        if ($sewa->user_id && $sewa->user_id !== auth()->id()) {
            abort(403);
        }

        $qrCode = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
            ->size(100)
            ->generate($kode);

        $pdf = Pdf::loadView('pdf.bukti-sewa', compact('sewa', 'qrCode'))->setPaper('a6', 'portrait');
        return $pdf->download('bukti-sewa-' . $kode . '.pdf');
    }
}
