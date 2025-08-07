<?php

namespace App\Filament\Admin\Clusters\Master\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Ruangan;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Section;
use App\Filament\Admin\Clusters\Master;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Admin\Clusters\Master\Resources\RuanganResource\Pages;
use App\Filament\Admin\Clusters\Master\Resources\RuanganResource\RelationManagers;

class RuanganResource extends Resource
{
    protected static ?string $model = Ruangan::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationGroup = 'Master Inventaris';

    protected static ?string $navigationLabel = 'Ruangan';

    protected static ?string $modelLabel = 'Ruangan';

    protected static ?string $pluralModelLabel = 'Ruangan';

    protected static ?string $slug = 'ruangan';

    protected static ?string $cluster = Master::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                ->schema([
                    Forms\Components\Select::make('gedung_id')
                    ->relationship('gedung', 'nama_gedung')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\TextInput::make('nama_ruangan')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('lantai')
                    ->options([
                        '1' => 'Lantai 1',
                        '2' => 'Lantai 2',
                        '3' => 'Lantai 3',
                        '4' => 'Lantai 4',
                        '5' => 'Lantai 5',
                        '6' => 'Lantai 6',
                        '7' => 'Lantai 7',
                        '8' => 'Lantai 8',
                        '9' => 'Lantai 9',
                        '10' => 'Lantai 10',
                    ])
                    ->native(false),
                Forms\Components\TextInput::make('kode_ruangan')
                    ->maxLength(255),
                Forms\Components\Textarea::make('deskripsi_ruangan')
                    ->rows(3)
                    ->maxLength(255),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('gedung.nama_gedung')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('lantai')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nama_ruangan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kode_ruangan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('deskripsi_ruangan')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListRuangans::route('/'),
            'create' => Pages\CreateRuangan::route('/create'),
            'view' => Pages\ViewRuangan::route('/{record}'),
            'edit' => Pages\EditRuangan::route('/{record}/edit'),
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
