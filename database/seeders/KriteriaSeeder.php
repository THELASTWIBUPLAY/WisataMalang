<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KriteriaSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'nama_kriteria' => 'harga',
                'bobot' => 30, // 30%
                'jenis' => 'cost', // Semakin murah semakin bagus
            ],
            [
                'nama_kriteria' => 'jarak',
                'bobot' => 20, // 25%
                'jenis' => 'cost', // Semakin dekat semakin bagus
            ],
            [
                'nama_kriteria' => 'fasilitas',
                'bobot' => 30, // 20%
                'jenis' => 'benefit', // Semakin lengkap semakin bagus
            ],
            [
                'nama_kriteria' => 'rating',
                'bobot' => 20, // 20%
                'jenis' => 'benefit', // Semakin lengkap semakin bagus
            ],
        ];

        DB::table('kriterias')->insert($data);
    }
}