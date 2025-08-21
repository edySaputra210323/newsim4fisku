<?php

namespace App\Filament\Admin\Pages\DataSiswa;

use App\Models\DataSiswa;
use App\Models\RiwayatKelas;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\DB;

class StatistikDataSiswaPages extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-chart-pie';
    protected static ?string $navigationGroup = 'Laporan';
    protected static ?string $navigationLabel = 'Statistik Data Siswa';
    protected static string $view = 'filament.admin.pages.data-siswa.statistik-data-siswa-pages';

    public function table(Table $table): Table
    {
        // --- Hitung semua statistik (siswa Aktif saja) ---
        $jumlahRombel = RiwayatKelas::distinct('kelas_id')->count('kelas_id');

        $jumlahSiswaPerempuan = DataSiswa::whereIn('jenis_kelamin', ['P','Perempuan'])
            ->whereHas('UpdateStatusSiswa', fn($q) => $q->whereRaw('LOWER(status) = ?', ['aktif']))
            ->count();

        $jumlahSiswaLaki = DataSiswa::whereIn('jenis_kelamin', ['L','Laki-laki'])
            ->whereHas('UpdateStatusSiswa', fn($q) => $q->whereRaw('LOWER(status) = ?', ['aktif']))
            ->count();

        $jumlahSiswaYatim = DataSiswa::whereHas('UpdateStatusSiswa', fn($q) =>
                $q->whereRaw('LOWER(status) = ?', ['aktif'])
            )
            ->whereIn('yatim_piatu', ['Yatim', 'Piatu', 'Yatim Piatu'])
            ->count();

        $totalSiswaAktif = DataSiswa::whereHas('UpdateStatusSiswa', fn($q) =>
                $q->whereRaw('LOWER(status) = ?', ['aktif'])
            )
            ->count();

        // --- Buat subquery union ALL ---
        $q1 = DB::query()->selectRaw('? as keterangan, ? as jumlah', ['Jumlah Rombel', $jumlahRombel]);
        $q2 = DB::query()->selectRaw('? as keterangan, ? as jumlah', ['Total Siswa Aktif', $totalSiswaAktif]);
        $q3 = DB::query()->selectRaw('? as keterangan, ? as jumlah', ['Laki-laki Aktif', $jumlahSiswaLaki]);
        $q4 = DB::query()->selectRaw('? as keterangan, ? as jumlah', ['Perempuan Aktif', $jumlahSiswaPerempuan]);
        $q5 = DB::query()->selectRaw('? as keterangan, ? as jumlah', ['Yatim / Piatu / Yatim Piatu', $jumlahSiswaYatim]);

        $union = $q1->unionAll($q2)->unionAll($q3)->unionAll($q4)->unionAll($q5);

        $query = DB::query()
            ->fromSub($union, 'stats')
            ->select('keterangan', 'jumlah');

        // --- Tabel Filament ---
        return $table
            ->query($query)
            ->columns([
                Stack::make([
                    TextColumn::make('keterangan')
                        ->weight('bold')
                        ->size('lg'),
                    TextColumn::make('jumlah')
                        ->color('primary')
                        ->size('xl'),
                ]),
            ])
            ->paginated(false)
            ->recordAction(null);
    }
}
