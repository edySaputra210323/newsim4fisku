<?php

namespace App\Filament\Admin\Clusters\Master\Resources\KategoriBarangResource\Pages;

use App\Filament\Admin\Clusters\Master\Resources\KategoriBarangResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewKategoriBarang extends ViewRecord
{
    protected static string $resource = KategoriBarangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
