<?php

namespace App\Filament\Admin\Clusters\Master\Resources\GedungResource\Pages;

use App\Filament\Admin\Clusters\Master\Resources\GedungResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGedung extends EditRecord
{
    protected static string $resource = GedungResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
