<?php

namespace App\Filament\Admin\Resources\RiwayatKelasResource\Pages;

use App\Filament\Admin\Resources\RiwayatKelasResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewRiwayatKelas extends ViewRecord
{
    protected static string $resource = RiwayatKelasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
