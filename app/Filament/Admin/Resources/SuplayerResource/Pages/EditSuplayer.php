<?php

namespace App\Filament\Admin\Resources\SuplayerResource\Pages;

use App\Filament\Admin\Resources\SuplayerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSuplayer extends EditRecord
{
    protected static string $resource = SuplayerResource::class;

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
