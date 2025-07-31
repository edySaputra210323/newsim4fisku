<?php

namespace App\Filament\Admin\Clusters\Master\Resources\KategoriBarangResource\Pages;

use App\Filament\Admin\Clusters\Master\Resources\KategoriBarangResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKategoriBarang extends EditRecord
{
    protected static string $resource = KategoriBarangResource::class;

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
