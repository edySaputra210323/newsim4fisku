<?php

namespace App\Filament\Admin\Resources\RiwayatKelasResource\Pages;

use App\Filament\Admin\Resources\RiwayatKelasResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRiwayatKelas extends ListRecords
{
    protected static string $resource = RiwayatKelasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
