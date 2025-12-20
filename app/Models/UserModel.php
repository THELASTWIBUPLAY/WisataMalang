<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class UserModel extends Authenticatable
{
    protected $table = 'm_user';
    protected $primaryKey = 'user_id';
    protected $fillable = ['level_id', 'username', 'nama', 'password'];
    protected $hidden = ['password'];

    // Relasi ke tabel Level
    public function level() {
        return $this->belongsTo(LevelModel::class, 'level_id', 'level_id');
    }
}