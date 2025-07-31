<?php

namespace App\Filament\Admin\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Ruangan;
use App\Models\Suplayer;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\KategoriBarang;
use App\Models\SumberAnggaran;
use Filament\Resources\Resource;
use App\Models\KategoriInventaris;
use App\Models\TransaksionalInventaris;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
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
                        Forms\Components\TextInput::make('jumlah_beli')
                        ->required()
                        ->numeric(),
                    Forms\Components\TextInput::make('harga_satuan')
                        ->required()
                        ->numeric(),
                    Forms\Components\TextInput::make('total_harga')
                        ->required()
                        ->numeric(),
                    ]),
                Forms\Components\Textarea::make('keterangan')
                    ->maxLength(255)
                    ->columnSpanFull(),
                    ])->columnSpan(2)->columns(2),
                formSection::make('Foto Barang dan Nota Beli')
                    ->columns(2)
                    ->schema([
                Forms\Components\FileUpload::make('foto_inventaris'),
                Forms\Components\FileUpload::make('nota_beli'),
                    ])->columnSpan(1)->columns(1),
                
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kode_inventaris')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kategori_inventaris_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('no_urut_barang')
                ->label('No. Urut Barang')
                ->sortable(),
                Tables\Columns\TextColumn::make('suplayer_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('kategori_barang_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sumber_anggaran_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ruang_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nama_inventaris')
                    ->searchable(),
                Tables\Columns\TextColumn::make('merk_inventaris')
                    ->searchable(),
                Tables\Columns\TextColumn::make('material_bahan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kondisi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tanggal_beli')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jumlah_beli')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('harga_satuan')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_harga')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('keterangan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('foto_inventaris')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nota_beli')
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
}
