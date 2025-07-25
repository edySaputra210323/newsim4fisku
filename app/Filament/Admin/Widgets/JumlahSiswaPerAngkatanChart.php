<?php

namespace App\Filament\Admin\Widgets;

use Illuminate\Support\Facades\DB;
use EightyNine\FilamentAdvancedWidget\AdvancedChartWidget;

class JumlahSiswaPerAngkatanChart extends AdvancedChartWidget
{
    // Judul yang akan ditampilkan di widget dashboard
    protected static ?string $heading = 'Jumlah Siswa per Angkatan';

    // Ikon opsional untuk widget (gunakan ikon Heroicons)
    protected static ?string $icon = 'heroicon-o-chart-bar';

    protected static string $color = 'info';

    protected static ?string $badgeSize = 'xs';

    // Warna opsional untuk widget (bisa 'primary', 'success', 'danger', 'warning', dll.)
    protected static ?string $iconColor = 'primary';

    protected function getData(): array
    {
        // Mengambil data dari tabel 'data_siswa'
        // Kita menghitung jumlah siswa untuk setiap 'angkatan'
        $dataSiswaPerAngkatan = DB::table('data_siswa')
                                ->select('angkatan', DB::raw('count(*) as total_siswa'))
                                ->groupBy('angkatan')
                                ->orderBy('angkatan') // Urutkan berdasarkan angkatan agar rapi
                                ->get();

        // Memisahkan 'angkatan' menjadi label untuk sumbu X
        $labels = $dataSiswaPerAngkatan->pluck('angkatan')->toArray();

        // Memisahkan 'total_siswa' menjadi data untuk batang grafik
        $jumlahSiswa = $dataSiswaPerAngkatan->pluck('total_siswa')->toArray();
        return [            
            'labels' => $labels, // Label untuk sumbu X (angkatan)
            'datasets' => [
                [
                    'label' => 'Jumlah Siswa', // Label untuk dataset ini
                    'data' => $jumlahSiswa, // Data jumlah siswa
                    'backgroundColor' => [ // Warna latar belakang untuk setiap batang
                        'rgba(52, 149, 235)', // Hijau kebiruan
                    ],
                    'borderColor' => [ 
                        'rgba(52, 149, 235)',
                    ],
                    'borderWidth' => 1, // Lebar border batang
                ],
            ],
        ];
    }

     // Metode ini untuk mengatur opsi tampilan grafik (misalnya judul sumbu, responsif, dll.)
    protected function getOptions(): array
    {
        return [
            'responsive' => true, // Grafik akan responsif terhadap ukuran layar
            'scales' => [
                'y' => [
                    'beginAtZero' => true, // Sumbu Y dimulai dari nol
                    'title' => [
                        'display' => true,
                        'text' => 'Jumlah Siswa', // Judul sumbu Y
                    ],
                    'ticks' => [
                        'precision' => 0, // Pastikan nilai di sumbu Y adalah bilangan bulat
                    ],
                ],
                'x' => [
                    'title' => [
                        'display' => true,
                        'text' => 'Angkatan', // Judul sumbu X
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => false, // Tidak menampilkan legenda karena hanya ada satu dataset
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
