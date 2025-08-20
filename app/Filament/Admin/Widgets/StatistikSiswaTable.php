<?php

namespace App\Filament\Admin\Widgets;

use App\Models\RiwayatKelas;
use App\Models\DataSiswa;
use App\Models\Statistik;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class StatistikSiswaTable extends BaseWidget
{
    protected static ?string $heading = 'Statistik Siswa TA Aktif';

    // protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        // hitung data
        $jumlahRombel = RiwayatKelas::distinct('kelas_id')->count('kelas_id');

        // hitung perempuan
        $jumlahSiswaPerempuan = DataSiswa::whereIn('jenis_kelamin', ['P','Perempuan'])
            ->whereHas('UpdateStatusSiswa', fn($q) => $q->whereRaw('LOWER(status) = ?', ['aktif']))
            ->count();

        // hitung laki-laki
        $jumlahSiswaLaki = DataSiswa::whereIn('jenis_kelamin', ['L','Laki-laki'])
            ->whereHas('UpdateStatusSiswa', fn($q) => $q->whereRaw('LOWER(status) = ?', ['aktif']))
            ->count();

        // hanya ambil yang aktif
        $jumlahYatim = DataSiswa::where('yatim_piatu', 'Yatim')
            ->whereHas('UpdateStatusSiswa', fn($q) => 
                $q->whereRaw('LOWER(status) = ?', ['aktif'])
            )
            ->count();

        $jumlahYatimPiatu = DataSiswa::where('yatim_piatu', 'Yatim Piatu')
            ->whereHas('UpdateStatusSiswa', fn($q) => 
                $q->whereRaw('LOWER(status) = ?', ['aktif'])
            )
            ->count();

        // total semua
        $jumlahSiswaYatim = $jumlahYatim + $jumlahYatimPiatu;

        $TotalSiswaAktif = DataSiswa::whereHas('UpdateStatusSiswa', fn($q) => $q->whereRaw('LOWER(status) = ?', ['aktif']))->count();

        // bersihkan isi tabel statistik lama (biar selalu update)
        Statistik::truncate();

        // isi ulang
        Statistik::insert([
            ['keterangan' => 'Jumlah Rombel', 'jumlah' => $jumlahRombel],
            ['keterangan' => 'Total Perempuan', 'jumlah' => $jumlahSiswaPerempuan],
            ['keterangan' => 'Total Laki-laki', 'jumlah' => $jumlahSiswaLaki],
            ['keterangan' => 'Total Siswa (Yatim / Yatim Piatu)', 'jumlah' => $jumlahSiswaYatim],
            ['keterangan' => 'Total Siswa Aktif', 'jumlah' => $TotalSiswaAktif],
        ]);

        return $table
            ->query(Statistik::query())
            ->columns([
                Tables\Columns\TextColumn::make('keterangan')
                    ->label('Keterangan'),
                Tables\Columns\TextColumn::make('jumlah')
                    ->label('Jumlah')
                    ->weight(fn ($record) =>
                        $record->keterangan === 'Total Siswa Aktif' ? 'bold' : null
                    ),
            ])
            ->paginated(false)
            ->recordAction(null);
    }
}
