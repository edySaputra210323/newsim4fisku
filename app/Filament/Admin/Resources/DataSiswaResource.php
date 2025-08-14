<?php

namespace App\Filament\Admin\Resources;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\DataSiswa;
use App\Models\Transport;
use Filament\Tables\Table;
use App\Models\JarakTempuh;
use App\Models\StatusSiswa;
use App\Models\PekerjaanOrtu;
use App\Models\PendidikanOrtu;
use App\Models\PenghasilanOrtu;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Grid;
use Filament\Tables\Filters\Filter;
use App\Imports\SiswaImportProcessor;
use Filament\Support\Enums\FontWeight;
use Illuminate\Support\Facades\Storage;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\Fieldset;
use EightyNine\ExcelImport\ExcelImportAction;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Admin\Resources\DataSiswaResource\Pages;
use Joaopaulolndev\FilamentPdfViewer\Facades\FilamentPdfViewer;
use App\Filament\Admin\Resources\DataSiswaResource\RelationManagers;
use Joaopaulolndev\FilamentPdfViewer\Infolists\Components\PdfViewerEntry;
use Filament\Tables\Components\Group as Group; // Alias untuk Group di Tables
use Filament\Tables\Components\Split as Split; // Alias untuk Split di Tables
use Filament\Tables\Components\Grid as TableGrid; // Alias untuk Grid di Tables
use Joaopaulolndev\FilamentPdfViewer\Infolists\Components\FilamentPdfViewerEntry;
use Filament\Forms\Components\Section as FormSection; // Alias untuk Section di Form
use Filament\Infolists\Components\Grid as InfolistGrid; // Alias untuk Grid di Infolist
use Filament\Tables\Components\TextEntry as TextEntry; // Alias untuk TextEntry di Tables
use Filament\Infolists\Components\Group as InfolistGroup; // Alias untuk Group di Infolist
use Filament\Infolists\Components\Split as InfolistSplit; // Alias untuk Split di Infolist
use Filament\Tables\Components\ImageEntry as ImageEntry; // Alias untuk ImageEntry di Tables
use Filament\Infolists\Components\Section as InfolistSection; // Alias untuk Section di Infolist
use Filament\Infolists\Components\TextEntry as InfolistTextEntry; // Alias untuk TextEntry di Infolist
use Filament\Infolists\Components\ImageEntry as InfolistImageEntry; // Alias untuk ImageEntry di Infolist

class DataSiswaResource extends Resource
{
    protected static ?string $model = DataSiswa::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Siswa';

    protected static ?string $navigationLabel = 'Data Siswa';

    protected static ?string $modelLabel = 'Data Siswa';

    protected static ?string $pluralModelLabel = 'Data Siswa';

