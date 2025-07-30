<?php

namespace App\Filament\Admin\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Semester;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\SuratKeluar;
use App\Models\TahunAjaran;
use App\Models\KategoriSurat;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Admin\Resources\SuratKeluarResource\Pages;
use App\Filament\Admin\Resources\SuratKeluarResource\RelationManagers;

class SuratKeluarResource extends Resource
{
    protected static ?string $model = SuratKeluar::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Surat';

    protected static ?string $navigationLabel = 'Surat Keluar';

    protected static ?string $modelLabel = 'Surat Keluar';

    protected static ?string $pluralModelLabel = 'Surat Keluar';

    protected static ?string $slug = 'surat-keluar';

    /**
     * Helper untuk generate nomor surat
     * Nomor urut dihitung berdasarkan th_ajaran_id
     */
    public static function generateNomorSurat($tanggalSurat, $kategoriSuratId): string
    {                                                               
        $activeTahunAjaran = cache()->remember('active_th_ajaran', now()->addMinutes(1), fn () => \App\Models\TahunAjaran::where('status', true)->first());
        if (!$activeTahunAjaran) {
            throw new \Exception('Tidak ada tahun ajaran yang aktif. Silakan aktifkan tahun ajaran terlebih dahulu.');
        }

        $tahun = date('Y', strtotime($tanggalSurat));
        $bulan = date('n', strtotime($tanggalSurat));

        $bulanRomawiMap = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V',
            6 => 'VI', 7 => 'VII', 8 => 'VIII', 9 => 'IX',
            10 => 'X', 11 => 'XI', 12 => 'XII'
        ];
        $bulanRomawi = $bulanRomawiMap[$bulan] ?? '';

        $kategoriSurat = KategoriSurat::find($kategoriSuratId);
        $kodeKategori = $kategoriSurat->kode_kategori ?? 'UNKNOWN';

        // Hitung nomor urut berdasarkan th_ajaran_id
        $lastSurat = SuratKeluar::where('th_ajaran_id', $activeTahunAjaran->id)
            ->orderBy('nomor_urut', 'desc')
            ->first();

        $nomorUrut = $lastSurat ? $lastSurat->nomor_urut + 1 : 1;
        $nomorUrutFormatted = str_pad($nomorUrut, 3, '0', STR_PAD_LEFT);

        // Notifikasi jika nomor urut direset
        if ($nomorUrut === 1) {
            Notification::make()
                ->title('Info')
                ->body('Nomor urut surat telah direset ke 001 untuk tahun ajaran baru.')
                ->info()
                ->send();
        }

