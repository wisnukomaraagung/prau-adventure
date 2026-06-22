<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SewaItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sewa_id',
        'produk_id',
        'jumlah',
        'harga_per_hari',
    ];

    protected $casts = [
        'harga_per_hari' => 'decimal:2',
    ];

    public function sewa()
    {
        return $this->belongsTo(Sewa::class);
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }

    public function subtotal(): float
    {
        return $this->harga_per_hari * $this->jumlah * $this->sewa->jumlahHari();
    }
}
