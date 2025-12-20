<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Gambar extends Model
{
    protected $table = 'gambars';
    protected $primaryKey = 'id'; // Menggunakan ID sesuai keinginan Anda
    
    protected $fillable = [
        'wisata_id', // Foreign key yang menyambung ke Wisata->id
        'nama_file'
    ];

    /**
     * Relasi ke Wisata: Banyak Gambar dimiliki oleh satu Wisata
     */
    public function wisata(): BelongsTo
    {
        return $this->belongsTo(Wisata::class, 'wisata_id', 'id');
    }
}