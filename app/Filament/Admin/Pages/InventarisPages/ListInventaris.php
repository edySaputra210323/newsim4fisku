<?php

namespace App\Filament\Admin\Pages\InventarisPages;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Pages\Page;
use App\Models\TransaksionalInventaris;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Concerns\InteractsWithTable;

class ListInventaris extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.admin.pages.inventaris-pages.list-inventaris';

    protected static ?string $navigationLabel = 'Grid Inventaris';

    protected static ?string $navigationGroup = 'Inventaris';

    protected static ?int $navigationSort = 2;

    public function getTableQuery()
    {
        return \App\Models\TransaksionalInventaris::query()
        ->with(['kategoriInventaris', 'ruang']);
    }

    public function getTableColumns(): array
    {
        
        return [
            Tables\Columns\Layout\Split::make([
                Tables\Columns\ImageColumn::make('foto_inventaris')
                    ->simpleLightbox()
                    ->height(120)
                    ->width(120)
                    ->extraImgAttributes(['style' => 'object-fit: cover; border-radius: 8px;']),
    
                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\TextColumn::make('nama_inventaris')
                        ->searchable()
                        ->sortable()
                        ->weight('bold'),
    
                    Tables\Columns\TextColumn::make('kategoriInventaris.nama_kategori_inventaris')
                        ->searchable()
                        ->sortable(),
    
                    Tables\Columns\TextColumn::make('ruang.nama_ruangan')
                        ->searchable(),
    
                    Tables\Columns\TextColumn::make('total_harga')
                        ->money('IDR', locale: 'id_ID'),
    
                    Tables\Columns\TextColumn::make('tanggal_beli')
                        ->date('d/m/Y'),
                ]),
            ]),
        ];
    }
    public function getTableContentGrid(): ?array
{
    return [
        'md' => 2,
        'xl' => 3,
    ];
}
}

