<?php

namespace App\Filament\Admin\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\DataSiswa;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\HtmlString;
use Filament\Tables\Filters\Filter;
use App\Imports\SiswaImportProcessor;
use Filament\Forms\Components\Section;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\Layout\Grid;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use EightyNine\ExcelImport\ExcelImportAction;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Admin\Resources\DataSiswaResource\Pages;
use App\Filament\Admin\Resources\DataSiswaResource\RelationManagers;

class DataSiswaResource extends Resource
{
    protected static ?string $model = DataSiswa::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Data Siswa';

    protected static ?string $modelLabel = 'Data Siswa';

    protected static ?string $pluralModelLabel = 'Data Siswa';

    protected static ?string $slug = 'data-siswa';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                    Section::make()
                        ->schema([
                            Forms\Components\TextInput::make('nama_siswa')
                    ->required()
                    ->maxLength(100)
                    ->validationMessages([
                        'required' => 'Nama siswa wajib diisi',
                    ])
                    ->columnSpan('full'),
                Forms\Components\TextInput::make('nis')
                    ->required()
                    ->maxLength(20),
                Forms\Components\TextInput::make('nisn')
                    ->required()
                    ->maxLength(20),
                Forms\Components\TextInput::make('no_hp')
                    ->maxLength(15),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->maxLength(100),
                Forms\Components\TextInput::make('agama')
                    ->maxLength(50),
                Forms\Components\TextInput::make('jenis_kelamin')
                    ->required(),
                Forms\Components\TextInput::make('tempat_lahir')
                    ->required()
                    ->maxLength(50),
                Forms\Components\DatePicker::make('tanggal_lahir')
                    ->required(),
                Forms\Components\TextInput::make('negara')
                    ->maxLength(50),
                Forms\Components\TextInput::make('provinsi')
                    ->maxLength(50),
                Forms\Components\TextInput::make('kabupaten')
                    ->maxLength(50),
                Forms\Components\TextInput::make('kecamatan')
                    ->maxLength(50),
                Forms\Components\TextInput::make('kelurahan')
                    ->maxLength(50),
                Forms\Components\TextInput::make('alamat')
                    ->maxLength(100),
                Forms\Components\TextInput::make('rt')
                    ->maxLength(10),
                Forms\Components\TextInput::make('rw')
                    ->maxLength(10),
                Forms\Components\TextInput::make('kode_pos')
                    ->maxLength(10),
                Forms\Components\TextInput::make('yatim_piatu'),
                Forms\Components\TextInput::make('penyakit')
                    ->maxLength(100),
                Forms\Components\TextInput::make('jumlah_saudara')
                    ->maxLength(10),
                Forms\Components\TextInput::make('anak_ke')
                    ->maxLength(10),
                Forms\Components\TextInput::make('dari_bersaudara')
                    ->maxLength(10),
                Forms\Components\TextInput::make('jarak_tempuh_id')
                    ->numeric(),
                Forms\Components\TextInput::make('transport_id')
                    ->numeric(),
                Forms\Components\TextInput::make('angkatan')
                    ->required()
                    ->maxLength(50),
                Forms\Components\DatePicker::make('tanggal_masuk'),
                Forms\Components\DatePicker::make('tanggal_keluar'),
                Forms\Components\TextInput::make('lanjut_sma_dimana')
                    ->maxLength(255),
                Forms\Components\TextInput::make('status_siswa_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('nm_ayah')
                    ->maxLength(100),
                Forms\Components\TextInput::make('pendidikan_ayah_id')
                    ->numeric(),
                Forms\Components\TextInput::make('pekerjaan_ayah_id')
                    ->numeric(),
                Forms\Components\TextInput::make('penghasilan_ayah_id')
                    ->numeric(),
                Forms\Components\TextInput::make('no_hp_ayah')
                    ->maxLength(15),
                Forms\Components\TextInput::make('nm_ibu')
                    ->maxLength(100),
                Forms\Components\TextInput::make('pendidikan_ibu_id')
                    ->numeric(),
                Forms\Components\TextInput::make('pekerjaan_ibu_id')
                    ->numeric(),
                Forms\Components\TextInput::make('penghasilan_ibu_id')
                    ->numeric(),
                Forms\Components\TextInput::make('no_hp_ibu')
                    ->maxLength(15),
                Forms\Components\TextInput::make('nm_wali')
                    ->maxLength(100),
                Forms\Components\TextInput::make('pendidikan_wali_id')
                    ->numeric(),
                Forms\Components\TextInput::make('pekerjaan_wali_id')
                    ->numeric(),
                Forms\Components\TextInput::make('penghasilan_wali_id')
                    ->numeric(),
                Forms\Components\TextInput::make('no_hp_wali')
                    ->maxLength(15),
                Forms\Components\TextInput::make('user_id')
                    ->numeric(),
                        ])->columnSpan(2)->columns(2),
                Section::make()
                    ->schema([
                        Forms\Components\FileUpload::make('foto_siswa')
                            ->label('Foto Siswa')
                            ->disk('public')
                            ->directory('siswa')
                            ->acceptedFileTypes(['image/jpeg', 'image/png']),
                        Forms\Components\FileUpload::make('upload_ijazah_sd')
                            ->label('Ijazah SD')
                            ->disk('public')
                            ->required()
                            ->directory('ijazah_sd')
                            ->acceptedFileTypes(['application/pdf'])
                            ->validationMessages([
                                'required' => 'Ijazah SD wajib diisi',
                            ])
                    ])->columnSpan(1)->columns(1),
            ])->columns(3);
    }
    public static function table(Table $table): Table
    {
        return $table
        ->extremePaginationLinks()
        ->recordUrl(null)
        ->paginated([5, 10, 20, 50])
        ->defaultPaginationPageOption(10)
        ->striped()
        ->recordAction(null)
        ->recordClasses(function () {
            $classes = 'table-vertical-align-top ';
            return $classes;
        })
            ->columns([
        Tables\Columns\Layout\Panel::make([       
            Tables\Columns\Layout\Split::make([
                    Tables\Columns\Layout\Split::make([
                        Tables\Columns\ImageColumn::make('foto_siswa')
                            ->simpleLightbox()
                            ->label('Foto')
                            ->circular()
                            ->size(100)
                            ->grow(false)
                            ->defaultImageUrl(asset('images/no_pic.jpg')),
                            Tables\Columns\Layout\Split::make([
                                Tables\Columns\Layout\Stack::make([
                                    Tables\Columns\TextColumn::make('nama_siswa')
                                        ->searchable()
                                        ->sortable()
                                        ->weight(FontWeight::Bold),
                                    Tables\Columns\TextColumn::make('virtual_account')
                                        ->searchable()
                                        ->formatStateUsing(fn (?string $state): HtmlString => new HtmlString("<small>VA: " . ($state ?? '-') . "</small>")),
                                    Tables\Columns\TextColumn::make('nis')
                                        ->searchable()
                                        ->formatStateUsing(fn (?string $state): HtmlString => new HtmlString("<small>NIS: " . ($state ?? '-') . "</small>")),
                                    Tables\Columns\TextColumn::make('nisn')
                                        ->searchable()
                                        ->formatStateUsing(fn (?string $state): HtmlString => new HtmlString("<small>NISN: " . ($state ?? '-') . "</small>")),
                                    Tables\Columns\TextColumn::make('nik')
                                        ->searchable()
                                        ->formatStateUsing(fn (?string $state): HtmlString => new HtmlString("<small>NIK: " . ($state ?? '-') . "</small>")),
                                    Tables\Columns\Layout\Split::make([
                                    Tables\Columns\TextColumn::make('statussiswa.status')
                                        ->searchable()
                                        ->sortable()
                                        ->badge()
                                        ->alignLeft()
                                        ->color(fn ($state) => match ($state) {
                                            'Aktif' => 'success',
                                            'Pindah' => 'warning',
                                            'Lulus' => 'info',
                                            'Cuti' => 'gray',
                                            'Drop Out' => 'danger',
                                            default => 'secondary',
                                        })
                                        ->formatStateUsing(fn (?string $state): HtmlString => new HtmlString("<small>" . ($state ?? '-') . "</small>")),
                                        Tables\Columns\TextColumn::make('yatim_piatu')
                                            ->searchable()
                                            ->badge()
                                            ->alignLeft()
                                            ->color(fn ($state) => match ($state) {
                                                'Yatim' => 'info',
                                                'Piatu' => 'info',
                                                'Yatim Piatu' => 'warning',
                                                'Bukan Yatim' => 'success',
                                                default => 'secondary',
                                            })
                                            ->formatStateUsing(fn (?string $state): HtmlString => new HtmlString("<small>Yatim: " . ($state ?? '-') . "</small>")),
                                        Tables\Columns\TextColumn::make('angkatan')
                                            ->searchable()
                                            ->badge()
                                            ->alignLeft()
                                            ->color('primary')
                                            ->formatStateUsing(fn (?string $state): HtmlString => new HtmlString("<small>Angkatan: " . ($state ?? '-') . "</small>")),
                                            ])->grow(false),
                                ])
                            ]),
                        ]),
                    Tables\Columns\Layout\Split::make([
                        Tables\Columns\Layout\Stack::make([
                            Tables\Columns\TextColumn::make('alamat_lengkap')
                            ->label('Alamat')
                            ->searchable(query: fn ($query, $search) => $query->where(function ($query) use ($search) {
                                $query->where('alamat', 'like', "%{$search}%")
                                      ->orWhere('rt', 'like', "%{$search}%")
                                      ->orWhere('rw', 'like', "%{$search}%")
                                      ->orWhere('kelurahan', 'like', "%{$search}%")
                                      ->orWhere('kecamatan', 'like', "%{$search}%")
                                      ->orWhere('kabupaten', 'like', "%{$search}%")
                                      ->orWhere('provinsi', 'like', "%{$search}%")
                                      ->orWhere('kode_pos', 'like', "%{$search}%");
                            }))
                            ->description('Alamat:', position: 'above'),
                        Tables\Columns\TextColumn::make('email')
                            ->searchable()
                            ->icon('heroicon-m-envelope')
                            ->formatStateUsing(fn (?string $state): string => $state ?? '-'),
                        Tables\Columns\TextColumn::make('no_hp')
                            ->searchable()
                            ->icon('heroicon-m-phone')
                            ->formatStateUsing(fn (?string $state): string => $state ?? '-'),
                        ])
                    ])->visibleFrom('md'),
                 ])
                    
                ])                   
                            
                ])

            ->filters([
                    SelectFilter::make('status')
                        ->relationship('statussiswa', 'status')
                        ->searchable()
                        ->preload()
                        ->label('Status siswa'),
                    SelectFilter::make('angkatan')
                        ->options(fn () => \App\Models\DataSiswa::query()
                            ->select('angkatan')
                            ->distinct()
                            ->pluck('angkatan', 'angkatan')
                            ->toArray())
                        ->searchable()
                        ->preload()
                        ->label('Angkatan'),
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
                ->icon('heroicon-m-trash'),
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
            'index' => Pages\ListDataSiswas::route('/'),
            'create' => Pages\CreateDataSiswa::route('/create'),
            'view' => Pages\ViewDataSiswa::route('/{record}'),
            'edit' => Pages\EditDataSiswa::route('/{record}/edit'),
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
