<?php

namespace App\Filament\Admin\Clusters\Master\Resources\SemesterResource\Pages;

use App\Filament\Admin\Clusters\Master\Resources\SemesterResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSemesters extends ListRecords
{
    protected static string $resource = SemesterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
