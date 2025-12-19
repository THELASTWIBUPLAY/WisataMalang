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
        Schema::create('wisata_fasilitas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wisata_id')->constrained('wisatas')->onDelete('cascade');
            $table->foreignId('fasilitas_id')->constrained('fasilitas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wisata_fasilitas');
    }
};
