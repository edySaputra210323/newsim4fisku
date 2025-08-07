<?php

namespace App\Filament\Admin\Clusters\Master\Resources\RuanganResource\Pages;

use App\Filament\Admin\Clusters\Master\Resources\RuanganResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateRuangan extends CreateRecord
{
    protected static string $resource = RuanganResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

}
