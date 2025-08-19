<?php

namespace App\Filament\Admin\Resources\MutasiSiswaResource\Widgets;

use App\Models\MutasiSiswa;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class MutasiOverview extends BaseWidget
{
    protected function getStats(): array
    {
        // Hitung jumlah mutasi masuk
        $mutasiMasuk = MutasiSiswa::where('tipe_mutasi', 'Masuk')->count();

        // Hitung jumlah mutasi keluar
        $mutasiKeluar = MutasiSiswa::where('tipe_mutasi', 'Keluar')->count();

        // Hitung total mutasi
        $totalMutasi = MutasiSiswa::count();

        return [
            Stat::make('Mutasi Masuk', $mutasiMasuk)
                ->description('Total siswa yang masuk')
                ->descriptionIcon('heroicon-o-arrow-down-circle')
                ->color('success'),

            Stat::make('Mutasi Keluar', $mutasiKeluar)
                ->description('Total siswa yang keluar')
                ->descriptionIcon('heroicon-o-arrow-up-circle')
                ->color('danger'),

            Stat::make('Total Mutasi', $totalMutasi)
                ->description('Gabungan masuk & keluar')
                ->descriptionIcon('heroicon-o-arrows-right-left')
                ->color('primary'),
        ];
    }
}
