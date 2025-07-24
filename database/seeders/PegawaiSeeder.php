<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class PegawaiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        {
            DB::table('pegawai')->insert([
                [
                    'npy' => '05000008',
                    'nm_pegawai' => 'Tia Hafriana, S.Pd.',
                    'jenis_kelamin' => 'P',
                    'phone' => '081234567890',
                    'status' => true,
                ],
                [
                    'npy' => '05000015',
                    'nm_pegawai' => 'Yeni, S.Pd.',
                    'jenis_kelamin' => 'P',
                    'phone' => '082198765432',
                    'status' => true,
                ],
                [
                    'npy' => '05000018',
                    'nm_pegawai' => 'Siti Nurhalisa, S.H.',
                    'jenis_kelamin' => 'P',
                    'phone' => '085712345678',
                    'status' => true,
                ],
                [
                    'npy' => '05000032',
                    'nm_pegawai' => 'Yana Arsila, S.Pd.',
                    'jenis_kelamin' => 'P',
                    'phone' => '081324681357',
                    'status' => true,
                ],
                [
                    'npy' => '05000035',
                    'nm_pegawai' => 'Rasmawati, S.Pd.I.',
                    'jenis_kelamin' => 'P',
                    'phone' => '081299998888',
                    'status' => true,
                ],
                [
                    'npy' => '05000106',
                    'nm_pegawai' => 'Yuyun Rusmita, S.Pd.',
                    'jenis_kelamin' => 'P',
                    'phone' => '085267891234',
                    'status' => true,
                ],
                [
                    'npy' => '05000107',
                    'nm_pegawai' => 'Rusnahwati',
                    'jenis_kelamin' => 'P',
                    'phone' => '083845678912',
                    'status' => true,
                ],
                [
                    'npy' => '05000115',
                    'nm_pegawai' => 'Ardi, S.Pd.Gr.',
                    'jenis_kelamin' => 'L',
                    'phone' => '081912345678',
                    'status' => true,
                    ],
                [
                    'npy' => '05000201',
                    'nm_pegawai' => 'Heru Purwanto, S.Pd.',
                    'jenis_kelamin' => 'L',
                    'phone' => '087854321098',
                    'status' => true,
                ],
                [
                    'npy' => '05000205',
                    'nm_pegawai' => 'Rika Dwi Anggraini, S.Pd.',
                    'jenis_kelamin' => 'P',
                    'phone' => '089623456789',
                    'status' => true,
                ],
                [
                    'npy' => '05000311',
                    'nm_pegawai' => 'Mihrah Hasan, S.Pd.',
                    'jenis_kelamin' => 'P',
                    'phone' => '085678901234',
                    'status' => true,
                ],
                [
                    'npy' => '05000343',
                    'nm_pegawai' => 'Umi Kalsum, S.Pd.',
                    'jenis_kelamin' => 'P',
                    'phone' => '081111112222',
                    'status' => true,
                ],
                [
                    'npy' => '05000344',
                    'nm_pegawai' => 'Pandu Riyandi, S.Pd.',
                    'jenis_kelamin' => 'L',
                    'phone' => '083123456789',
                    'status' => true,
                ],
                [
                    'npy' => '05000345',
                    'nm_pegawai' => 'Erik Rahmana, S.Pd.',
                    'jenis_kelamin' => 'L',
                    'phone' => '081254329876',
                    'status' => true,
                ],
                [
                    'npy' => '05000346',
                    'nm_pegawai' => 'Nur Hidayatullah, S.Pd.',
                    'jenis_kelamin' => 'L',
                    'phone' => '082313578642',
                    'status' => true,
                ],
                [
                    'npy' => '05000347',
                    'nm_pegawai' => 'Sutrisno, S.Pd.',
                    'jenis_kelamin' => 'L',
                    'phone' => '089567894321',
                    'status' => true,
                    ],
                [
                    'npy' => '05000348',
                    'nm_pegawai' => 'Siti Amalia Hidayah, S.Pd.',
                    'jenis_kelamin' => 'P',
                    'phone' => '085123456789',
                    'status' => true,
                ],
                [
                    'npy' => '05000349',
                    'nm_pegawai' => 'Ana Khairunnisa, S.Pd.',
                    'jenis_kelamin' => 'P',
                    'phone' => '087776543210',
                    'status' => true,
                ],
                [
                    'npy' => '05000350',
                    'nm_pegawai' => 'Juli Elmariza, S.Si.',
                    'jenis_kelamin' => 'P',
                    'phone' => '081287654321',
                    'status' => true,
                ],
                [
                    'npy' => '05000352',
                    'nm_pegawai' => 'Wulandari, S.Pd.',
                    'jenis_kelamin' => 'P',
                    'phone' => '083212345678',
                    'status' => true,
                    ],
                [
                    'npy' => '05000355',
                    'nm_pegawai' => 'Agus Purwanti, S.Pd.',
                    'jenis_kelamin' => 'P',
                    'phone' => '082156784321',
                    'status' => true,
                ],
                [
                    'npy' => '05000356',
                    'nm_pegawai' => 'Ahmad Awaludin, S.Pd, M.Pd.',
                    'jenis_kelamin' => 'L',
                    'phone' => '085324681357',
                    'status' => true,
                ],
                [
                    'npy' => '05000441',
                    'nm_pegawai' => 'Prettyana, S.Pd.',
                    'jenis_kelamin' => 'P',
                    'phone' => '081599997777',
                    'status' => true,
                ],
                [
                    'npy' => '05000468',
                    'nm_pegawai' => 'Nur Jayanti, A.Md.',
                    'jenis_kelamin' => 'P',
                    'phone' => '083345671234',
                    'status' => true,
                ],
                [
                    'npy' => '05000481',
                    'nm_pegawai' => 'M Yudi, A.Md.',
                    'jenis_kelamin' => 'L',
                    'phone' => '081267892345',
                    'status' => true,
                ],
                [
                    'npy' => '05000497',
                    'nm_pegawai' => 'Dian Faqih, S.Pd., Gr.',
                    'jenis_kelamin' => 'P',
                    'phone' => '089734567890',
                    'status' => true,
                ],
                [
                    'npy' => '05000551',
                    'nm_pegawai' => 'Faza Andary Qatrunnada, Lc',
                    'jenis_kelamin' => 'P',
                    'phone' => '081754329876',
                    'status' => true,
                ],
                [
                    'npy' => '05000552',
                    'nm_pegawai' => 'Ibmu Kausar, M.Pd.',
                    'jenis_kelamin' => 'L',
                    'phone' => '0895609982896',
                    'status' => true,
                ],
            ]);
        }
    }
}
