<?php

namespace App\Filament\Admin\Resources\TransaksionalInventarisResource\Pages;

use App\Filament\Admin\Resources\TransaksionalInventarisResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTransaksionalInventaris extends ListRecords
{
    protected static string $resource = TransaksionalInventarisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
