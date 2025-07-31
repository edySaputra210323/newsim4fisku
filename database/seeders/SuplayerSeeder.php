<?php

namespace Database\Seeders;

use App\Models\Suplayer;
use Illuminate\Database\Seeder;

class SuplayerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suplayer = [
            [
                'nama_suplayer' => 'Suplayer 1',
                'alamat_suplayer' => 'Alamat Suplayer 2',
                'no_telp_suplayer' => '08123456789',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_suplayer' => 'Suplayer 2',
                'alamat_suplayer' => 'Alamat Suplayer 2',
                'no_telp_suplayer' => '08123456789',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_suplayer' => 'Suplayer 3',
                'alamat_suplayer' => 'Alamat Suplayer 2',
                'no_telp_suplayer' => '08123456789',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_suplayer' => 'Suplayer 4',
                'alamat_suplayer' => 'Alamat Suplayer 2',
                'no_telp_suplayer' => '08123456789',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_suplayer' => 'Suplayer 5',
                'alamat_suplayer' => 'Alamat Suplayer 2',
                'no_telp_suplayer' => '08123456789',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($suplayer as $suplayer) {
            Suplayer::create($suplayer);
        }
    }
}
