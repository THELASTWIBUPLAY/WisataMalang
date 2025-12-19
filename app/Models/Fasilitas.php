<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fasilitas extends Model
{
    protected $table = 'fasilitas';
    protected $fillable = ['nama_fasilitas'];

    public function wisatas() {
        return $this->belongsToMany(Wisata::class, 'wisata_fasilitas', 'fasilitas_id', 'wisata_id');
    }
}
