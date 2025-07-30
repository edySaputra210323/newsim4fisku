<?php

namespace App\Filament\Admin\Clusters\Master\Resources;

use App\Filament\Admin\Clusters\Master;
use App\Filament\Admin\Clusters\Master\Resources\SumberAnggaranResource\Pages;
use App\Filament\Admin\Clusters\Master\Resources\SumberAnggaranResource\RelationManagers;
use App\Models\SumberAnggaran;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SumberAnggaranResource extends Resource
{
    protected static ?string $model = SumberAnggaran::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationGroup = 'Master Inventaris';

    protected static ?string $navigationLabel = 'Sumber Anggaran';

    protected static ?string $modelLabel = 'Sumber Anggaran';

    protected static ?string $pluralModelLabel = 'Sumber Anggaran';

    protected static ?string $slug = 'sumber-anggaran';

    protected static ?string $cluster = Master::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama_sumber_anggaran')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_sumber_anggaran')
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
            'index' => Pages\ListSumberAnggarans::route('/'),
            'create' => Pages\CreateSumberAnggaran::route('/create'),
            'view' => Pages\ViewSumberAnggaran::route('/{record}'),
            'edit' => Pages\EditSumberAnggaran::route('/{record}/edit'),
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
