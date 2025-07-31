<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\SuplayerResource\Pages;
use App\Filament\Admin\Resources\SuplayerResource\RelationManagers;
use App\Models\Suplayer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SuplayerResource extends Resource
{
    protected static ?string $model = Suplayer::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationGroup = 'Inventaris';

    protected static ?string $navigationLabel = 'Suplayer';

    protected static ?string $modelLabel = 'Suplayer';

    protected static ?string $pluralModelLabel = 'Suplayer';

    protected static ?string $slug = 'suplayer';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama_suplayer')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('alamat_suplayer')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('no_telp_suplayer')
                    ->tel()
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_suplayer')
                    ->searchable(),
                Tables\Columns\TextColumn::make('alamat_suplayer')
                    ->searchable(),
                Tables\Columns\TextColumn::make('no_telp_suplayer')
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
            'index' => Pages\ListSuplayers::route('/'),
            'create' => Pages\CreateSuplayer::route('/create'),
            'view' => Pages\ViewSuplayer::route('/{record}'),
            'edit' => Pages\EditSuplayer::route('/{record}/edit'),
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
