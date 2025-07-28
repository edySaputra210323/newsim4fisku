<?php

namespace App\Filament\Admin\Resources\RiwayatKelasResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\RiwayatKelas;
use App\Models\TahunAjaran;
use App\Models\Semester;
use Illuminate\Database\Eloquent\Builder;

class RiwayatKelasWidgets extends BaseWidget
{
    protected static ?string $pollingInterval = '10s';
    protected function getStats(): array
    {
         // Ambil tahun ajaran dan semester aktif
         $activeTahunAjaran = TahunAjaran::where('status', true)->first();
         $activeSemester = $activeTahunAjaran
             ? Semester::where('th_ajaran_id', $activeTahunAjaran->id)->where('status', true)->first()
             : null;

             if (!$activeTahunAjaran || !$activeSemester) {
                return [
                    Stat::make('Laki-laki', '0')
                        ->description('Tidak ada data tahun ajaran/semester aktif')
                        ->color('warning')
                        ->icon('heroicon-o-user-group'),
                    Stat::make('Perempuan', '0')
                        ->description('Tidak ada data tahun ajaran/semester aktif')
                        ->color('warning')
                        ->icon('heroicon-o-user-group'),
                ];
            }

             // Hitung total laki-laki dan perempuan
        $data = RiwayatKelas::where('tahun_ajaran_id', $activeTahunAjaran->id)
        ->where('semester_id', $activeSemester->id)
        ->whereHas('dataSiswa', fn (Builder $query) => $query->aktif())
        ->join('data_siswa', 'riwayat_kelas.data_siswa_id', '=', 'data_siswa.id')
        ->selectRaw('data_siswa.jenis_kelamin, COUNT(*) as total')
        ->groupBy('data_siswa.jenis_kelamin')
        ->pluck('total', 'data_siswa.jenis_kelamin')
        ->toArray();

    $laki = $data['L'] ?? 0;
    $perempuan = $data['P'] ?? 0;
        return [
            Stat::make('Laki-laki', $laki)
                ->description('Jumlah siswa laki-laki aktif')
                ->color('primary')
                ->icon('heroicon-o-user-group'),
            Stat::make('Perempuan', $perempuan)
                ->description('Jumlah siswa perempuan aktif')
                ->color('success')
                ->icon('heroicon-o-user-group'),
            Stat::make('Total Siswa', $laki + $perempuan)
                ->description('Jumlah total siswa aktif')
                ->color('info')
                ->icon('heroicon-o-users'),
        ];
    }
}
