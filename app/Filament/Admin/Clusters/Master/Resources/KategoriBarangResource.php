<?php

namespace App\Filament\Admin\Clusters\Master\Resources;

use App\Filament\Admin\Clusters\Master;
use App\Filament\Admin\Clusters\Master\Resources\KategoriBarangResource\Pages;
use App\Filament\Admin\Clusters\Master\Resources\KategoriBarangResource\RelationManagers;
use App\Models\KategoriBarang;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class KategoriBarangResource extends Resource
{
    protected static ?string $model = KategoriBarang::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'Master Inventaris';

    protected static ?string $navigationLabel = 'Kategori Barang';

    protected static ?string $modelLabel = 'Kategori Barang';

    protected static ?string $pluralModelLabel = 'Kategori Barang';

    protected static ?string $slug = 'kategori-barang';

    protected static ?string $cluster = Master::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama_kategori_barang')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('kode_kategori_barang')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('deskripsi_kategori_barang')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_kategori_barang')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kode_kategori_barang')
                    ->searchable(),
                Tables\Columns\TextColumn::make('deskripsi_kategori_barang')
                    ->searchable(),
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
            'index' => Pages\ListKategoriBarangs::route('/'),
            'create' => Pages\CreateKategoriBarang::route('/create'),
            'view' => Pages\ViewKategoriBarang::route('/{record}'),
            'edit' => Pages\EditKategoriBarang::route('/{record}/edit'),
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
