<?php

namespace Database\Seeders;

use App\Models\TahunAjaran;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TahunAjaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tahunAjaran = [
            [
                'th_ajaran' => '2021/2022',
                'status' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'th_ajaran' => '2022/2023',
                'status' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'th_ajaran' => '2023/2024',
                'status' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'th_ajaran' => '2024/2025',
                'status' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'th_ajaran' => '2025/2026',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($tahunAjaran as $tahun) {
            TahunAjaran::create($tahun);
        }
    }
}
