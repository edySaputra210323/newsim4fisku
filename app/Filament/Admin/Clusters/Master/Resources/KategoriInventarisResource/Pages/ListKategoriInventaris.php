<?php

namespace App\Filament\Admin\Clusters\Master\Resources\KategoriInventarisResource\Pages;

use App\Filament\Admin\Clusters\Master\Resources\KategoriInventarisResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKategoriInventaris extends ListRecords
{
    protected static string $resource = KategoriInventarisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
