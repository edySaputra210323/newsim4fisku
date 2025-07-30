<?php

namespace App\Filament\Admin\Clusters\Master\Resources\SumberAnggaranResource\Pages;

use App\Filament\Admin\Clusters\Master\Resources\SumberAnggaranResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSumberAnggaran extends ViewRecord
{
    protected static string $resource = SumberAnggaranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