        return "$nomorUrutFormatted/$kodeKategori/SMPIT-AFISKU/$bulanRomawi/$tahun";
    }

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
                        TextInput::make('perihal')
                            ->required()
                            ->maxLength(255)
                            ->disabled(!$isTahunAjaranActive || !$activeSemester), // Perbaiki typo: >disabled menjadi ->disabled
                        TextInput::make('tujuan_pengiriman')
                            ->required()
                            ->maxLength(255)
                            ->disabled(!$isTahunAjaranActive || !$activeSemester), // Perbaiki typo: >disabled menjadi ->disabled
                        DatePicker::make('tgl_surat_keluar')
                            ->label('Tanggal Surat Keluar')
                            ->required()
                            ->placeholder('d/m/Y')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->live()
                            ->maxDate(now()->format('Y-m-d'))
                            ->disabled(!$isTahunAjaranActive || !$activeSemester) // Pastikan dinonaktifkan jika tidak ada semester aktif
                            ->afterStateUpdated(function (callable $get, callable $set) use ($isTahunAjaranActive, $activeSemester) {
                                if (!$isTahunAjaranActive || !$activeSemester) {
                                    \Log::info('Nomor surat tidak digenerate: Tidak ada tahun ajaran atau semester aktif');
                                    return;
                                }

                                if ($get('tgl_surat_keluar') && $get('kategori_surat_id')) {
                                    try {
                                        $noSurat = self::generateNomorSurat($get('tgl_surat_keluar'), $get('kategori_surat_id'));
                                        $set('no_surat', $noSurat);
                                    } catch (\Exception $e) {
                                        Notification::make()
                                            ->title('Error')
                                            ->body($e->getMessage())
                                            ->danger()
                                            ->send();
                                    }
                                } else {
                                    \Log::info('Nomor surat tidak terisi: tgl_surat_keluar atau kategori_surat_id kosong', [
                                        'tgl_surat_keluar' => $get('tgl_surat_keluar'),
                                        'kategori_surat_id' => $get('kategori_surat_id'),
                                    ]);
                                }
                            }),
                        Select::make('kategori_surat_id')
                            ->label('Kategori Surat')
                            ->required()
                            ->placeholder('Pilih Kategori')
                            ->searchable()
                            ->preload()
                            ->options(fn () => KategoriSurat::pluck('kategori', 'id'))
                            ->live()
                            ->rules(['exists:kategori_surat,id'])
                            ->disabled(!$isTahunAjaranActive || !$activeSemester) // Pastikan dinonaktifkan jika tidak ada semester aktif
                            ->afterStateUpdated(function (callable $get, callable $set) use ($isTahunAjaranActive, $activeSemester) {
                                if (!$isTahunAjaranActive || !$activeSemester) {
                                    \Log::info('Nomor surat tidak digenerate: Tidak ada tahun ajaran atau semester aktif');
                                    return;
                                }

                                if ($get('tgl_surat_keluar') && $get('kategori_surat_id')) {
                                    try {
                                        $noSurat = self::generateNomorSurat($get('tgl_surat_keluar'), $get('kategori_surat_id'));
                                        $set('no_surat', $noSurat);
                                    } catch (\Exception $e) {
                                        Notification::make()
                                            ->title('Error')
                                            ->body($e->getMessage())
                                            ->danger()
                                            ->send();
                                    }
                                } else {
                                    \Log::info('Nomor surat tidak terisi: tgl_surat_keluar atau kategori_surat_id kosong', [
                                        'tgl_surat_keluar' => $get('tgl_surat_keluar'),
                                        'kategori_surat_id' => $get('kategori_surat_id'),
                                    ]);
                                }
                            }),
                        TextInput::make('no_surat')
                            ->label('Nomor Surat')
                            ->required()
                            ->columnSpanFull()
                            ->disabled()
                            ->hidden()
                            ->placeholder('Nomor surat akan otomatis terisi setelah memilih tanggal dan kategori')
                            ->unique(table: SuratKeluar::class, ignoreRecord: true),
                    ])->columns(2)
            ]);
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
                return $query->orderBy('no_surat', 'desc');
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
                TextColumn::make('No')
                    ->rowIndex(),
                TextColumn::make('no_surat')
                    ->copyable()
                    ->copyMessage('Copy no surat keluar berhasil')
                    ->searchable(),
                TextColumn::make('perihal'),
                TextColumn::make('kategoriSurat.kategori')
                    ->label('Kategori')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('tgl_surat_keluar')
                    ->date('d/m/Y')
                    ->label('Tgl Surat'),
                TextColumn::make('tujuan_pengiriman')
                    ->label('Tujuan')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('user.pegawai.nm_pegawai')
                    ->label('Dibuat Oleh'),
            ])
            ->filters([
                SelectFilter::make('kategori_surat_id')
                    ->label('Kategori Surat')
                    ->relationship('kategoriSurat', 'kategori')
                    ->searchable()
                    ->preload(),
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
            // Aksi untuk mengunggah dokumen (sebelum dokumen ada)
            Tables\Actions\Action::make('unggah_dokumen')
                ->label(false) // Hapus label teks, hanya ikon
                ->color('primary') // Warna biru sebelum dokumen di-upload
                ->icon('heroicon-o-arrow-up-on-square')
                ->tooltip('Unggah Dokumen') // Tooltip untuk memberi informasi
                ->modalWidth('lg')
                ->visible(fn(SuratKeluar $record): bool => is_null($record->dokumen)) // Hapus pembatasan superadmin
                ->form([
                    Forms\Components\FileUpload::make('dokumen')
                        ->label('Pilih Dokumen (PDF Maks. 5MB)')
                        ->visibility('public')
                        ->disk('public')
                        ->directory('dokumen/arsip_surat_keluar')
                        ->acceptedFileTypes(['application/pdf'])
                        ->maxSize(5024)
                        ->helperText('Pastikan dokumen dalam format PDF dan ukurannya tidak lebih dari 5MB.')
                        ->rules([
                            'file',
                            'mimes:pdf',
                            'max:5024',
                        ])
                        ->validationMessages([
                            'mimes' => 'Dokumen harus berformat PDF.',
                            'max' => 'Ukuran dokumen maksimal 5 MB.',
                        ]),
                ])
                ->action(function (SuratKeluar $record, array $data) {
                    // Update kolom dokumen dengan file yang diunggah
                    $record->update([
                        'dokumen' => $data['dokumen'],
                    ]);
                    Notification::make()
                        ->title('Upload Berhasil')
                        ->body('Dokumen telah berhasil diunggah.')
                        ->success()
                        ->send();
                }),
            // Tombol Kelola Dokumen (setelah dokumen ada) dengan ActionGroup untuk dropdown
            Tables\Actions\ActionGroup::make([
                Tables\Actions\Action::make('lihat_dokumen')
                    ->label('Lihat Dokumen')
                    ->color('success')
                    ->icon('heroicon-o-document-text')
                    ->url(fn(SuratKeluar $record): string => asset('storage/' . $record->dokumen))
                    ->openUrlInNewTab(),
                Tables\Actions\Action::make('hapus_dokumen')
                    ->label('Hapus Dokumen')
                    ->color('danger')
                    ->icon('heroicon-o-trash')
                    ->requiresConfirmation()
                    ->action(function (SuratKeluar $record) {
                        if ($record->dokumen) {
                            if (Storage::disk('public')->exists($record->dokumen)) {
                                Storage::disk('public')->delete($record->dokumen);
                            }
                            $record->update(['dokumen' => null]);
                            Notification::make()
                                ->title('Dokumen Dihapus')
                                ->body('Dokumen telah berhasil dihapus.')
                                ->success()
                                ->send();
                            }
                        }),
                ])
                ->label(false) // Hapus label teks, hanya ikon
                ->color('warning') // Warna kuning setelah dokumen di-upload
                ->icon('heroicon-o-cog')
                ->tooltip('Kelola Dokumen') // Tooltip untuk memberi informasi
                ->visible(fn(SuratKeluar $record): bool => !is_null($record->dokumen)), // Hapus pembatasan superadmin
            ])
            ->headerActions([
                Tables\Actions\Action::make('export_pdf')
                    ->label('Export Agenda PDF')
                    ->icon('heroicon-o-document-text')
                    ->action(function ($livewire) use ($activeTahunAjaran, $activeSemester) {
                        // Validasi tahun ajaran dan semester aktif
                        if (!$activeTahunAjaran || !$activeSemester) {
                            Notification::make()
                                ->title('Error')
                                ->body('Tidak ada tahun ajaran atau semester aktif.')
                                ->danger()
                                ->send();
                            return;
                        }
            
                        // Ambil filter atau gunakan default
                        $tahunAjaranId = $livewire->tableFilters['th_ajaran_id']['value'] ?? $activeTahunAjaran->id;
                        $semesterId = $livewire->tableFilters['semester_id']['value'] ?? $activeSemester->id;
            
                        // Validasi tahun ajaran
                        $tahunAjaran = \App\Models\TahunAjaran::find($tahunAjaranId);
                        if (!$tahunAjaran) {
                            Notification::make()
                                ->title('Error')
                                ->body('Tahun ajaran tidak ditemukan.')
                                ->danger()
                                ->send();
                            return;
                        }
            
                        // Validasi semester
                        $semester = \App\Models\Semester::find($semesterId);
                        if (!$semester) {
                            Notification::make()
                                ->title('Error')
                                ->body('Semester tidak ditemukan.')
                                ->danger()
                                ->send();
                            return;
                        }
            
                        // Ambil data surat keluar
                        $suratKeluars = SuratKeluar::where('th_ajaran_id', $tahunAjaranId)
                            ->where('semester_id', $semesterId)
                            ->with('kategoriSurat')
                            ->orderBy('nomor_urut')
                            ->get();
            
                        // Cek jika data kosong
                        if ($suratKeluars->isEmpty()) {
                            Notification::make()
                                ->title('Peringatan')
                                ->body('Tidak ada surat keluar untuk periode ini.')
                                ->warning()
                                ->send();
                            return;
                        }
            
                        // Generate PDF
                        $pdf = Pdf::loadView('pdf.agenda-surat-keluar', [
                            'suratKeluars' => $suratKeluars,
                            'semester' => $semester,
                            'tahunAjaran' => $tahunAjaran,
                        ])->setPaper('a4', 'landscape');
            
                        // Unduh PDF
                        return response()->streamDownload(
                            fn () => print($pdf->output()),
                            'agenda-surat-keluar-' . str_replace('/', '-', $tahunAjaran->th_ajaran) . '.pdf'
                        );
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListSuratKeluars::route('/'),
            'create' => Pages\CreateSuratKeluar::route('/create'),
            'view' => Pages\ViewSuratKeluar::route('/{record}'),
            'edit' => Pages\EditSuratKeluar::route('/{record}/edit'),
        ];
    }
}