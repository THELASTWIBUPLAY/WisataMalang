<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('wisatas', function (Blueprint $table) {
        $table->id();
        $table->string('nama_wisata');
        $table->double('harga');        // Kriteria: Cost (Harga Tiket)
        $table->double('rating')->default(0); // Kriteria: Benefit (Rata-rata rating user)
        $table->double('lat');          // Untuk hitung jarak (Latitude)
        $table->double('lng');          // Untuk hitung jarak (Longitude)
        $table->integer('fasilitas');   // Kriteria: Benefit (Contoh: jumlah fasilitas 1-5)
        $table->string('gambar')->nullable();
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wisatas');
    }
};
