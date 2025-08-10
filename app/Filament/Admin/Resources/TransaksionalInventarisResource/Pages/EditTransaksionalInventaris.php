<?php

namespace App\Filament\Admin\Resources\TransaksionalInventarisResource\Pages;

use Filament\Actions;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Admin\Resources\TransaksionalInventarisResource;

class EditTransaksionalInventaris extends EditRecord
{
    protected static string $resource = TransaksionalInventarisResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $record = $this->getRecord();

        // Foto Inventaris
        if (
            !empty($data['foto_inventaris']) &&
            $data['foto_inventaris'] !== $record->foto_inventaris
        ) {
            try {
                Storage::disk('public')->delete($record->foto_inventaris);
                \Log::info("File foto inventaris lama dihapus saat edit: {$record->foto_inventaris}");
            } catch (\Exception $e) {
                \Log::warning("Gagal menghapus file foto inventaris lama saat edit: {$record->foto_inventaris}, Error: {$e->getMessage()}");
            }
        } elseif (empty($data['foto_inventaris'])) {
            // Tidak upload baru → pertahankan nilai lama
            $data['foto_inventaris'] = $record->foto_inventaris;
        }
    
        // Nota Beli
        if (
            !empty($data['nota_beli']) &&
            $data['nota_beli'] !== $record->nota_beli
        ) {
            try {
                Storage::disk('public')->delete($record->nota_beli);
                \Log::info("File nota beli lama dihapus saat edit: {$record->nota_beli}");
            } catch (\Exception $e) {
                \Log::warning("Gagal menghapus file nota beli lama saat edit: {$record->nota_beli}, Error: {$e->getMessage()}");
            }
        } elseif (empty($data['nota_beli'])) {
            // Tidak upload baru → pertahankan nilai lama
            $data['nota_beli'] = $record->nota_beli;
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

    protected function getRedirectUrl(): string
    {
        Notification::make()
            ->title('Sukses')
            ->body('Transaksional inventaris berhasil diubah!')
            ->success()
            ->send();

        return $this->getResource()::getUrl('index');
    }
}
