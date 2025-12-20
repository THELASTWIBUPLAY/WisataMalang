<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kriteria extends Model {
    protected $table = 'kriterias';
    protected $primaryKey = 'id';
    protected $fillable = [
    'nama_kriteria', 
    'bobot', 
    'jenis', 
    'bobot_normalisasi' // Tambahkan ini
];
}