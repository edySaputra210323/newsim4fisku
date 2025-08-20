<?php

namespace App\Filament\Admin\Widgets;

use App\Models\DataSiswa;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class SiswaPerAngkatanChart extends ApexChartWidget
{
    // protected int|string|array $columnSpan = 6;

    protected static ?string $chartId = 'siswaPerAngkatanChart';

    protected static ?string $heading = 'Jumlah Siswa per Angkatan (Laki-laki - Perempuan - Total)';

    protected function getOptions(): array
    {
        $angkatanList = DataSiswa::select('angkatan')->distinct()->orderBy('angkatan')->pluck('angkatan');

        $lakiData = [];
        $perempuanData = [];
        $totalData = [];

        foreach ($angkatanList as $angkatan) {
            $laki = DataSiswa::where('angkatan', $angkatan)->where('jenis_kelamin', 'L')->count();
            $perempuan = DataSiswa::where('angkatan', $angkatan)->where('jenis_kelamin', 'P')->count();
            $total = $laki + $perempuan;

            $lakiData[] = $laki;
            $perempuanData[] = $perempuan;
            $totalData[] = $total;
        }

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 350,
                'stacked' => false,
            ],
            'series' => [
                [
                    'name' => 'Laki-laki',
                    'data' => $lakiData,
                ],
                [
                    'name' => 'Perempuan',
                    'data' => $perempuanData,
                ],
                [
                    'name' => 'Total',
                    'data' => $totalData,
                ],
            ],
            'xaxis' => [
                'categories' => $angkatanList->toArray(),
                'title' => ['text' => 'Angkatan'],
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'yaxis' => [
                'title' => ['text' => 'Jumlah Siswa'],
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'colors' => ['#3b82f6', '#ec4899', '#f59e0b'], // Biru, Pink, Kuning-Emas
            'plotOptions' => [
                'bar' => [
                    'horizontal' => false,
                    'columnWidth' => '55%',
                ],
            ],
            'legend' => [
                'position' => 'top',
                'horizontalAlign' => 'center',
            ],
        ];
    }
}
