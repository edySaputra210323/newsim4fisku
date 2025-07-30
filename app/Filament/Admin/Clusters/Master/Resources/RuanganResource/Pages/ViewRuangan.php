<?php

namespace App\Filament\Admin\Clusters\Master\Resources\RuanganResource\Pages;

use App\Filament\Admin\Clusters\Master\Resources\RuanganResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewRuangan extends ViewRecord
{
    protected static string $resource = RuanganResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
