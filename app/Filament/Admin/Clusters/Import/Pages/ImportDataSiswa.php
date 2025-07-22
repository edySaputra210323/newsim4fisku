<?php

namespace App\Filament\Admin\Clusters\Import\Pages;

use App\Filament\Admin\Clusters\Import;
use App\Imports\SiswaImportProcessor;
use App\Models\SiswaImportFailed;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Contracts\Database\Eloquent\Builder;
use stdClass;

class ImportDataSiswa extends Page implements HasTable
{
    use InteractsWithTable;

    use HasPageShield;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-up-on-square';

    protected static string $view = 'filament.admin.clusters.import.pages.import-data-siswa';

    protected static ?string $cluster = Import::class;

    protected function getHeaderActions(): array
    {
         return [
            \EightyNine\ExcelImport\ExcelImportAction::make()
                ->color("primary")
                ->use(SiswaImportProcessor::class),
            \Filament\Actions\Action::make('download')
                ->label('Download Template')
                ->color('success')
                ->icon('heroicon-m-document-arrow-down')
                ->url(route('download.template.datasiswa')) // Route untuk download
                ->openUrlInNewTab(), // Membuka di tab baru (opsional)
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(SiswaImportFailed::query())
            ->heading('Gagal Import Pegawai')
            ->extremePaginationLinks()
            ->recordUrl(null)
            ->paginated([5, 10, 20, 50])
            ->defaultPaginationPageOption(10)
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
                Tables\Columns\TextColumn::make('nama_siswa')
                    ->label('Nama Lengkap')
                    ->searchable(),
                Tables\Columns\TextColumn::make('virtual_account')
                    ->label('No. Virtual Account')
                    ->searchable(),
                Tables\Columns\TextColumn::make('catatan_gagal')
                    ->label('Catatan')
                    ->wrap(),
            ])
            ->filters([
                // ...
            ])
            ->actions([
                Tables\Actions\DeleteAction::make()
                    ->iconButton()
                    ->color('danger')
                    ->icon('heroicon-m-trash')
                    ->modalHeading('Hapus Import Pegawai Yang Gagal'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
