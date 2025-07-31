<?php

namespace App\Filament\Admin\Resources\SuplayerResource\Pages;

use App\Filament\Admin\Resources\SuplayerResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSuplayer extends ViewRecord
{
    protected static string $resource = SuplayerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
