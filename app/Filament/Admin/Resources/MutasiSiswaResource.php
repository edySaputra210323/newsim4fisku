<?php

namespace App\Filament\Admin\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Kelas;
use App\Models\Semester;
use Filament\Forms\Form;
use App\Models\DataSiswa;
use Filament\Tables\Table;
use App\Models\MutasiSiswa;
use App\Models\TahunAjaran;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Illuminate\Support\Facades\Storage;
use Filament\Notifications\Notification;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Admin\Resources\MutasiSiswaResource\Pages;
use App\Filament\Admin\Resources\MutasiSiswaResource\RelationManagers;
use Filament\Forms\Components\Section as FormSection; // Alias untuk Section di Form

class MutasiSiswaResource extends Resource
{
    protected static ?string $model = MutasiSiswa::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';

    protected static ?string $navigationGroup = 'Siswa';

    protected static ?string $navigationLabel = 'Mutasi Siswa';

    protected static ?string $modelLabel = 'Mutasi Siswa';

    protected static ?string $pluralModelLabel = 'Mutasi Siswa';

    protected static ?string $slug = 'mutasi-siswa';

    public static function form(Form $form): Form
    {
        // Cek apakah ada tahun ajaran aktif
       $activeTahunAjaran = TahunAjaran::where('status', true)->first();
       $isTahunAjaranActive = !!$activeTahunAjaran;

       // Cek semester aktif berdasarkan tahun ajaran aktif
       $activeSemester = $isTahunAjaranActive
           ? Semester::where('th_ajaran_id', $activeTahunAjaran->id)
               ->where('status', true)
               ->first()
           : null;

       // Jika tidak ada tahun ajaran aktif, tampilkan notifikasi
       if (!$isTahunAjaranActive) {
           Notification::make()
               ->title('Peringatan')
               ->body('Tidak ada tahun ajaran yang aktif. Anda tidak dapat membuat surat keluar sampai tahun ajaran diaktifkan.')
               ->warning()
               ->persistent()
               ->send();
       }

       // Jika tidak ada semester aktif, tampilkan notifikasi
       if ($isTahunAjaranActive && !$activeSemester) {
           Notification::make()
               ->title('Peringatan')
               ->body('Tidak ada semester yang aktif untuk tahun ajaran ini. Anda tidak dapat membuat surat keluar sampai semester diaktifkan.')
               ->warning()
               ->persistent()
               ->send();
       }
        return $form
            ->schema([
                        FormSection::make('Mutasi Siswa')
                            ->description('transaksional mutasi siswa masuk dan keluar')
                            ->icon('heroicon-m-user-group')
                            ->schema([
                                // Field untuk memilih siswa, hanya siswa berstatus Aktif yang muncul
                                Forms\Components\Select::make('data_siswa_id')
                                ->label('Data Siswa')
                                ->searchable()
                                ->required()
                                ->columnSpanFull()
                                ->disabled(!$isTahunAjaranActive || !$activeSemester)
                                ->getSearchResultsUsing(function (string $search, $get) use ($activeTahunAjaran, $activeSemester) {
                                    return DataSiswa::query()
                                        ->where(function ($q) use ($search) {
                                            $q->where('nama_siswa', 'like', "%{$search}%")
                                            ->orWhere('nis', 'like', "%{$search}%");
                                        })
                                        ->whereHas('UpdateStatusSiswa', function ($query) {
                                            $query->where('status', 'Aktif');
                                        })
                                        ->limit(50) // Batasi hasil untuk performa
                                        ->get()
                                        ->mapWithKeys(function ($siswa) {
                                            // Format hasil pencarian: id => nama siswa (NIS)
                                            return [$siswa->id => $siswa->nama_siswa . ' (' . $siswa->nis . ')'];
                                        })
                                        ->toArray();
                                })
                                ->getOptionLabelUsing(function ($value): ?string {
                                    $siswa = \App\Models\DataSiswa::find($value);
                            
                                    return $siswa ? "{$siswa->nama_siswa} ({$siswa->nis})" : null;
                                })
                                 // Validasi tambahan: pastikan siswa berstatus Aktif jika tipe mutasi adalah Keluar
                                // Ini mendukung logika bahwa hanya siswa aktif yang bisa pindah (status diubah ke Pindah)
                                ->rules([
                                    fn ($get, $context) => function ($attribute, $value, $fail) use ($get, $context) {
                                        if ($context === 'create' && $get('tipe_mutasi') === 'Keluar') {
                                            $siswa = \App\Models\DataSiswa::find($value);
                                            if ($siswa && $siswa->UpdateStatusSiswa && $siswa->UpdateStatusSiswa->status !== 'Aktif') {
                                                $fail('Siswa yang dipilih harus berstatus Aktif untuk mutasi keluar.');
                                            }
                                        }
                                    },
                                ]),
                            Forms\Components\Select::make('kelas_id')
                            ->label('Kelas yang ditinggalkan / Kelas tujuan')
                            ->disabled(!$isTahunAjaranActive || !$activeSemester)
                            ->options(Kelas::all()->pluck('nama_kelas', 'id'))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpanFull(),
                        // Field untuk memilih tipe mutasi (Masuk/Keluar)
                            Forms\Components\Select::make('tipe_mutasi')
                            ->label('Tipe Mutasi')
                            ->disabled(!$isTahunAjaranActive || !$activeSemester)
                            ->options([
                                'Masuk' => 'Mutasi Masuk',
                                'Keluar' => 'Mutasi Keluar',
                            ])
                            ->required()
                            ->placeholder('Pilih Tipe Mutasi')
                            ->reactive() // Membuat field ini reaktif untuk mengontrol visibilitas field lain
                            ->columnSpanFull(),
                            // Grid untuk field terkait mutasi masuk
                        Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('asal_sekolah')
                                    ->label('Asal Sekolah')
                                    ->disabled(!$isTahunAjaranActive || !$activeSemester)
                                    ->maxLength(255)
                                    ->required(fn ($get) => $get('tipe_mutasi') === 'Masuk')
                                    ->visible(fn ($get) => $get('tipe_mutasi') === 'Masuk'),
                                Forms\Components\TextInput::make('nomor_mutasi_masuk')
                                    ->label('Nomor Mutasi Masuk')
                                    ->disabled(!$isTahunAjaranActive || !$activeSemester)
                                    ->maxLength(255)
                                    ->required(fn ($get) => $get('tipe_mutasi') === 'Masuk')
                                    ->visible(fn ($get) => $get('tipe_mutasi') === 'Masuk'),
                            ]),
                        Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('sekolah_tujuan')
                                    ->label('Sekolah Tujuan')
                                    ->disabled(!$isTahunAjaranActive || !$activeSemester)
                                    ->maxLength(255)
                                    ->required(fn ($get) => $get('tipe_mutasi') === 'Keluar')
                                    ->visible(fn ($get) => $get('tipe_mutasi') === 'Keluar'),
                                Forms\Components\TextInput::make('nomor_mutasi_keluar')
                                    ->label('Nomor Mutasi Keluar')
                                    ->disabled(!$isTahunAjaranActive || !$activeSemester)
                                    ->maxLength(255)
                                    ->required(fn ($get) => $get('tipe_mutasi') === 'Keluar')
                                    ->visible(fn ($get) => $get('tipe_mutasi') === 'Keluar'),
                            ]),
                        
                    ])->columnSpan(2)->columns(2),
                    FormSection::make('Dokumen Mutasi')
                    ->icon('heroicon-m-document-text')
                    ->schema([
                        Forms\Components\FileUpload::make('dokumen_mutasi')
                        ->disabled(!$isTahunAjaranActive || !$activeSemester)
                        ->label('Dokumen Mutasi')
                        ->disk('local') // Gunakan 'local' untuk akses privat
                        ->visibility('private') // Tambahan, meski tidak terlalu berpengaruh di 'local'
                        ->acceptedFileTypes(['application/pdf'])
                        ->directory('dokumen_mutasi')
                        ->validationMessages([
                            'acceptedFileTypes' => 'Dokumen harus berupa file PDF.',
                        ]),
                        Forms\Components\DatePicker::make('tanggal_mutasi')
                            ->label('Tanggal Mutasi')
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->disabled(!$isTahunAjaranActive || !$activeSemester)
                            ->validationMessages([
                                'required' => 'Tanggal Mutasi tidak boleh kosong',
                                ])->columnSpanFull(),
                        Forms\Components\Textarea::make('keterangan')
                            ->rows(10)
                            ->cols(20)
                            ->disabled(!$isTahunAjaranActive || !$activeSemester)
                            ->columnSpanFull(),
                    ])->columnSpan(1)
                
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        $activeTahunAjaran = cache()->remember('active_th_ajaran', now()->addMinutes(1), fn () => \App\Models\TahunAjaran::where('status', true)->first());
        $activeSemester = cache()->remember('active_semester', now()->addMinutes(1), fn () => \App\Models\Semester::where('status', true)->first());
        
        // Tampilkan notifikasi jika tidak ada tahun ajaran aktif
        if (!$activeTahunAjaran) {
            Notification::make()
                ->title('Peringatan')
                ->body('Tidak ada tahun ajaran yang aktif. Silakan aktifkan tahun ajaran terlebih dahulu.')
                ->warning()
                ->persistent()
                ->send();
        }

        if (!$activeSemester) {
            Notification::make()
                ->title('Peringatan')
                ->body('Tidak ada semester yang aktif. Silakan aktifkan semester terlebih dahulu.')
                ->warning()
                ->persistent()
                ->send();
        }
        return $table
        ->modifyQueryUsing(function (Builder $query) {
            return $query->orderBy('tanggal_mutasi', 'desc');
        })
            ->recordAction(null)
            ->recordUrl(null)
            ->extremePaginationLinks()
            ->paginated([5, 10, 20, 50])
            ->defaultPaginationPageOption(10)
            ->striped()
            ->poll('5s')
            ->recordClasses(function () {
                $classes = 'table-vertical-align-top ';
                return $classes;
            })
            ->columns([
                Tables\Columns\TextColumn::make('dataSiswa.nama_siswa')
                    ->sortable()
                    ->searchable()
                    ->label('Nama Siswa'),
                Tables\Columns\TextColumn::make('tahunAjaran.th_ajaran')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Tahun Ajaran'),
                Tables\Columns\TextColumn::make('semester.nm_semester')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Semester'),
                    Tables\Columns\TextColumn::make('tipe_mutasi')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->formatStateUsing(fn ($state) => ucfirst($state)) // untuk kapitalisasi
                    ->color(fn ($state) => match (strtolower($state)) {
                        'masuk' => 'success',
                        'keluar' => 'danger',
                        default => 'secondary', // fallback untuk nilai tak dikenal
                    })
                    ->label('Tipe Mutasi'),
                Tables\Columns\TextColumn::make('tanggal_mutasi')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('kelas.nama_kelas')
                    ->sortable()
                    ->searchable()
                    ->label('Kelas'),
                Tables\Columns\TextColumn::make('info_mutasi')
                    ->label('Info Mutasi')
                    ->searchable()
                    ->sortable(),
                // Tables\Columns\TextColumn::make('dokumen_mutasi')
                //     ->searchable(),
                Tables\Columns\IconColumn::make('dokumen_mutasi')
                ->label('Dokumen Mutasi')
                ->icon(fn ($record) => filled($record->dokumen_mutasi) ? 'heroicon-o-document-text' : 'heroicon-o-x-circle')
                ->color(fn ($record) => filled($record->dokumen_mutasi) ? 'primary' : 'gray')
                ->tooltip(fn ($record) => filled($record->dokumen_mutasi) ? 'Lihat Dokumen' : 'Tidak Ada Dokumen')
                ->url(fn ($record) => filled($record->dokumen_mutasi) ? route('siswa.dokumen_mutasi', $record->id) : null)
                ->openUrlInNewTab(fn ($record) => filled($record->dokumen_mutasi)),
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
            SelectFilter::make('tahun_ajaran_id')
                ->label('Tahun Ajaran')
                ->relationship('tahunAjaran', 'th_ajaran')
                ->searchable()
                ->preload()
                ->default($activeTahunAjaran ? $activeTahunAjaran->id : null),
            SelectFilter::make('semester_id')
                ->label('Semester')
                ->relationship('semester', 'nm_semester')
                ->searchable()
                ->preload()
                ->default($activeSemester ? $activeSemester->id : null),
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
            'index' => Pages\ListMutasiSiswas::route('/'),
            'create' => Pages\CreateMutasiSiswa::route('/create'),
            'view' => Pages\ViewMutasiSiswa::route('/{record}'),
            'edit' => Pages\EditMutasiSiswa::route('/{record}/edit'),
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
