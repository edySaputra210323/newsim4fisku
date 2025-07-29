<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class KategoriBarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('kategori_barang')->insert([
            [
                'nama_kategori_barang' => 'KOMUDITI PERANGKAT KOMPUTER',
                'kode_kategori_barang' => 'KPK',
                'deskripsi_kategori_barang' => null, // Atau bisa diisi deskripsi jika ada
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nama_kategori_barang' => 'KOMUDITI LABORATORIUM IPA',
                'kode_kategori_barang' => 'L-IPA',
                'deskripsi_kategori_barang' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nama_kategori_barang' => 'KOMUDITI ELEKTRONIK',
                'kode_kategori_barang' => 'ELK',
                'deskripsi_kategori_barang' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nama_kategori_barang' => 'KOMUDITI PERALATAN DAPUR',
                'kode_kategori_barang' => 'DPR',
                'deskripsi_kategori_barang' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nama_kategori_barang' => 'KOMUDITI PERLATAN KEBERSIHAN',
                'kode_kategori_barang' => 'CLEAN',
                'deskripsi_kategori_barang' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nama_kategori_barang' => 'KOMUDITI PROPERTI',
                'kode_kategori_barang' => 'KP',
                'deskripsi_kategori_barang' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nama_kategori_barang' => 'KOMUDITI PERALATAN KESEHATAN',
                'kode_kategori_barang' => 'KES',
                'deskripsi_kategori_barang' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nama_kategori_barang' => 'PERALATAN PEMBLAJARAN',
                'kode_kategori_barang' => 'PP',
                'deskripsi_kategori_barang' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
