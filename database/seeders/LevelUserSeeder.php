<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class LevelUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Isi Level
        DB::table('m_level')->insert([
            ['level_id' => 1, 'level_kode' => 'ADM', 'level_nama' => 'Administrator'],
            ['level_id' => 2, 'level_kode' => 'USR', 'level_nama' => 'User Biasa'],
        ]);

        // Isi User Contoh
        DB::table('m_user')->insert([
            [
                'level_id' => 1,
                'username' => 'admin',
                'nama' => 'Admin Wisata',
                'password' => Hash::make('123456')
            ],
            [
                'level_id' => 2,
                'username' => 'user',
                'nama' => 'Pengunjung Malang',
                'password' => Hash::make('123456')
            ],
        ]);
    }
}
