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
        Schema::create('transaksis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wisata_id')->constrained();
            $table->string('nama_user');
            $table->integer('jumlah_tiket');
            $table->integer('total_bayar');
            $table->string('bukti_bayar')->nullable(); // File gambar
            $table->enum('status', ['pending', 'lunas'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksis');
    }
};
