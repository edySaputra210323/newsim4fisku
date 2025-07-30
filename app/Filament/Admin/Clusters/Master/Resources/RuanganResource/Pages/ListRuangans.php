<?php

namespace App\Filament\Admin\Clusters\Master\Resources\RuanganResource\Pages;

use App\Filament\Admin\Clusters\Master\Resources\RuanganResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRuangans extends ListRecords
{
    protected static string $resource = RuanganResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
