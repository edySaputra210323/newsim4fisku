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

    protected static ?string $navigationLabel = 'Riwayat Kelas';

    protected static ?string $modelLabel = 'Riwayat Kelas';

    protected static ?string $pluralModelLabel = 'Riwayat Kelas';

    protected static ?string $slug = 'riwayat-kelas';

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
                Section::make()
                ->schema([
                    Forms\Components\TextInput::make('kelas_id')
                    ->label('Pilih Kelas')
                    ->disabled(!$isTahunAjaranActive || !$activeSemester),
                    Forms\Components\TextInput::make('pegawai_id')
                    ->label('Pilih Wali Kelas')
                    ->disabled(!$isTahunAjaranActive || !$activeSemester),
                ])->columnSpan(1),
                Section::make()
                ->schema([
                    Forms\Components\Select::make('data_siswa_id')
                ->label('Data Siswa')
                ->searchable()
                ->getSearchResultsUsing(function (string $search) {
                    return \App\Models\DataSiswa::where('nama_siswa', 'like', "%{$search}%")
                        ->orWhere('nis', 'like', "%{$search}%")
                        ->limit(50)
                        ->get()
                        ->mapWithKeys(fn ($siswa) => [$siswa->id => "{$siswa->nama_siswa} - {$siswa->nis}"]);
                })
                ->getOptionLabelUsing(fn ($value): ?string => \App\Models\DataSiswa::find($value)?->nama_siswa . ' - ' . \App\Models\DataSiswa::find($value)?->nis)
                ->preload()
                ->placeholder('PILIH DATA SISWA')
                ->disabled(!$isTahunAjaranActive || !$activeSemester),
                Forms\Components\Select::make('status_id')
                ->label('Status')
                ->disabled(!$isTahunAjaranActive || !$activeSemester),
                ])->columnSpan(2),
            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('data_siswa_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('kelas_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pegawai_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tahun_ajaran_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('semester_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status_id')
                    ->numeric()
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
