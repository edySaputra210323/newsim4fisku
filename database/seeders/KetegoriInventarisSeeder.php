<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class KetegoriInventarisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ["nama_kategori_inventaris" => "Aset", "kode_kategori_inventaris" => "A", "deskripsi_kategori_inventaris" => "Nilai besar, masa manfaat lebih dari 1 tahun, dicatat sebagai aset"],
            ["nama_kategori_inventaris" => "Inventaris", "kode_kategori_inventaris" => "B", "deskripsi_kategori_inventaris" => "Tidak habis pakai dengan cepat, dipakai ulang, dicatat di KIB"],
            ["nama_kategori_inventaris" => "Perlengkapan", "kode_kategori_inventaris" => "C", "deskripsi_kategori_inventaris" => "Habis pakai, pembelian rutin, dicatat sebagai biaya operasional"],
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
