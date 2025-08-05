<?php

namespace App\Filament\Admin\Resources;

use Dompdf\Dompdf;
use Filament\Forms;
use Filament\Tables;
use App\Models\Ruangan;
use App\Models\Suplayer;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Support\RawJs;
use App\Models\KategoriBarang;
use App\Models\SumberAnggaran;
use Filament\Resources\Resource;
use App\Models\KategoriInventaris;
use App\Models\TransaksionalInventaris;
use Filament\Tables\Columns\TextColumn;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Filament\Forms\Components\Grid as FormGrid;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section as formSection;
use App\Filament\Admin\Resources\TransaksionalInventarisResource\Pages;
use App\Filament\Admin\Resources\TransaksionalInventarisResource\RelationManagers;

class TransaksionalInventarisResource extends Resource
{
    protected static ?string $model = TransaksionalInventaris::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'Inventaris';

    protected static ?string $navigationLabel = 'Inventaris';

    protected static ?string $modelLabel = 'Inventaris';

    protected static ?string $pluralModelLabel = 'Inventaris';

    protected static ?string $slug = 'inventaris';

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
                formSection::make()
                    ->schema([
                // Forms\Components\TextInput::make('kode_inventaris')
                //     ->required()
                //     ->maxLength(255)
                //     ->disabled()
                //     ->columnSpanFull(),
                Forms\Components\TextInput::make('nama_inventaris')
                ->required()
                ->maxLength(255)
                ->columnSpanFull(),
                Forms\Components\Select::make('kondisi')
                ->options([
                    'Baru' => 'Baru',
                    'Bekas Pakai' => 'Bekas Pakai',
                ])
                ->native(false)
                ->required()
                ->columnSpanFull(),
                Forms\Components\Select::make('kategori_inventaris_id')
                    ->options(KategoriInventaris::orderBy('nama_kategori_inventaris')->get()->pluck('nama_kategori_inventaris', 'id'))
                    ->native(false)
                    ->label('Kategori Inventaris'),
                Forms\Components\Select::make('suplayer_id')
                    ->options(Suplayer::orderBy('nama_suplayer')->get()->pluck('nama_suplayer', 'id'))
                    ->native(false)
                    ->label('Suplayer'),
                Forms\Components\Select::make('kategori_barang_id')
                    ->options(KategoriBarang::orderBy('nama_kategori_barang')->get()->pluck('nama_kategori_barang', 'id'))
                    ->native(false)
                    ->label('Kategori Barang'),
                Forms\Components\Select::make('sumber_anggaran_id')
                    ->options(SumberAnggaran::orderBy('nama_sumber_anggaran')->get()->pluck('nama_sumber_anggaran', 'id'))
                    ->native(false)
                    ->label('Sumber Anggaran'),
                Forms\Components\Select::make('ruang_id')
                    ->options(Ruangan::orderBy('nama_ruangan')->get()->pluck('nama_ruangan', 'id'))
                    ->native(false)
                    ->label('Ruang'),

                Forms\Components\TextInput::make('merk_inventaris')
                    ->maxLength(255),
                Forms\Components\TextInput::make('material_bahan')
                    ->maxLength(255),
                Forms\Components\DatePicker::make('tanggal_beli')
                    ->required(),
                FormGrid::make(3)
                    ->schema([
                            // Field untuk Jumlah Beli
                            Forms\Components\TextInput::make('jumlah_beli')
                            ->label('Jumlah Beli')
                            ->required()
                            ->numeric()
                            ->disabled(fn (string $operation) => $operation === 'edit')
                            ->minValue(1)
                            ->maxValue(1000)
                            ->live(onBlur: true)
                            ->validationMessages([
                                'required' => 'Jumlah beli wajib diisi',
                                'numeric' => 'Jumlah beli harus berupa angka',
                                'min' => 'Jumlah beli minimal 1',
                                'max' => 'Jumlah beli maksimal 1000',
                            ])
                            ->afterStateUpdated(function (callable $set, callable $get) {
                                self::updateTotalPrice($set, $get);
                            })
                            ->extraInputAttributes([
                                'oninput' => "this.value = this.value.replace(/[^0-9]/g, '')",
                                'style' => 'text-align: right',
                            ]),
                        Forms\Components\TextInput::make('harga_satuan')
                            ->label('Harga Satuan')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(1000000000) // Batas maksimum Rp 1 miliar
                            ->prefix('Rp ')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->live(onBlur: true)
                            ->formatStateUsing(fn ($state) => $state ? 'Rp ' . number_format($state, 0, ',', '.') : null)
                            ->dehydrateStateUsing(fn ($state) => (int) str_replace(['.', 'Rp ', ','], '', $state ?: '0'))
                            ->validationMessages([
                                'required' => 'Harga satuan wajib diisi',
                                'numeric' => 'Harga satuan harus berupa angka',
                                'min' => 'Harga satuan tidak boleh negatif',
                                'max' => 'Harga satuan maksimal Rp 1.000.000.000',
                            ])
                            ->afterStateUpdated(function (callable $set, callable $get) {
                                self::updateTotalPrice($set, $get);
                            })
                            ->extraInputAttributes([
                                'oninput' => "this.value = this.value.replace(/[^0-9]/g, '')",
                                'style' => 'text-align: right',
                            ]),
                        Forms\Components\TextInput::make('total_harga')
                            ->label('Total Harga')
                            ->required()
                            ->numeric()
                            ->prefix('Rp ')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->readOnly()
                            ->dehydrated(true)  
                            ->formatStateUsing(fn ($state) => $state ? 'Rp ' . number_format($state, 0, ',', '.') : null)
                            ->dehydrateStateUsing(fn ($state) => (int) str_replace(['.', 'Rp ', ','], '', $state ?: '0'))
                            ->extraInputAttributes([
                                'style' => 'text-align: right',
                            ]),
                    ]),

                Forms\Components\Textarea::make('keterangan')
                    ->maxLength(255)
                    ->columnSpanFull(),
                    ])->columnSpan(2)->columns(2),
                formSection::make('Foto Barang dan Nota Beli')
                    ->columns(2)
                    ->schema([
                Forms\Components\FileUpload::make('foto_inventaris')
                ->label('Foto Barang')
                ->image(),
                Forms\Components\FileUpload::make('nota_beli')
                ->label('Nota Beli')
                ->image(),
                    ])->columnSpan(1)->columns(1),
                
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
                Split::make([
                    ImageColumn::make('foto_inventaris')
                        ->simpleLightbox()
                        ->height(120)
                        ->width(120)
                        ->extraImgAttributes(['style' => 'object-fit: cover; border-radius: 8px;']),
            
                    Stack::make([
                        TextColumn::make('nama_inventaris')
                            ->searchable()
                            ->sortable()
                            ->weight('bold')
                            ->searchable(),
                        // TextColumn::make('kode_inventaris')
                        //     ->badge()
                        //     ->color('primary')
                        //     ->searchable()
                            // ->weight('thin'),
                        TextColumn::make('kategoriInventaris.nama_kategori_inventaris')
                            ->searchable()
                            ->sortable(),
                        TextColumn::make('ruang.nama_ruangan')
                            ->searchable(),
                        TextColumn::make('total_harga')
                            ->money('IDR', locale: 'id_ID'),
                        TextColumn::make('tanggal_beli')
                            ->date('d/m/Y'),
                    ]),
                ]),
            ])
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\SelectFilter::make('kategori_inventaris_id')
                    ->label('Kategori Inventaris')
                    ->options(KategoriInventaris::pluck('nama_kategori_inventaris', 'id')),
                Tables\Filters\SelectFilter::make('ruang_id')
                    ->label('Ruang')
                    ->options(Ruangan::pluck('nama_ruangan', 'id')),
                Tables\Filters\Filter::make('tanggal_beli')
                    ->form([
                        Forms\Components\DatePicker::make('tanggal_dari')
                            ->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('tanggal_sampai')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['tanggal_dari'], fn ($query) => $query->where('tanggal_beli', '>=', $data['tanggal_dari']))
                            ->when($data['tanggal_sampai'], fn ($query) => $query->where('tanggal_beli', '<=', $data['tanggal_sampai']));
                    }),
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
                    Tables\Actions\BulkAction::make('exportLabels')
                        ->label('Export Label Barcode')
                        ->action(function (Collection $records) {
                            return self::exportBarcodeLabels($records);
                        })
                        ->modalHeading('Export Label Barcode')
                        ->modalSubmitActionLabel('Export PDF')
                        ->color('success'),
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
            'index' => Pages\ListTransaksionalInventaris::route('/'),
            'create' => Pages\CreateTransaksionalInventaris::route('/create'),
            'view' => Pages\ViewTransaksionalInventaris::route('/{record}'),
            'edit' => Pages\EditTransaksionalInventaris::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    protected static function updateTotalPrice(callable $set, callable $get): void
    {
        // Ambil nilai jumlah_beli dan bersihkan dari string non-numerik
        $jumlahBeli = (int) ($get('jumlah_beli') ?: 0);
        // Ambil nilai harga_satuan dan bersihkan dari format Rupiah
        $hargaSatuan = (int) str_replace(['.', 'Rp ', ','], '', $get('harga_satuan') ?: '0');
    
        // Hitung total
        $total = $jumlahBeli * $hargaSatuan;
    
        // Atur nilai total_harga
        $set('total_harga', $total);
    }

    protected static function exportBarcodeLabels($records)
    {
            $dompdf = new Dompdf();

            $html = view('pdf.barcode-labels', [
                'records' => $records,
            ])->render();

            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            return response()->streamDownload(function () use ($dompdf) {
                echo $dompdf->output();
            }, 'barcode-labels-' . now()->format('YmdHis') . '.pdf');
    }
}
