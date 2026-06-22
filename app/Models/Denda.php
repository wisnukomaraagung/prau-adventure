<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Denda extends Model
{
    use HasFactory;

    protected $fillable = [
        'sewa_id',
        'hari_telat',
        'total_denda',
    ];

    protected $casts = [
        'total_denda' => 'decimal:2',
    ];

    public function sewa()
    {
        return $this->belongsTo(Sewa::class);
    }
}
