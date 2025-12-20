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
        Schema::table('wisatas', function (Blueprint $table) {
            // Mengubah deskripsi menjadi nullable
            $table->text('deskripsi')->nullable();

            // Menambahkan kolom harga baru
            $table->integer('harga_dewasa_min')->default(0)->after('nama_wisata');
            $table->integer('harga_dewasa_max')->nullable()->after('harga_dewasa_min');
            $table->integer('harga_anak_min')->default(0)->after('harga_dewasa_max');
            $table->integer('harga_anak_max')->nullable()->after('harga_anak_min');

            // Menghapus kolom harga lama (Hapus baris ini jika kolomnya belum ada)
            $table->dropColumn('harga');
        });
    }

    public function down(): void
    {
        Schema::table('wisatas', function (Blueprint $table) {
            // Mengembalikan kolom harga lama jika rollback
            $table->integer('harga')->default(0);

            // Menghapus kolom-kolom baru
            $table->dropColumn(['harga_dewasa_min', 'harga_dewasa_max', 'harga_anak_min', 'harga_anak_max']);
        });
    }
};
