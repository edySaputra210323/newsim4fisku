<?php

namespace Database\Seeders;

use App\Models\Gedung;
use App\Models\Ruangan;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RuanganSeeder extends Seeder
{
    public function run()
    {
        $ruangans = [
            ['nama_gedung' => 'SMPIT AKHWAT', 'nama_ruangan' => 'Kantor Guru Akhwat', 'kode_ruangan' => 'KGA', 'deskripsi_ruangan' => 'Kantor Guru Akhwat', 'created_at' => now(), 'updated_at' => now()],
            ['nama_gedung' => 'SMPIT AKHWAT', 'nama_ruangan' => 'Kurikulum dan Bendahara', 'kode_ruangan' => 'KDB', 'deskripsi_ruangan' => 'Kurikulum dan Bendahara', 'created_at' => now(), 'updated_at' => now()],
            ['nama_gedung' => 'SMPIT Ikhwan', 'nama_ruangan' => 'TU dan WAKASIS', 'kode_ruangan' => 'TUW', 'deskripsi_ruangan' => 'TU dan WAKASIS', 'created_at' => now(), 'updated_at' => now()],
            ['nama_gedung' => 'SMPIT Ikhwan', 'nama_ruangan' => 'Kepala Sekolah', 'kode_ruangan' => 'KPS', 'deskripsi_ruangan' => 'Kepala Sekolah', 'created_at' => now(), 'updated_at' => now()],
            ['nama_gedung' => 'SMPIT Ikhwan', 'nama_ruangan' => 'Perpustakaan', 'kode_ruangan' => 'PST', 'deskripsi_ruangan' => 'Perpustakaan', 'created_at' => now(), 'updated_at' => now()],
        ];

        foreach ($ruangans as $data) {
            // Cari ID gedung berdasarkan nama gedung
            $gedung = Gedung::where('nama_gedung', $data['nama_gedung'])->first();

            // Pastikan gedung ditemukan sebelum membuat ruangan
            if ($gedung) {
                Ruangan::create([
                    'nama_ruangan' => $data['nama_ruangan'],
                    'gedung_id' => $gedung->id,
                    'kode_ruangan' => $data['kode_ruangan'],
                    'deskripsi_ruangan' => $data['deskripsi_ruangan'],
                    'created_at' => $data['created_at'],
                    'updated_at' => $data['updated_at'],
                ]);
            } else {
                $this->command->warn("Gedung dengan nama '{$data['nama_gedung']}' tidak ditemukan. Ruangan '{$data['nama_ruangan']}' tidak akan ditambahkan.");
            }
        }
    }
}
