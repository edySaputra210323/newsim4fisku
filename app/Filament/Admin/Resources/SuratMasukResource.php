<?php

namespace App\Filament\Admin\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\SuratMasuk;
use Filament\Tables\Table;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Resources\Resource;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Section;
use Filament\Support\Enums\FontWeight;
use Filament\Notifications\Notification;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Admin\Resources\SuratMasukResource\Pages;
use App\Filament\Admin\Resources\SuratMasukResource\RelationManagers;

class SuratMasukResource extends Resource
{
    protected static ?string $model = SuratMasuk::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Surat';

    protected static ?string $navigationLabel = 'Surat Masuk';

    protected static ?string $modelLabel = 'Surat Masuk';

    protected static ?string $pluralModelLabel = 'Surat Masuk';

    protected static ?string $slug = 'surat-masuk';

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
                    Section::make()
                        ->schema([
                    Forms\Components\TextInput::make('nm_pengirim')
                        ->label('Data Pengirim Surat')
                        ->maxLength(255)
                        ->required()
                        ->placeholder('Contoh: Dinas Komunikasi dan Informatika')
                        ->validationMessages([
                            'required' => 'Data pengirim / instansi pengiri surat wajib diisi',
                            'max' => 'Data pengirim surat tidak boleh lebih dari 255 karakter',
                        ])
                        ->disabled(!$isTahunAjaranActive || !$activeSemester),
                    Forms\Components\TextInput::make('no_surat')
                        ->label('Nomor Surat')
                        ->maxLength(50)
                        ->unique(ignoreRecord: true)
                        ->required()
                        ->validationMessages([
                            'required' => 'Nomor surat wajib diisi',
                            'max' => 'Nomor surat tidak boleh lebih dari 50 karakter',
                            'unique' => 'Nomor surat sudah ada',
                        ])
                        ->disabled(!$isTahunAjaranActive || !$activeSemester),
                    Forms\Components\TextInput::make('perihal')
                        ->label('Perihal Surat Masuk')
                        ->maxLength(255)
                        ->required()
                        ->validationMessages([
                            'required' => 'Perihal surat wajib diisi',
                            'max' => 'Perihal surat tidak boleh lebih dari 255 karakter',
                        ])
                        ->disabled(!$isTahunAjaranActive || !$activeSemester),
                    Forms\Components\TextInput::make('tujuan_surat')
                        ->label('Tujuan Surat')
                        ->required()
                        ->placeholder('Contoh: Kepala sekolah')
                        ->maxLength(255)
                        ->validationMessages([
                            'required' => 'Tujuan Surat wajib diisi',
                            'max' => 'Tujuan Surat tidak boleh lebih dari 255 karakter',
                        ])
                        ->disabled(!$isTahunAjaranActive || !$activeSemester),
                    Forms\Components\DatePicker::make('tgl_terima')
                        ->label('Tanggal Surat Terima')
                        ->native(false)
                        ->displayFormat('d/m/Y')
                        ->required()
                        ->validationMessages([
                            'required' => 'Tanggal surat terima wajib diisi',
                        ]) 
                        ->disabled(!$isTahunAjaranActive || !$activeSemester),
                    Forms\Components\DatePicker::make('tgl_surat')
                        ->label('Tanggal Surat di Sahkan')
                        ->native(false)
                        ->displayFormat('d/m/Y')
                        ->required()
                        ->validationMessages([
                            'required' => 'Tanggal surat di sahkan wajib diisi',
                        ])
                        ->disabled(!$isTahunAjaranActive || !$activeSemester),
                    Forms\Components\Select::make('status')
                        ->options([
                            'diterima' => 'Diterima',
                            'diproses' => 'Diproses',
                            'selesai' => 'Selesai',
                        ])
                        ->default('diterima')
                        ->required()
                        ->columnSpan(2)
                    ])->columnSpan(2)->columns(2),
                    Section::make('Upload Arsip Dokumen')
                        ->collapsible()
                        ->description('format PDF, JPEG, atau PNG')
                        ->schema([
                        Forms\Components\FileUpload::make('file_surat')
                        ->label('File Surat')
                        ->disk('public')
                        ->directory('surat_masuk')
                        ->label(false)
                        ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                        ->disabled(!$isTahunAjaranActive || !$activeSemester),
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
        ->modifyQueryUsing(function (Builder $query) {
            return $query->orderBy('tgl_terima', 'desc');
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
                Tables\Columns\TextColumn::make('No')
                    ->rowIndex(),
                Tables\Columns\TextColumn::make('nm_pengirim')
                    ->searchable()
                    ->label('Pengirim')
                    ->weight(FontWeight::Bold)
                    ->label('Nama Pegawai')
                    ->description(function ($record) {
                        $data = '';
                        if (!empty($record->no_surat)) {
                            $data .= '<small>no surat : ' . $record->no_surat . '</small>';
                        }
                        return new HtmlString($data);
                    }),
                Tables\Columns\TextColumn::make('tgl_terima')
                    ->date('d/m/Y')
                    ->label('Tgl Terima')
                    ->sortable(),
                Tables\Columns\TextColumn::make('tgl_surat')
                    ->date('d/m/Y')
                    ->label('Tgl Surat')
                    ->sortable(),
                Tables\Columns\TextColumn::make('perihal')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.pegawai.nm_pegawai')
                    ->label('Diterima Oleh')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                ->label('Status')
                ->searchable()
                ->badge()
                ->color(function ($record) {
                    if ($record->status == 'diterima') {
                        return 'success';
                    } elseif ($record->status == 'diproses') {
                        return 'warning';
                    } elseif ($record->status == 'selesai') {
                        return 'info';
                    }
                }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
            SelectFilter::make('th_ajaran_id')
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
            ])
            ->actions([
            Tables\Actions\Action::make('lihat_dokumen')
                ->iconButton()
                ->color('primary')
                ->tooltip('Lihat Dokumen')
                ->icon('heroicon-o-document')
                ->url(fn(SuratMasuk $record) => $record->file_surat 
                    ? asset('storage/' . $record->file_surat) 
                    : null, shouldOpenInNewTab: true)
                ->visible(fn ($record) => !empty($record->file_surat)),
            Tables\Actions\EditAction::make()
                ->iconButton()
                ->color('warning')
                ->tooltip('Ubah Data')
                ->icon('heroicon-o-pencil-square'),
            Tables\Actions\DeleteAction::make()
                ->iconButton()
                ->color('danger')
                ->tooltip('Hapus Data')
                ->icon('heroicon-o-trash')
                ->modalHeading('Hapus Data'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                Tables\Actions\Action::make('export_pdf')
                    ->label('Export Agenda PDF')
                    ->icon('heroicon-o-document-text')
                    ->action(function ($livewire) use ($activeTahunAjaran, $activeSemester) {
                        if (!$activeTahunAjaran || !$activeSemester) {
                            Notification::make()
                                ->title('Error')
                                ->body('Tidak ada tahun ajaran atau semester aktif.')
                                ->danger()
                                ->send();
                            return;
                        }

                        $tahunAjaranId = $livewire->tableFilters['th_ajaran_id']['value'] ?? $activeTahunAjaran->id;
                        $semesterId = $livewire->tableFilters['semester_id']['value'] ?? $activeSemester->id;

                        $tahunAjaran = \App\Models\TahunAjaran::find($tahunAjaranId);
                        if (!$tahunAjaran) {
                            Notification::make()
                                ->title('Error')
                                ->body('Tahun ajaran tidak ditemukan.')
                                ->danger()
                                ->send();
                            return;
                        }

                        $semester = \App\Models\Semester::find($semesterId);
                        if (!$semester) {
                            Notification::make()
                                ->title('Error')
                                ->body('Semester tidak ditemukan.')
                                ->danger()
                                ->send();
                            return;
                        }

                        $suratMasuks = SuratMasuk::where('th_ajaran_id', $tahunAjaranId)
                            ->where('semester_id', $semesterId)
                            ->with(['user.pegawai', 'semester', 'tahunAjaran'])
                            ->orderBy('tgl_terima')
                            ->get();

                        if ($suratMasuks->isEmpty()) {
                            Notification::make()
                                ->title('Peringatan')
                                ->body('Tidak ada surat masuk untuk periode ini.')
                                ->warning()
                                ->send();
                            return;
                        }

                        $pdf = Pdf::loadView('pdf.agenda-surat-masuk', [
                            'suratMasuks' => $suratMasuks,
                            'semester' => $semester,
                            'tahunAjaran' => $tahunAjaran,
                        ])->setPaper('a4', 'landscape');

                        return response()->streamDownload(
                            fn () => print($pdf->output()),
                            'agenda-surat-masuk-' . str_replace('/', '-', $tahunAjaran->th_ajaran) . '.pdf'
                        );
                    }),
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
            'index' => Pages\ListSuratMasuks::route('/'),
            'create' => Pages\CreateSuratMasuk::route('/create'),
            'view' => Pages\ViewSuratMasuk::route('/{record}'),
            'edit' => Pages\EditSuratMasuk::route('/{record}/edit'),
        ];
    }
}
