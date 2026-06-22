<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KatalogController;
use App\Http\Controllers\SewaController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProdukController;
use App\Http\Controllers\Admin\KategoriController;
use App\Http\Controllers\Admin\TransaksiController;
use App\Http\Controllers\Admin\SewaManualController;

// Landing page
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Katalog (publik)
Route::get('/katalog', [KatalogController::class, 'index'])->name('katalog.index');
Route::get('/katalog/cek-stok', [KatalogController::class, 'cekStokAjax'])->name('katalog.cekStok');
Route::get('/katalog/{produk}', [KatalogController::class, 'show'])->name('katalog.show');

// Auth routes (Breeze)
require __DIR__.'/auth.php';

// ── Pelanggan ────────────────────────────────────────────
Route::middleware(['auth', 'role:pelanggan'])->prefix('pelanggan')->name('pelanggan.')->group(function () {
    Route::get('/riwayat', [PelangganController::class, 'riwayat'])->name('riwayat');
});

// Sewa flow (harus login, boleh pelanggan)
Route::middleware(['auth', 'role:pelanggan'])->prefix('sewa')->name('sewa.')->group(function () {
    Route::get('/keranjang', [SewaController::class, 'keranjang'])->name('keranjang');
    Route::post('/keranjang/tambah', [SewaController::class, 'tambahKeranjang'])->name('keranjang.tambah');
    Route::delete('/keranjang/{key}', [SewaController::class, 'hapusKeranjang'])->name('keranjang.hapus');
    Route::get('/checkout', [SewaController::class, 'checkout'])->name('checkout');
    Route::post('/checkout', [SewaController::class, 'store'])->name('store');
    Route::get('/bukti/{kode}', [SewaController::class, 'buktiSewa'])->name('bukti');
    Route::get('/bukti/{kode}/pdf', [SewaController::class, 'downloadPdf'])->name('bukti.pdf');
});

// ── Admin ─────────────────────────────────────────────────
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Produk
    Route::resource('produk', ProdukController::class);

    // Kategori
    Route::resource('kategori', KategoriController::class);

    // Transaksi
    Route::get('/transaksi', [TransaksiController::class, 'index'])->name('transaksi.index');
    Route::patch('/transaksi/{sewa}/konfirmasi', [TransaksiController::class, 'konfirmasi'])->name('transaksi.konfirmasi');
    Route::get('/transaksi/pengambilan', [TransaksiController::class, 'pengambilan'])->name('transaksi.pengambilan');
    Route::post('/transaksi/pengambilan/cari', [TransaksiController::class, 'cariAmbil'])->name('transaksi.cariAmbil');
    Route::post('/transaksi/proses-ambil', [TransaksiController::class, 'prosesAmbil'])->name('transaksi.prosesAmbil');
    Route::get('/transaksi/pengembalian', [TransaksiController::class, 'pengembalian'])->name('transaksi.pengembalian');
    Route::post('/transaksi/pengembalian/cari', [TransaksiController::class, 'cariKembali'])->name('transaksi.cariKembali');
    Route::post('/transaksi/proses-kembali', [TransaksiController::class, 'prosesKembali'])->name('transaksi.prosesKembali');
    Route::get('/transaksi/{sewa}/struk-ambil', [TransaksiController::class, 'cetakStrukAmbil'])->name('transaksi.struk.ambil');
    Route::get('/transaksi/{sewa}/struk-kembali', [TransaksiController::class, 'cetakStrukKembali'])->name('transaksi.struk.kembali');

    // Sewa Manual
    Route::get('/sewa-manual', [SewaManualController::class, 'create'])->name('sewaManual.create');
    Route::post('/sewa-manual', [SewaManualController::class, 'store'])->name('sewaManual.store');
});
