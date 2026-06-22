<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Denda;
use App\Models\Sewa;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        $query = Sewa::with(['user', 'items.produk'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('cari')) {
            $query->where('kode_booking', 'like', '%' . $request->cari . '%')
                  ->orWhere('nama_pelanggan', 'like', '%' . $request->cari . '%');
        }

        $sewas = $query->paginate(20)->withQueryString();

        return view('admin.transaksi.index', compact('sewas'));
    }

    public function konfirmasi(Sewa $sewa)
    {
        $sewa->update(['status' => 'lunas']);
        return back()->with('success', 'Pembayaran dikonfirmasi. Status diubah ke Lunas.');
    }

    public function pengambilan()
    {
        return view('admin.transaksi.pengambilan');
    }

    public function cariAmbil(Request $request)
    {
        $request->validate(['kode_booking' => 'required|string']);

        $sewa = Sewa::with(['items.produk', 'user'])
            ->where('kode_booking', strtoupper($request->kode_booking))
            ->first();

        if (!$sewa) {
            return back()->withInput()->withErrors(['kode_booking' => 'Kode booking tidak ditemukan.']);
        }

        return view('admin.transaksi.pengambilan', compact('sewa'));
    }

    public function pengembalian()
    {
        return view('admin.transaksi.pengembalian');
    }

    public function cariKembali(Request $request)
    {
        $request->validate(['kode_booking' => 'required|string']);

        $sewa = Sewa::with(['items.produk', 'user'])
            ->where('kode_booking', strtoupper($request->kode_booking))
            ->first();

        if (!$sewa) {
            return back()->withInput()->withErrors(['kode_booking' => 'Kode booking tidak ditemukan.']);
        }

        // Hitung preview denda
        $today = now()->startOfDay();
        $tglKembali = $sewa->tanggal_kembali->startOfDay();
        $hariTelat = $today->gt($tglKembali) ? $today->diffInDays($tglKembali) : 0;
        $previewDenda = $hariTelat > 0
            ? $sewa->items->sum(fn($item) => $item->harga_per_hari * $item->jumlah * $hariTelat)
            : 0;

        return view('admin.transaksi.pengembalian', compact('sewa', 'hariTelat', 'previewDenda'));
    }

    public function prosesAmbil(Request $request)
    {
        $request->validate(['kode_booking' => 'required|string']);

        $sewa = Sewa::with(['items.produk', 'user'])
            ->where('kode_booking', $request->kode_booking)
            ->firstOrFail();

        if ($sewa->status !== 'lunas') {
            return back()->withErrors(['kode_booking' => 'Status sewa tidak valid untuk proses pengambilan. Status saat ini: ' . $sewa->status]);
        }

        $sewa->update(['status' => 'sudah_diambil']);

        return redirect()->route('admin.transaksi.struk.ambil', $sewa)->with('success', 'Status diubah ke Sudah Diambil.');
    }

    public function prosesKembali(Request $request)
    {
        $request->validate(['kode_booking' => 'required|string']);

        $sewa = Sewa::with(['items.produk', 'user'])
            ->where('kode_booking', $request->kode_booking)
            ->firstOrFail();

        if ($sewa->status !== 'sudah_diambil') {
            return back()->withErrors(['kode_booking' => 'Status sewa tidak valid untuk proses pengembalian. Status saat ini: ' . $sewa->status]);
        }

        $today = now()->startOfDay();
        $tglKembali = $sewa->tanggal_kembali->startOfDay();
        $hariTelat = max(0, $tglKembali->diffInDays($today, false) * -1);
        // Hitung hari telat: jika today > tanggal_kembali
        $hariTelat = $today->gt($tglKembali) ? $today->diffInDays($tglKembali) : 0;

        $totalDenda = 0;
        if ($hariTelat > 0) {
            $totalDenda = $sewa->items->sum(function ($item) use ($hariTelat) {
                return $item->harga_per_hari * $item->jumlah * $hariTelat;
            });

            Denda::updateOrCreate(
                ['sewa_id' => $sewa->id],
                ['hari_telat' => $hariTelat, 'total_denda' => $totalDenda]
            );
        }

        $sewa->update(['status' => 'dikembalikan']);
        $sewa->refresh();

        return redirect()->route('admin.transaksi.struk.kembali', $sewa)->with('success', 'Barang dikembalikan. Denda: Rp ' . number_format($totalDenda));
    }

    public function cetakStrukAmbil(Sewa $sewa)
    {
        $sewa->load(['items.produk', 'user']);
        $pdf = Pdf::loadView('pdf.struk-ambil', compact('sewa'))->setPaper('a6', 'portrait');
        return $pdf->stream('struk-ambil-' . $sewa->kode_booking . '.pdf');
    }

    public function cetakStrukKembali(Sewa $sewa)
    {
        $sewa->load(['items.produk', 'user', 'denda']);
        $pdf = Pdf::loadView('pdf.struk-kembali', compact('sewa'))->setPaper('a6', 'portrait');
        return $pdf->stream('struk-kembali-' . $sewa->kode_booking . '.pdf');
    }
}
