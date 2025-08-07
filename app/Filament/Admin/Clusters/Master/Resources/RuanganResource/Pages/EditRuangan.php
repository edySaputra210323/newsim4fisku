<?php

namespace App\Filament\Admin\Clusters\Master\Resources\RuanganResource\Pages;

use App\Filament\Admin\Clusters\Master\Resources\RuanganResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRuangan extends EditRecord
{
    protected static string $resource = RuanganResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

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
