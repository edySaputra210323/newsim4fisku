<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class GedungSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'kode_gedung' => 'A',
                'nama_gedung' => 'YAYASAN',
                'deskripsi_gedung' => 'Gedung Yayasan',
            ],
            [
                'kode_gedung' => 'B',
                'nama_gedung' => 'TKIT',
                'deskripsi_gedung' => 'Gedung TKIT',
            ],
            [
                'kode_gedung' => 'C',
                'nama_gedung' => 'SDIT',
                'deskripsi_gedung' => 'Gedung SDIT',
            ],
            [
                'kode_gedung' => 'D',
                'nama_gedung' => 'SMPIT AKHWAT',
                'deskripsi_gedung' => 'Gedung SMPIT AKHWAT',
            ],
            [
                'kode_gedung' => 'E',
                'nama_gedung' => 'SMAIT AKHWAT',
                'deskripsi_gedung' => 'Gedung SMAIT AKHWAT',
            ],
            [
                'kode_gedung' => 'F',
                'nama_gedung' => 'SMPIT Ikhwan',
                'deskripsi_gedung' => 'Gedung SMPIT Ikhwan',
            ],
            [
                'kode_gedung' => 'G',
                'nama_gedung' => 'ASRAMA AKHWAT 1',
                'deskripsi_gedung' => 'Gedung ASRAMA AKHWAT 1',
            ],
            [
                'kode_gedung' => 'H',
                'nama_gedung' => 'ASRAMA AKHWAT 2',
                'deskripsi_gedung' => 'Gedung ASRAMA AKHWAT 2',
            ],
            [
                'kode_gedung' => 'I',
                'nama_gedung' => 'ASRAMA IKHWAN 3',
                'deskripsi_gedung' => 'Gedung ASRAMA IKHWAN 3',
            ],
            [
                'kode_gedung' => 'J',
                'nama_gedung' => 'ASRAMA IKHWAN 4',
                'deskripsi_gedung' => 'Gedung ASRAMA IKHWAN 4',
            ],
            [
                'kode_gedung' => 'K',
                'nama_gedung' => 'RESTORAN AKHWAT',
                'deskripsi_gedung' => 'Gedung RESTORAN AKHWAT',
            ],
            [
                'kode_gedung' => 'L',
                'nama_gedung' => 'RESTORAN IKHWAN',
                'deskripsi_gedung' => 'Gedung RESTORAN IKHWAN',
            ],
            [
                'kode_gedung' => 'M',
                'nama_gedung' => 'AULA',
                'deskripsi_gedung' => 'Gedung AULA',
            ],
            [
                'kode_gedung' => 'O',
                'nama_gedung' => 'MASJID',
                'deskripsi_gedung' => 'Gedung MASJID',
            ],
            [
                'kode_gedung' => 'P',
                'nama_gedung' => 'LONDRY',
                'deskripsi_gedung' => 'Gedung LONDRY',
            ],
            [
                'kode_gedung' => 'Q',
                'nama_gedung' => 'RUMAH YAYASAN',
                'deskripsi_gedung' => 'Gedung RUMAH YAYASAN',
            ],
            [
                'kode_gedung' => 'R',
                'nama_gedung' => 'SECURITY',
                'deskripsi_gedung' => 'Gedung SECURITY',
            ],
            [
                'kode_gedung' => 'N',
                'nama_gedung' => 'GOR',
                'deskripsi_gedung' => 'Gedung GOR',
            ],
        ];

        foreach ($data as $item) {
            DB::table('gedung')->insert([
                'kode_gedung' => $item['kode_gedung'],
                'nama_gedung' => $item['nama_gedung'],
                'deskripsi_gedung' => $item['deskripsi_gedung'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
