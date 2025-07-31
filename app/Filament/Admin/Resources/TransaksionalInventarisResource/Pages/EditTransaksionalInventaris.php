<?php

namespace App\Filament\Admin\Resources\TransaksionalInventarisResource\Pages;

use App\Filament\Admin\Resources\TransaksionalInventarisResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTransaksionalInventaris extends EditRecord
{
    protected static string $resource = TransaksionalInventarisResource::class;

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
