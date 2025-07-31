<?php

namespace App\Filament\Admin\Resources\SuplayerResource\Pages;

use App\Filament\Admin\Resources\SuplayerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSuplayers extends ListRecords
{
    protected static string $resource = SuplayerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
