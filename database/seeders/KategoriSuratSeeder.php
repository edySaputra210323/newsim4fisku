<?php

namespace Database\Seeders;

use App\Models\KategoriSurat;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class KategoriSuratSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kategoriSurat = [
            ['kode_kategori' => 'ST', 'kategori' => 'SURAT TUGAS', 'deskripsi' => NULL, 'created_at' => now(), 'updated_at' => now()],
            ['kode_kategori' => 'S.KA', 'kategori' => 'SURAT KUASA', 'deskripsi' => NULL, 'created_at' => now(), 'updated_at' => now()],
            ['kode_kategori' => 'SR', 'kategori' => 'SURAT REKOMENDASI', 'deskripsi' => NULL, 'created_at' => now(), 'updated_at' => now()],
            ['kode_kategori' => 'P', 'kategori' => 'PEMBERITAHUAN', 'deskripsi' => NULL, 'created_at' => now(), 'updated_at' => now()],
            ['kode_kategori' => 'U', 'kategori' => 'SURAT UNDANGAN', 'deskripsi' => NULL, 'created_at' => now(), 'updated_at' => now()],
            ['kode_kategori' => 'SP', 'kategori' => 'SURAT PERINGATAN', 'deskripsi' => NULL, 'created_at' => now(), 'updated_at' => now()],
            ['kode_kategori' => 'S.Ket', 'kategori' => 'SURAT KETERANGAN', 'deskripsi' => NULL, 'created_at' => now(), 'updated_at' => now()],
            ['kode_kategori' => 'S.PPD', 'kategori' => 'SURAT PERINTAH PERJALANAN DINAS', 'deskripsi' => NULL, 'created_at' => now(), 'updated_at' => now()],
            ['kode_kategori' => 'SM', 'kategori' => 'SURAT MANDAT (KEWENANGAN)', 'deskripsi' => NULL, 'created_at' => now(), 'updated_at' => now()],
            ['kode_kategori' => 'T', 'kategori' => 'SURAT TAGIHAN', 'deskripsi' => NULL, 'created_at' => now(), 'updated_at' => now()],
            ['kode_kategori' => 'PH', 'kategori' => 'SURAT PERMOHONAN', 'deskripsi' => NULL, 'created_at' => now(), 'updated_at' => now()],
            ['kode_kategori' => 'PJ', 'kategori' => 'SURAT PERJANJIAN', 'deskripsi' => NULL, 'created_at' => now(), 'updated_at' => now()],
            ['kode_kategori' => 'PG', 'kategori' => 'SURAT PANGGILAN ORANG TUA', 'deskripsi' => NULL, 'created_at' => now(), 'updated_at' => now()],
            ['kode_kategori' => 'SPK', 'kategori' => 'SURAT PERJANJIAN KERJA', 'deskripsi' => NULL, 'created_at' => now(), 'updated_at' => now()],
            ['kode_kategori' => 'SPT', 'kategori' => 'SURAT PENGANTAR', 'deskripsi' => NULL, 'created_at' => now(), 'updated_at' => now()],
            ['kode_kategori' => 'SPn', 'kategori' => 'SURAT PINDAH', 'deskripsi' => NULL, 'created_at' => now(), 'updated_at' => now()],
            ['kode_kategori' => 'OP', 'kategori' => 'ORDER PEMBELIAN', 'deskripsi' => NULL, 'created_at' => now(), 'updated_at' => now()],
            ['kode_kategori' => 'SK', 'kategori' => 'SURAT KEPUTUSAN', 'deskripsi' => NULL, 'created_at' => now(), 'updated_at' => now()],
            ['kode_kategori' => 'SIP', 'kategori' => 'SURAT IZIN PIMPINAN', 'deskripsi' => NULL, 'created_at' => now(), 'updated_at' => now()],
            ['kode_kategori' => 'SKR', 'kategori' => 'SURAT SKORSING', 'deskripsi' => NULL, 'created_at' => now(), 'updated_at' => now()],
            ['kode_kategori' => 'PKS', 'kategori' => 'PERJANJIAN KERJA SAMA', 'deskripsi' => NULL, 'created_at' => now(), 'updated_at' => now()],
            ['kode_kategori' => 'SE', 'kategori' => 'SURAT EDARAN', 'deskripsi' => NULL, 'created_at' => now(), 'updated_at' => now()],
            ['kode_kategori' => 'SK-GTK', 'kategori' => 'SURAT KEPUTUSAN (PENEMPATAN DAN PENGANGKATAN)', 'deskripsi' => NULL, 'created_at' => now(), 'updated_at' => now()],
            ['kode_kategori' => 'SK-Pan', 'kategori' => 'SURAT KEPUTUSAN (PANITIA KEGIATAN)', 'deskripsi' => NULL, 'created_at' => now(), 'updated_at' => now()],
            ['kode_kategori' => 'SPR', 'kategori' => 'SURAT PERNYATAAN', 'deskripsi' => NULL, 'created_at' => now(), 'updated_at' => now()],
        ];

        foreach ($kategoriSurat as $kategori) {
            KategoriSurat::create($kategori);
        }
    
    }
}
