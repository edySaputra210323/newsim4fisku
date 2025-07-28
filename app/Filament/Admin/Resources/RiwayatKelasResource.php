<?php

namespace App\Filament\Admin\Resources;

use stdClass;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\DataSiswa;
use Filament\Tables\Table;
use App\Models\RiwayatKelas;
use Filament\Resources\Resource;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Section;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Contracts\HasTable;
use Filament\Notifications\Notification;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Admin\Resources\RiwayatKelasResource\Pages;
use App\Filament\Admin\Resources\RiwayatKelasResource\RelationManagers;
use App\Filament\Admin\Resources\RiwayatKelasResource\Widgets\RiwayatKelasWidgets;

class RiwayatKelasResource extends Resource
{
    public static function getWidgets(): array
    {
        return [
            RiwayatKelasWidgets::class,
        ];
    }

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
        ->extremePaginationLinks()
        ->recordUrl(null)
        ->paginated([40, 50, 60])
        ->defaultPaginationPageOption(40)
        ->striped()
        ->recordClasses(function () {
            $classes = 'table-vertical-align-top ';
            return $classes;
        })
            ->columns([
                Tables\Columns\TextColumn::make('index')
                ->label('No')
                ->width('1%')
                ->alignCenter()
                ->state(
                    static function (HasTable $livewire, stdClass $rowLoop): string {
                        return (string) (
                            $rowLoop->iteration +
                            (intval($livewire->getTableRecordsPerPage()) * (
                                intval($livewire->getTablePage()) - 1
                            ))
                        );
                    }
                ),
                Tables\Columns\TextColumn::make('dataSiswa.nama_siswa')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->label('Nama Siswa')
                    ->description(function ($record) {
                        $data = '';
                        if (!empty($record->dataSiswa->nis)) {
                            $data .= '<small>NIS : ' . $record->dataSiswa->nis . '</small>';
                        }
                        if (!empty($record->dataSiswa->nisn)) {
                            if ($data != '')
                                $data .= '<br>';
                            $data .= '<small>NISN : ' . $record->dataSiswa->nisn . '</small>';
                        }
                        return new HtmlString($data);
                    }),
                // Tables\Columns\TextColumn::make('dataSiswa.nis')
                //     ->label('NIS')
                //     ->sortable()
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('dataSiswa.nisn')
                //     ->label('NISN')
                //     ->sortable()
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('dataSiswa.nama_siswa')
                //     ->label('Nama Siswa')
                //     ->weight(FontWeight::Bold)
                //     ->sortable()
                //     ->searchable(),
                Tables\Columns\TextColumn::make('dataSiswa.jenis_kelamin')
                    ->label('JK'),
                Tables\Columns\TextColumn::make('dataSiswa.tempat_tanggal_lahir')
                    ->label('Tempat Tanggal Lahir'),
                Tables\Columns\TextColumn::make('dataSiswa.status_jumlah_saudara')
                    ->label('Jumlah Saudara'),
                Tables\Columns\TextColumn::make('dataSiswa.nm_ayah')
                    ->searchable()
                    ->weight(FontWeight::Bold)
                    ->label('Nama Ayah')
                    ->description(function ($record) {
                        $data = '';

                        if (!empty($record->dataSiswa->no_hp_ayah)) {
                            $data .= '<small>No HP Ayah: ' . $record->dataSiswa->no_hp_ayah . '</small>';
                        }

                        if (!empty($record->dataSiswa->pekerjaan_ayah_id) && $record->dataSiswa->pekerjaanAyah) {
                            if ($data != '') {
                                $data .= '<br>';
                            }
                            $data .= '<small>Pekerjaan Ayah: ' . $record->dataSiswa->pekerjaanAyah->nama_pekerjaan . '</small>';
                        }

                        if (!empty($record->dataSiswa->pendidikan_ayah_id) && $record->dataSiswa->pendidikanAyah) {
                            if ($data != '') {
                                $data .= '<br>';
                            }
                            $data .= '<small>Pendidikan Ayah: ' . $record->dataSiswa->pendidikanAyah->jenjang_pendidikan . '</small>';
                        }

                        if (!empty($record->dataSiswa->penghasilan_ayah_id) && $record->dataSiswa->penghasilanAyah) {
                            if ($data != '') {
                                $data .= '<br>';
                            }
                            $data .= '<small>Penghasilan Ayah: ' . $record->dataSiswa->penghasilanAyah->penghasilan . '</small>';
                        }

                        return new HtmlString($data);
                    }),
                    Tables\Columns\TextColumn::make('dataSiswa.nm_ibu')
                    ->searchable()
                    ->weight(FontWeight::Bold)
                    ->label('Nama Ibu')
                    ->description(function ($record) {
                        $data = '';

                        if (!empty($record->dataSiswa->no_hp_ibu)) {
                            $data .= '<small>No HP Ibu: ' . $record->dataSiswa->no_hp_ibu . '</small>';
                        }

                        if (!empty($record->dataSiswa->pekerjaan_ibu_id) && $record->dataSiswa->pekerjaanIbu) {
                            if ($data != '') {
                                $data .= '<br>';
                            }
                            $data .= '<small>Pekerjaan Ibu: ' . $record->dataSiswa->pekerjaanIbu->nama_pekerjaan . '</small>';
                        }

                        if (!empty($record->dataSiswa->pendidikan_ibu_id) && $record->dataSiswa->pendidikanIbu) {
                            if ($data != '') {
                                $data .= '<br>';
                            }
                            $data .= '<small>Pendidikan Ibu: ' . $record->dataSiswa->pendidikanIbu->jenjang_pendidikan . '</small>';
                        }

                        if (!empty($record->dataSiswa->penghasilan_ibu_id) && $record->dataSiswa->penghasilanIbu) {
                            if ($data != '') {
                                $data .= '<br>';
                            }
                            $data .= '<small>Penghasilan Ibu: ' . $record->dataSiswa->penghasilanIbu->penghasilan . '</small>';
                        }

                        return new HtmlString($data);
                    }),
                Tables\Columns\TextColumn::make('dataSiswa.alamat_lengkap')
                    ->label('Alamat')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('kelas.nama_kelas')
                    ->label('Kelas')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('guru.nm_pegawai')
                    ->label('Wali Kelas')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('tahunAjaran.th_ajaran')
                    ->label('Tahun Ajaran')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('semester.nm_semester')
                    ->label('Semester')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                SelectFilter::make('th_ajaran_id')
                    ->label('Tahun Ajaran')
                    ->relationship('tahunAjaran', 'th_ajaran')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('semester_id')
                    ->label('Semester')
                    ->relationship('semester', 'nm_semester')
                    ->searchable()
                    ->preload(),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->iconButton()
                    ->color('primary')
                    ->icon('heroicon-m-eye'),
                Tables\Actions\EditAction::make()
                ->iconButton()
                ->color('warning')
                ->icon('heroicon-m-pencil-square'),
            Tables\Actions\DeleteAction::make()
                ->iconButton()
                ->color('danger')
                ->icon('heroicon-m-trash')
                ->modalHeading('Hapus siswa dari rombongan belajar'),
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