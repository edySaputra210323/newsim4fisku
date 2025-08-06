<?php

namespace App\Filament\Admin\Resources\TransaksionalInventarisResource\Pages;

use App\Filament\Admin\Resources\TransaksionalInventarisResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class EditTransaksionalInventaris extends EditRecord
{
    protected static string $resource = TransaksionalInventarisResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Ambil data record yang sudah ada
        $record = $this->getRecord();

        // Jika ada file baru untuk foto_inventaris, hapus file lama
        if (isset($data['foto_inventaris']) && $record->foto_inventaris) {
            try {
                Storage::disk('public')->delete($record->foto_inventaris);
                \Log::info("File foto inventaris lama dihapus saat edit: {$record->foto_inventaris}");
            } catch (\Exception $e) {
                \Log::warning("Gagal menghapus file foto inventaris lama saat edit: {$record->foto_inventaris}, Error: {$e->getMessage()}");
            }
        }

        // Jika ada file baru untuk nota_beli, hapus file lama
        if (isset($data['nota_beli']) && $record->nota_beli) {
            try {
                Storage::disk('public')->delete($record->nota_beli);
                \Log::info("File nota beli lama dihapus saat edit: {$record->nota_beli}");
            } catch (\Exception $e) {
                \Log::warning("Gagal menghapus file nota beli lama saat edit: {$record->nota_beli}, Error: {$e->getMessage()}");
            }
        }

        return $data;
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
