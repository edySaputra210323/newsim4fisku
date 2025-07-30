<?php

namespace App\Filament\Admin\Clusters\Master\Resources\SumberAnggaranResource\Pages;

use App\Filament\Admin\Clusters\Master\Resources\SumberAnggaranResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSumberAnggaran extends EditRecord
{
    protected static string $resource = SumberAnggaranResource::class;

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
