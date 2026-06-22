<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sewa extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'kode_booking',
        'nama_pelanggan',
        'no_hp_pelanggan',
        'tanggal_sewa',
        'tanggal_kembali',
        'total_harga',
        'metode_bayar',
        'status',
        'bukti_bayar',
        'catatan',
    ];

    protected $casts = [
        'tanggal_sewa'   => 'date',
        'tanggal_kembali' => 'date',
        'total_harga'    => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($sewa) {
            if (empty($sewa->kode_booking)) {
                $sewa->kode_booking = self::generateKodeBooking();
            }
        });
    }

    public static function generateKodeBooking(): string
    {
        do {
            $tanggal = now()->format('dmY');
            $random  = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
            $kode    = 'PRU' . $tanggal . $random;
        } while (self::where('kode_booking', $kode)->exists());

        return $kode;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(SewaItem::class);
    }

    public function denda()
    {
        return $this->hasOne(Denda::class);
    }

    public function jumlahHari(): int
    {
        return max(1, $this->tanggal_sewa->diffInDays($this->tanggal_kembali));
    }
}
