<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sewas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('kode_booking')->unique();
            $table->string('nama_pelanggan')->nullable();
            $table->string('no_hp_pelanggan')->nullable();
            $table->date('tanggal_sewa');
            $table->date('tanggal_kembali');
            $table->decimal('total_harga', 12, 2)->default(0);
            $table->enum('metode_bayar', ['qris', 'transfer', 'cash'])->nullable();
            $table->enum('status', [
                'menunggu_konfirmasi',
                'menunggu_bayar_tempat',
                'lunas',
                'sudah_diambil',
                'dikembalikan'
            ])->default('menunggu_konfirmasi');
            $table->string('bukti_bayar')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sewas');
    }
};
