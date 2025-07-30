<?php

namespace App\Filament\Admin\Clusters\Master\Resources\KategoriInventarisResource\Pages;

use App\Filament\Admin\Clusters\Master\Resources\KategoriInventarisResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewKategoriInventaris extends ViewRecord
{
    protected static string $resource = KategoriInventarisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
