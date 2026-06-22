<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Produk extends Model
{
    use HasFactory;

    protected $fillable = [
        'kategori_id',
        'nama',
        'deskripsi',
        'harga_per_hari',
        'stok',
        'foto',
    ];

    protected $casts = [
        'harga_per_hari' => 'decimal:2',
    ];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }

    public function sewaItems()
    {
        return $this->hasMany(SewaItem::class);
    }

    /**
     * Accessor: stok tersedia untuk hari ini (dipakai di listing katalog).
     * Tidak mempertimbangkan tanggal spesifik user — hanya gambaran umum.
     */
    public function getStokTersediaHariIniAttribute(): int
    {
        $today    = now()->toDateString();
        $tomorrow = now()->addDay()->toDateString();
        return $this->stokTersedia($today, $tomorrow);
    }

    /**
     * Cek ketersediaan stok untuk tanggal tertentu.
     * Hanya menghitung sewa yang:
     * - statusnya bukan 'dikembalikan'
     * - tanggalnya overlap dengan rentang yang diminta
     */
    public function cekStok(string $tanggalSewa, string $tanggalKembali, int $jumlah = 1): bool
    {
        $terpakai = $this->sewaItems()
            ->whereHas('sewa', function ($q) use ($tanggalSewa, $tanggalKembali) {
                $q->whereNotIn('status', ['dikembalikan'])
                  ->where('tanggal_sewa', '<=', $tanggalKembali)
                  ->where('tanggal_kembali', '>=', $tanggalSewa);
            })
            ->sum('jumlah');

        return ($this->stok - $terpakai) >= $jumlah;
    }

    public function stokTersedia(string $tanggalSewa, string $tanggalKembali): int
    {
        $terpakai = $this->sewaItems()
            ->whereHas('sewa', function ($q) use ($tanggalSewa, $tanggalKembali) {
                $q->whereNotIn('status', ['dikembalikan'])
                  ->where('tanggal_sewa', '<=', $tanggalKembali)
                  ->where('tanggal_kembali', '>=', $tanggalSewa);
            })
            ->sum('jumlah');

        return max(0, $this->stok - $terpakai);
    }
}
