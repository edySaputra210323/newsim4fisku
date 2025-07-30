<?php

namespace App\Filament\Admin\Clusters\Master\Resources\GedungResource\Pages;

use App\Filament\Admin\Clusters\Master\Resources\GedungResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewGedung extends ViewRecord
{
    protected static string $resource = GedungResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
