<?php

namespace App\Filament\Admin\Resources\TransaksionalInventarisResource\Pages;

use App\Filament\Admin\Resources\TransaksionalInventarisResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTransaksionalInventaris extends ViewRecord
{
    protected static string $resource = TransaksionalInventarisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
