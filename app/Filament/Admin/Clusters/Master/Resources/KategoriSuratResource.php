<?php

namespace App\Filament\Admin\Clusters\Master\Resources;

use stdClass;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\KategoriSurat;
use Filament\Resources\Resource;
use App\Filament\Admin\Clusters\Master;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Admin\Clusters\Master\Resources\KategoriSuratResource\Pages;
use App\Filament\Admin\Clusters\Master\Resources\KategoriSuratResource\RelationManagers;

class KategoriSuratResource extends Resource
{
    protected static ?string $model = KategoriSurat::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Kategori Surat';

    protected static ?string $modelLabel = 'Kategori Surat';

    protected static ?string $pluralModelLabel = 'Kategori Surat';

    protected static ?string $slug = 'kategori-surat';

    protected static ?string $cluster = Master::class;

    public static function form(Form $form): Form
    {
        return $form
        ->inlineLabel()
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                Forms\Components\TextInput::make('kode_kategori')
                    ->maxLength(50)
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->validationMessages([
                        'unique' => 'Kode Kategori sudah ada, gunakan kode yang lain',
                        'required' => 'Kode Kategori tidak boleh kosong',
                    ]),
                Forms\Components\TextInput::make('kategori')
                    ->required()
                    ->maxLength(100),
                Forms\Components\Textarea::make('deskripsi')
                    ->columnSpanFull(),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->modifyQueryUsing(function (Builder $query) {
            return $query->orderBy('kode_kategori', 'asc');
        })
        ->recordAction(null)
        ->recordUrl(null)
        ->extremePaginationLinks()
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
                Tables\Columns\TextColumn::make('kode_kategori')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kategori')
                    ->searchable(),
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
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->iconButton()
                    ->color('warning')
                    ->icon('heroicon-m-pencil-square'),
                Tables\Actions\DeleteAction::make()
                    ->iconButton()
                    ->color('danger')
                    ->icon('heroicon-m-trash')
                    ->modalHeading('Hapus Kategori Surat'),
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
            'index' => Pages\ListKategoriSurats::route('/'),
            'create' => Pages\CreateKategoriSurat::route('/create'),
            'view' => Pages\ViewKategoriSurat::route('/{record}'),
            'edit' => Pages\EditKategoriSurat::route('/{record}/edit'),
        ];
    }
}
