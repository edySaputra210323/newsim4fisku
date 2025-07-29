<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class KategoriInventarisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['nama_kategori_inventaris' => 'ASET' , 'kode_kategori_inventaris' => 'A', 'deskripsi_kategori_inventaris' => 'Aset'],
            ['nama_kategori_inventaris' => 'INVENTARIS' , 'kode_kategori_inventaris' => 'B', 'deskripsi_kategori_inventaris' => 'Inventaris'],
            ['nama_kategori_inventaris' => 'PERLENGKAPAN' , 'kode_kategori_inventaris' => 'C', 'deskripsi_kategori_inventaris' => 'Perlengkapan'],
        ];

        foreach ($data as $item) {
            DB::table('kategori_inventaris')->insert([
                'nama_kategori_inventaris' => $item['nama_kategori_inventaris'],
                'kode_kategori_inventaris' => $item['kode_kategori_inventaris'],
                'deskripsi_kategori_inventaris' => $item['deskripsi_kategori_inventaris'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
