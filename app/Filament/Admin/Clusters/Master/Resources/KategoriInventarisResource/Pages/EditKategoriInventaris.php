<?php

namespace App\Filament\Admin\Clusters\Master\Resources\KategoriInventarisResource\Pages;

use App\Filament\Admin\Clusters\Master\Resources\KategoriInventarisResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKategoriInventaris extends EditRecord
{
    protected static string $resource = KategoriInventarisResource::class;

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
