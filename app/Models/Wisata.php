<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wisata extends Model
{
    protected $table = 'wisatas';
    protected $fillable = [
        'nama_wisata',
        'deskripsi',
        'harga_dewasa_min',
        'harga_dewasa_max',
        'harga_anak_min',
        'harga_anak_max',
        'rating',
        'lat',
        'lng',
        'fasilitas'
    ];
    protected $primaryKey = 'id';

    public function daftar_gambar()
    {
        return $this->hasMany(Gambar::class, 'wisata_id', 'id');
    }

    public function daftar_fasilitas()
    {
        return $this->belongsToMany(Fasilitas::class, 'wisata_fasilitas', 'wisata_id', 'fasilitas_id');
    }

    // Tambahkan ini jika Anda ingin menggunakan nama 'kriterias' di Controller
    public function kriterias()
    {
        return $this->belongsToMany(Fasilitas::class, 'wisata_fasilitas', 'wisata_id', 'fasilitas_id');
    }

    public function nilai_kriteria()
    {
        // Mengambil kriteria beserta nilai 'nilai' di tabel pivot
        return $this->belongsToMany(Kriteria::class, 'wisata_kriteria')->withPivot('nilai');
    }
}
