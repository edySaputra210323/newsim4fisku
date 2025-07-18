<?php

namespace App\Filament\Admin\Resources\RiwayatKelasResource\Pages;

use App\Filament\Admin\Resources\RiwayatKelasResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRiwayatKelas extends EditRecord
{
    protected static string $resource = RiwayatKelasResource::class;

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
