<?php

namespace App\Filament\Admin\Resources\PegawaiResource\Pages;

use App\Filament\Admin\Resources\PegawaiResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPegawai extends ViewRecord
{
    protected static string $resource = PegawaiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
