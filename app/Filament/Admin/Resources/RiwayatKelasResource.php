<?php

namespace App\Filament\Admin\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\DataSiswa;
use Filament\Tables\Table;
use App\Models\RiwayatKelas;
use Filament\Resources\Resource;
use Filament\Forms\Components\Section;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Admin\Resources\RiwayatKelasResource\Pages;
use App\Filament\Admin\Resources\RiwayatKelasResource\RelationManagers;

class RiwayatKelasResource extends Resource
{
    protected static ?string $model = RiwayatKelas::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Siswa';

    protected static ?string $navigationLabel = 'Rombel';

    protected static ?string $modelLabel = 'Rombel';

    protected static ?string $pluralModelLabel = 'Rombel';

    protected static ?string $slug = 'rombel';

    public static function form(Form $form): Form
    {
        // Cek apakah ada tahun ajaran aktif
        $activeTahunAjaran = \App\Models\TahunAjaran::where('status', true)->first();
        $isTahunAjaranActive = !!$activeTahunAjaran;

        // Cek semester aktif berdasarkan tahun ajaran aktif
        $activeSemester = $isTahunAjaranActive
            ? \App\Models\Semester::where('th_ajaran_id', $activeTahunAjaran->id)
                ->where('status', true)
                ->first()
            : null;

        // Jika tidak ada tahun ajaran aktif, tampilkan notifikasi
        if (!$isTahunAjaranActive) {
            Notification::make()
                ->title('Peringatan')
                ->body('Tidak ada tahun ajaran yang aktif. Anda tidak dapat menambahkan riwayat kelas sampai tahun ajaran diaktifkan.')
                ->warning()
                ->persistent()
                ->send();
        }

        // Jika tidak ada semester aktif, tampilkan notifikasi
        if ($isTahunAjaranActive && !$activeSemester) {
            Notification::make()
                ->title('Peringatan')
                ->body('Tidak ada semester yang aktif untuk tahun ajaran ini. Anda tidak dapat menambahkan riwayat kelas sampai semester diaktifkan.')
                ->warning()
                ->persistent()
                ->send();
        }

        return $form
            ->schema([
                Section::make('Filter Siswa')
                    ->schema([
                        Forms\Components\Select::make('angkatan_filter')
                            ->label('Filter Angkatan')
                            ->options(
                                DataSiswa::distinct()
                                    ->pluck('angkatan', 'angkatan')
                                    ->filter()
                                    ->toArray()
                            )
                            ->placeholder('Pilih Angkatan')
                            ->reactive(),
                        Forms\Components\Select::make('jenis_kelamin_filter')
                            ->label('Filter Jenis Kelamin')
                            ->options([
                                'L' => 'Laki-laki',
                                'P' => 'Perempuan',
                            ])
                            ->placeholder('Pilih Jenis Kelamin')
                            ->reactive(),
                    ])->columns(2),
                Section::make('Data Rombel')
                    ->schema([
                        Forms\Components\Select::make('kelas_id')
                            ->label('Pilih Kelas')
                            ->relationship('kelas', 'nama_kelas')
                            ->required()
                            ->disabled(!$isTahunAjaranActive || !$activeSemester),
                        Forms\Components\Select::make('guru_id')
                            ->label('Pilih Wali Kelas')
                            ->relationship('guru', 'nm_pegawai')
                            ->required()
                            ->disabled(!$isTahunAjaranActive || !$activeSemester),
                    ])->columnSpan(1),
                Section::make('Data Siswa')
                    ->schema([
                        Forms\Components\Select::make('data_siswa_id')
                            ->label('Data Siswa')
                            ->searchable()
                            ->getSearchResultsUsing(function (string $search, $get) use ($activeTahunAjaran, $activeSemester) {
                                $query = DataSiswa::query()
                                    ->where(function ($q) use ($search) {
                                        $q->where('nama_siswa', 'like', "%{$search}%")
                                          ->orWhere('nis', 'like', "%{$search}%");
                                    });

                                // Terapkan filter angkatan jika dipilih
                                if ($angkatan = $get('angkatan_filter')) {
                                    $query->where('angkatan', $angkatan);
                                }

                                // Terapkan filter jenis kelamin jika dipilih
                                if ($jenisKelamin = $get('jenis_kelamin_filter')) {
                                    $query->where('jenis_kelamin', $jenisKelamin);
                                }

                                // Kecualikan siswa yang sudah terdaftar di riwayat kelas untuk tahun ajaran dan semester aktif
                                if ($activeTahunAjaran && $activeSemester) {
                                    $query->whereNotIn('id', function ($subQuery) use ($activeTahunAjaran, $activeSemester) {
                                        $subQuery->select('data_siswa_id')
                                            ->from('riwayat_kelas')
                                            ->where('tahun_ajaran_id', $activeTahunAjaran->id)
                                            ->where('semester_id', $activeSemester->id);
                                    });
                                }

                                return $query->limit(50)
                                    ->get()
                                    ->mapWithKeys(fn ($siswa) => [$siswa->id => "{$siswa->nama_siswa} - {$siswa->nis}"]);
                            })
                            ->getOptionLabelsUsing(function ($values): array {
                                return DataSiswa::whereIn('id', (array) $values)
                                    ->get()
                                    ->mapWithKeys(fn ($siswa) => [$siswa->id => "{$siswa->nama_siswa} - {$siswa->nis}"])
                                    ->toArray();
                            })
                            ->options(function ($get) use ($activeTahunAjaran, $activeSemester) {
                                $query = DataSiswa::query();

                                // Terapkan filter angkatan jika dipilih
                                if ($angkatan = $get('angkatan_filter')) {
                                    $query->where('angkatan', $angkatan);
                                }

                                // Terapkan filter jenis kelamin jika dipilih
                                if ($jenisKelamin = $get('jenis_kelamin_filter')) {
                                    $query->where('jenis_kelamin', $jenisKelamin);
                                }

                                // Kecualikan siswa yang sudah terdaftar di riwayat kelas untuk tahun ajaran dan semester aktif
                                if ($activeTahunAjaran && $activeSemester) {
                                    $query->whereNotIn('id', function ($subQuery) use ($activeTahunAjaran, $activeSemester) {
                                        $subQuery->select('data_siswa_id')
                                            ->from('riwayat_kelas')
                                            ->where('tahun_ajaran_id', $activeTahunAjaran->id)
                                            ->where('semester_id', $activeSemester->id);
                                    });
                                }

                                return $query->limit(50)
                                    ->get()
                                    ->mapWithKeys(fn ($siswa) => [$siswa->id => "{$siswa->nama_siswa} - {$siswa->nis}"]);
                            })
                            ->placeholder('PILIH DATA SISWA')
                            ->required()
                            ->disabled(!$isTahunAjaranActive || !$activeSemester),
                    ])->columnSpan(2),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('dataSiswa.nama_siswa')
                    ->label('Nama Siswa')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('kelas.nama_kelas')
                    ->label('Kelas')
                    ->sortable(),
                Tables\Columns\TextColumn::make('guru.nm_pegawai')
                    ->label('Wali Kelas')
                    ->sortable(),
                Tables\Columns\TextColumn::make('tahunAjaran.th_ajaran')
                    ->label('Tahun Ajaran')
                    ->sortable(),
                Tables\Columns\TextColumn::make('semester.nm_semester')
                    ->label('Semester')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRiwayatKelas::route('/'),
            'create' => Pages\CreateRiwayatKelas::route('/create'),
            'view' => Pages\ViewRiwayatKelas::route('/{record}'),
            'edit' => Pages\EditRiwayatKelas::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}