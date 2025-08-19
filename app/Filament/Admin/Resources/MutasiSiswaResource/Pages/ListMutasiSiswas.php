<?php

namespace App\Filament\Admin\Resources\MutasiSiswaResource\Pages;

use App\Filament\Admin\Resources\MutasiSiswaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Admin\Resources\MutasiSiswaResource\Widgets\MutasiOverview;

class ListMutasiSiswas extends ListRecords
{
    protected static string $resource = MutasiSiswaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            MutasiOverview::class,
        ];
    }
}