    protected static ?string $slug = 'data-siswa';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                FormSection::make('Data Siswa')
                    ->description('bio data siswa sesuai kartu identitas atau kk atau surat lahir')
                    ->icon('heroicon-m-user-group')
                    ->schema([
                        Forms\Components\TextInput::make('nama_siswa')
                            ->required()
                            ->maxLength(100)
                            ->validationMessages([
                                'required' => 'Nama siswa wajib diisi',
                            ])
                            ->columnSpan('full'),
                        Forms\Components\TextInput::make('nis')
                            ->label('NIS')
                            ->placeholder('Nomor Induk Siswa')
                            ->numeric()
                            ->unique(table: DataSiswa::class, ignoreRecord: true)
                            ->required()
                            ->maxLength(7)
                            ->minLength(7)
                            ->extraInputAttributes([
                                'oninput' => "this.value = this.value.replace(/[^0-9]/g, '')",
                            ])
                            ->validationMessages([
                                'required' => 'NIS tidak boleh kosong',
                                'max' => 'NIS tidak boleh lebih dari 7 angka',
                                'numeric' => 'NIS harus angka',
                                'unique' => 'NIS sudah ada',
                            ]),
                        Forms\Components\TextInput::make('nisn')
                            ->label('NISN')
                            ->placeholder('Nomor Induk Siswa Nasional')
                            ->numeric()
                            ->unique(table: DataSiswa::class, ignoreRecord: true)
                            ->required()
                            ->maxLength(10)
                            ->minLength(10)
                            ->extraInputAttributes([
                                'oninput' => "this.value = this.value.replace(/[^0-9]/g, '')",
                            ])
                            ->validationMessages([
                                'required' => 'NISN tidak boleh kosong',
                                'max' => 'NISN tidak boleh lebih dari 10 angka',
                                'numeric' => 'NISN harus angka',
                                'unique' => 'NISN sudah ada',
                            ]),
                        Forms\Components\TextInput::make('no_hp')
                            ->label('Nomor Telepon')
                            ->maxLength(15)
                            ->numeric()
                            ->required()
                            ->maxLength(15)
                            ->minLength(10)
                            ->validationMessages([
                                'required' => 'Nomor telepon wajib diisi',
                                'max' => 'Nomor telepon tidak boleh lebih dari 15 angka',
                                'numeric' => 'Nomor telepon harus angka',
                                'min' => 'Nomor telepon tidak boleh kurang dari 10 angka',
                            ]),
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(100)
                            ->validationMessages([
                                'required' => 'Email wajib diisi',
                                'max' => 'Email tidak boleh lebih dari 100 karakter',
                                'email' => 'Email tidak valid',
                            ]),
                        Forms\Components\TextInput::make('agama')
                            ->label('Agama')
                            ->maxLength(50),
                        Forms\Components\Select::make('jenis_kelamin')
                            ->options(['L' => 'Laki-laki', 'P' => 'Perempuan'])
                            ->label('Jenis Kelamin')
                            ->required()
                            ->native(false)
                            ->validationMessages([
                                'required' => 'Jenis kelamin wajib dipilih',
                            ]),
                        Forms\Components\TextInput::make('tempat_lahir')
                            ->label('Tempat Lahir')
                            ->required()
                            ->maxLength(50)
                            ->validationMessages([
                                'required' => 'Tempat lahir wajib diisi',
                                'max' => 'Tempat lahir tidak boleh lebih dari 50 karakter',
                            ]),
                        Forms\Components\DatePicker::make('tanggal_lahir')
                            ->label('Tanggal Lahir')
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->validationMessages([
                                'required' => 'Tanggal Lahir tidak boleh kosong',
                            ]),
                        Forms\Components\Section::make('Alamat')
                            ->description('Alamat sesuai kartu identitas atau kk')
                            ->icon('heroicon-m-map-pin')
                            ->schema([
                                Grid::make(3)
                                    ->schema([
                                        Forms\Components\TextInput::make('negara')
                                            ->maxLength(50),
                                        Forms\Components\TextInput::make('provinsi')
                                            ->maxLength(50),
                                        Forms\Components\TextInput::make('kabupaten')
                                            ->maxLength(50),
                                    ]),
                                Forms\Components\TextInput::make('kecamatan')
                                    ->maxLength(50),
                                Forms\Components\TextInput::make('kelurahan')
                                    ->maxLength(50),
                                Forms\Components\TextInput::make('alamat')
                                    ->maxLength(100),
                                Grid::make(3)
                                    ->schema([
                                        Forms\Components\TextInput::make('rt')
                                            ->maxLength(10),
                                        Forms\Components\TextInput::make('rw')
                                            ->maxLength(10),
                                        Forms\Components\TextInput::make('kode_pos')
                                            ->maxLength(10),
                                    ]),
                            ]),
                        Forms\Components\Section::make('Data Pelengkap')
                            ->schema([
                                Forms\Components\Select::make('yatim_piatu')
                                    ->native(false)
                                    ->placeholder('kosongkan jika bukan yatim atau piatu')
                                    ->options([
                                        'Yatim' => 'Yatim',
                                        'Piatu' => 'Piatu',
                                        'Yatim Piatu' => 'Yatim Piatu'
                                    ])
                                    ->label('Yatim Piatu'),
                                Forms\Components\TextInput::make('penyakit')
                                    ->maxLength(100),
                                
                                Grid::make(3)
                                    ->schema([
                            Forms\Components\TextInput::make('jumlah_saudara')
                                ->maxLength(10)
                                ->numeric(),
                            Forms\Components\TextInput::make('anak_ke')
                                ->maxLength(10)
                                ->numeric(),
                            Forms\Components\TextInput::make('dari_bersaudara')
                                ->maxLength(10)
                                ->numeric(),
                            ]),
                        Forms\Components\Select::make('jarak_tempuh_id')
                            ->label('Jarak Tempuh')
                            ->placeholder('Pilih Jarak Tempuh')
                            ->options(JarakTempuh::orderBy('nama_jarak_tempuh')->get()->pluck('nama_jarak_tempuh', 'id')),
                        Forms\Components\Select::make('transport_id')
                            ->label('Transport')
                            ->placeholder('Pilih Transport')
                            ->options(Transport::orderBy('nama_transport')->get()->pluck('nama_transport', 'id')),
                        Forms\Components\TextInput::make('angkatan')
                            ->required()
                            ->placeholder('Penulisan angkatan menggunakan huruf romawi misalnya : XII')
                            ->maxLength(4)
                            ->validationMessages([
                                'required' => 'Angkatan wajib diisi',
                                'max' => 'Angkatan maksimal 4 karakter',
                            ]),
                        Forms\Components\DatePicker::make('tanggal_masuk')
                            ->label('Tanggal Masuk')
                            ->native(false)
                            ->displayFormat('d/m/Y'),
                        Forms\Components\TextInput::make('lanjut_sma_dimana')
                            ->maxLength(255),
            ]),
                Forms\Components\Section::make('Data Ayah')
                ->schema([
                    Forms\Components\TextInput::make('nm_ayah')
                    ->maxLength(100),
                    Forms\Components\TextInput::make('no_hp_ayah')
                    ->maxLength(15),
                    Grid::make(3)
                    ->schema([
                        Forms\Components\Select::make('pendidikan_ayah_id')
                            ->options(PendidikanOrtu::orderBy('jenjang_pendidikan')->get()->pluck('jenjang_pendidikan', 'id'))
                            ->native(false)
                            ->label('Pendidikan Ayah'),
                        Forms\Components\Select::make('pekerjaan_ayah_id')
                            ->options(PekerjaanOrtu::orderBy('nama_pekerjaan')->get()->pluck('nama_pekerjaan', 'id'))
                            ->native(false)
                            ->label('Pekerjaan Ayah'),
                        Forms\Components\Select::make('penghasilan_ayah_id')
                            ->options(PenghasilanOrtu::orderBy('penghasilan')->get()->pluck('penghasilan', 'id'))
                            ->native(false)
                            ->label('Penghasilan Ayah'),
                    ])
                ]),

                Forms\Components\Section::make('Data Ibu')
                ->schema([
                    Forms\Components\TextInput::make('nm_ibu')
                    ->maxLength(100),
                    Forms\Components\TextInput::make('no_hp_ibu')
                    ->maxLength(15),
                    Grid::make(3)
                    ->schema([
                        Forms\Components\Select::make('pendidikan_ibu_id')
                            ->options(PendidikanOrtu::orderBy('jenjang_pendidikan')->get()->pluck('jenjang_pendidikan', 'id'))
                            ->native(false)
                            ->label('Pendidikan Ibu'),
                        Forms\Components\Select::make('pekerjaan_ibu_id')
                            ->options(PekerjaanOrtu::orderBy('nama_pekerjaan')->get()->pluck('nama_pekerjaan', 'id'))
                            ->native(false)
                            ->label('Pekerjaan Ibu'),
                        Forms\Components\Select::make('penghasilan_ibu_id')
                            ->options(PenghasilanOrtu::orderBy('penghasilan')->get()->pluck('penghasilan', 'id'))
                            ->native(false)
                            ->label('Penghasilan Ibu'),
                    ])
                ]),
                Forms\Components\Section::make('Data Wali')
                ->schema([
                    Forms\Components\TextInput::make('nm_wali')
                    ->maxLength(100),
                    Forms\Components\TextInput::make('no_hp_wali')
                    ->maxLength(15),
                    Grid::make(3)
                    ->schema([
                        Forms\Components\Select::make('pendidikan_wali_id')
                            ->options(PendidikanOrtu::orderBy('jenjang_pendidikan')->get()->pluck('jenjang_pendidikan', 'id'))
                            ->native(false)
                            ->label('Pendidikan Wali'),
                        Forms\Components\Select::make('pekerjaan_wali_id')
                            ->options(PekerjaanOrtu::orderBy('nama_pekerjaan')->get()->pluck('nama_pekerjaan', 'id'))
                            ->native(false)
                            ->label('Pekerjaan Wali'),
                        Forms\Components\Select::make('penghasilan_wali_id')
                            ->options(PenghasilanOrtu::orderBy('penghasilan')->get()->pluck('penghasilan', 'id'))
                            ->native(false)
                            ->label('Penghasilan Wali'),
                    ])
                ]),
            ])->columnSpan(2)->columns(2),
            FormSection::make()
            ->schema([
                Forms\Components\FileUpload::make('foto_siswa')
                        ->image(['jpg', 'png'])
                        ->label('Foto Siswa')
                        ->disk('public')
                        ->minSize(20)
                        ->maxSize(2048)
                        ->openable()
                        ->directory('siswa')
                        ->removeUploadedFileButtonPosition('right')
                        ->visibility('public')
                        ->acceptedFileTypes(['image/jpeg', 'image/png'])
                        ->default(function ($record) {
                            return $record?->foto_siswa; // Ambil nilai dari model
                        })
                        ->afterStateUpdated(function ($state, $record, callable $set, callable $get) {
                            if ($state && $record && $record->foto_siswa) {
                                Storage::disk('public')->delete($record->foto_siswa);
                            }
                        })
                        ->deleteUploadedFileUsing(function ($record) {
                            if ($record && $record->foto_siswa) {
                                Storage::disk('public')->delete($record->foto_siswa);
                                $record->foto_siswa = null;
                                $record->save();
                            }
                        })
                        ->validationMessages([
                            'file' => 'File harus berupa gambar.',
                            'min' => 'Ukuran gambar terlalu kecil (minimal 1 MB).',
                            'max' => 'Ukuran gambar terlalu besar (maksimal 2 MB).',
                            'mimes' => 'Hanya file JPG atau PNG yang diperbolehkan.',
                            'accepted' => 'Jenis file tidak didukung. Harus JPG atau PNG.',
                        ]),
                    Forms\Components\FileUpload::make('upload_ijazah_sd')
                        ->label('Ijazah SD')
                        ->disk('public')
                        ->visibility('private')
                        ->acceptedFileTypes(['application/pdf'])
                        ->directory('ijazah_sd')
                        ->validationMessages([
                            'acceptedFileTypes' => 'Wajib PDF',
                        ]),
                        Forms\Components\Select::make('status_id')
                            ->label('Status Siswa')
                            ->required()
                            ->reactive() // penting agar bisa trigger perubahan field lain
                            ->placeholder('Pilih Status Siswa')
                            ->options(StatusSiswa::orderBy('status')->get()->pluck('status', 'id'))
                            ->native(false)
                            ->validationMessages([
                                'required' => 'Status Siswa wajib dipilih',
                            ]),
                        Forms\Components\DatePicker::make('tanggal_keluar')
                            ->label('Tanggal Keluar')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->visible(function ($get) {
                                $statusId = $get('status_id');
                                $status = StatusSiswa::find($statusId)?->status;
                                return in_array($status, ['Drop Out', 'Pindah', 'Cuti']);
                            }),
                        FormSection::make()
                        ->collapsible()
                        ->collapsed()
                        ->label('Dokumen Pendukung dan Status')
                        ->description('apabila status siswa aktif, maka dokumen pendukung tidak perlu diisi, apabila status siswa drop out, pindah atau cuti, maka dokumen pendukung perlu diisi')
                        ->schema([
                        Forms\Components\FileUpload::make('dokumen_pendukung')
                            ->label('Dokumen Pendukung')
                            ->disk('public')
                            ->visibility('private')
                            ->acceptedFileTypes(['application/pdf'])
                            ->directory('dokumen_pendukung')
                            ->validationMessages([
                                'acceptedFileTypes' => 'Wajib PDF',
                            ])
                        ])
                    ])->columnSpan(1),
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
                                    Tables\Columns\TextColumn::make('UpdateStatusSiswa.status')
                                        ->searchable()
                                        ->sortable()
                                        ->badge()
                                        ->alignLeft()
                                        ->color(fn ($state) => match ($state) {
                                            'Aktif' => 'success',
                                            'Lulus' => 'warning',
                                            default => 'danger',
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
                        ->relationship('UpdateStatusSiswa', 'status')
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
                    SelectFilter::make('yatim_piatu')
                    ->options([
                        'Yatim' => 'Yatim',
                        'Piatu' => 'Piatu',
                        'Yatim Piatu' => 'Yatim Piatu',
                    ])
                    ->label('Yatim Piatu')
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
                ->icon('heroicon-m-trash'),
                ])
                ->bulkActions([
                    Tables\Actions\BulkAction::make('export_qrcode_pdf')
                    ->label('Cetak ID Card PDF')
                    ->icon('heroicon-o-qr-code')
                    ->action(function ($records) {
                        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('siswa.qr_bulk_pdf', ['records' => $records])
                        ->setPaper('a4', 'portrait')
                        ->setOption('isRemoteEnabled', true)
                        ->setOption('isHtml5ParserEnabled', true)
                        ->setOption('isPhpEnabled', true);
                        return response()->streamDownload(function () use ($pdf) {
                            echo $pdf->output();
                        }, 'label-siswa-qr.pdf');
                    }),
                    Tables\Actions\BulkAction::make('export_qrcode_pdf_back')
                    ->label('Cetak ID Card PDF (Belakang)')
                    ->icon('heroicon-o-qr-code')
                    ->action(function ($records) {
                        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
                            'siswa.qr_bulk_pdf_back',
                            ['records' => $records]
            )
            ->setPaper('a4', 'portrait')
            ->setOption('isRemoteEnabled', true)
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isPhpEnabled', true);

            return response()->streamDownload(function () use ($pdf) {
                echo $pdf->output();
            }, 'idcard-belakang.pdf');
        }),
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

            return parent::getEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class])
            ->join('data_siswa', 'riwayat_kelas.data_siswa_id', '=', 'data_siswa.id')
            ->join('status_siswa', 'data_siswa.status_id', '=', 'status_siswa.id')
            ->where('status_siswa.status', 'aktif')
            ->with([
                'dataSiswa.pekerjaanAyah',
                'dataSiswa.pendidikanAyah',
                'dataSiswa.penghasilanAyah',
                'kelas',
                'pegawai',
                'tahunAjaran',
                'semester'
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
{
    return $infolist
        ->schema([
            InfolistSection::make()
                    ->schema([
                        InfolistSplit::make([
                            InfolistGrid::make(2)
                                ->schema([
                                    InfolistGroup::make([
                                            InfolistTextEntry::make('nama_siswa')
                                            ->label('Nama Siswa')
                                            ->weight(FontWeight::Bold)
                                            ->color('gray'),
                                        InfolistGrid::make([
                                            'md' => 3,
                                        ])
                                        ->schema([
                                            InfolistTextEntry::make('virtual_account')
                                            ->label('VA')
                                            ->badge()
                                            ->color('gray'),
                                            InfolistTextEntry::make('UpdateStatusSiswa.status')
                                            ->label('Status')
                                            ->badge()
                                            ->color(fn ($state) => match ($state) {
                                                'Aktif' => 'success',
                                                'Lulus' => 'warning',
                                                default => 'danger',
                                            }),
                                        ]),
                                        InfolistTextEntry::make('tempat_tanggal_lahir')
                                            ->label('Tempat Tanggal Lahir')
                                            ->color('gray'),
                                        InfolistTextEntry::make('jenis_kelamin')
                                            ->label('Jenis Kelamin')
                                            ->formatStateUsing(function (string $state): string {
                                                return match ($state) {
                                                    'L' => 'Laki-laki',
                                                    'P' => 'Perempuan',
                                                    default => 'Tidak Diketahui',
                                                };
                                            })
                                            ->color('gray'),
                                            Fieldset::make('Nomor Identitas')
                                            ->schema([
                                                InfolistTextEntry::make('nis')
                                            ->label('NIS')
                                            ->color('gray'),
                                        InfolistTextEntry::make('nisn')
                                            ->label('NISN')
                                            ->color('gray'),
                                        InfolistTextEntry::make('nik')
                                            ->label('NIK')
                                            ->color('gray'),
                                            ])->columns(3),
                                    ]),
                                    InfolistGroup::make([
                                        InfolistTextEntry::make('alamat_lengkap')
                                            ->label('Alamat')
                                            ->color('gray')
                                            ->icon('heroicon-m-map-pin'),
                                            InfolistGrid::make([
                                                'md' => 3,
                                                ])
                                            ->schema([
                                                InfolistTextEntry::make('no_hp')
                                                    ->icon('heroicon-m-phone')
                                                    ->color('gray')
                                                    ->label(false),
                                                InfolistTextEntry::make('email')
                                                    ->icon('heroicon-m-envelope')
                                                    ->color('gray')
                                                    ->label(false),
                                            ]),
                                        InfolistTextEntry::make('asal_sekolah_npsn')
                                            ->color('gray'),
                                    ]),
                                ]),
                            InfolistImageEntry::make('foto_siswa')
                                ->hiddenLabel()
                                ->grow(false)
                                ->width(130)
                                ->height(160)
                        ])->from('md'),
                    ]),
                InfolistSection::make('Data Orang Tua')
                    ->schema([
                        InfolistSplit::make([
                            InfolistGroup::make([
                                InfolistTextEntry::make('nm_ayah')
                                    ->label('Data Ayah')
                                    ->color('gray')
                                    ->weight(FontWeight::Bold),
                                InfolistTextEntry::make('no_hp_ayah')
                                    ->label(false)
                                    ->color('gray')
                                    ->icon('heroicon-m-phone'),
                                InfolistTextEntry::make('pendidikanAyah.jenjang_pendidikan')
                                    ->label(false)
                                    ->color('gray')
                                    ->icon('heroicon-m-book-open'),
                                InfolistTextEntry::make('pekerjaanAyah.nama_pekerjaan')
                                    ->label(false)
                                    ->color('gray')
                                    ->icon('heroicon-m-briefcase'),
                                InfolistTextEntry::make('penghasilanAyah.penghasilan')
                                    ->label(false)
                                    ->color('gray')
                                    ->icon('heroicon-m-wallet'),
                                ]),
                                InfolistGroup::make([
                                InfolistTextEntry::make('nm_ibu')
                                    ->label('Data Ibu')
                                    ->color('gray')
                                    ->weight(FontWeight::Bold),
                                    InfolistTextEntry::make('no_hp_ibu')
                                    ->label(false)
                                    ->color('gray')
                                    ->icon('heroicon-m-phone'),
                                InfolistTextEntry::make('pendidikanIbu.jenjang_pendidikan')
                                    ->label(false)
                                    ->color('gray')
                                    ->icon('heroicon-m-book-open'),
                                InfolistTextEntry::make('pekerjaanIbu.nama_pekerjaan')
                                    ->label(false)
                                    ->color('gray')
                                    ->icon('heroicon-m-briefcase'),
                                InfolistTextEntry::make('penghasilanIbu.penghasilan')
                                    ->label(false)
                                    ->color('gray')
                                    ->icon('heroicon-m-wallet'),
                                ]),
                                InfolistGroup::make([
                                InfolistTextEntry::make('nm_wali')
                                    ->label('Data Wali')
                                    ->color('gray')
                                    ->weight(FontWeight::Bold),
                                    InfolistTextEntry::make('no_hp_wali')
                                    ->label(false)
                                    ->color('gray')
                                    ->icon('heroicon-m-phone'),
                                InfolistTextEntry::make('pendidikanWali.jenjang_pendidikan')
                                    ->label(false)
                                    ->color('gray')
                                    ->icon('heroicon-m-book-open'),
                                InfolistTextEntry::make('pekerjaanWali.nama_pekerjaan')
                                    ->label(false)
                                    ->color('gray')
                                    ->icon('heroicon-m-briefcase'),
                                InfolistTextEntry::make('penghasilanWali.penghasilan')
                                    ->label(false)
                                    ->color('gray')
                                    ->icon('heroicon-m-wallet'),
                                ])
                        ])
                        
                    ])->collapsible(),
                InfolistSection::make('Ijazah SD')
                            ->collapsible()
                            ->schema([
                                PdfViewerEntry::make('upload_ijazah_sd')
                                            ->label('View the PDF')
                                            ->minHeight('70svh'),
                                // FilamentPdfViewerEntry::make('upload_ijazah_sd')
                                //     ->label('Ijazah SD')
                                //     ->minHeight('40svh')
                                //     ->notFoundMessage('Ijazah SD tidak tersedia')
                                //     ->file(function ($record) {
                                //         if ($record && $record->upload_ijazah_sd) {
                                //             return route('siswa.ijazah', ['record' => $record->id]);
                                //         }
                                //         return null;
                                //     }),
                            ])
                    ]);
}
}
