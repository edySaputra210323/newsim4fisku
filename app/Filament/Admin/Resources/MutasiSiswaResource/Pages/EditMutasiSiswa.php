<?php

namespace App\Filament\Admin\Resources\MutasiSiswaResource\Pages;

use App\Filament\Admin\Resources\MutasiSiswaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMutasiSiswa extends EditRecord
{
    protected static string $resource = MutasiSiswaResource::class;

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
