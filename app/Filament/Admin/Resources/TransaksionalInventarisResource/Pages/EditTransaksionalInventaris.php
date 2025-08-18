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
        // Ambil data record yang sudah ada
        $record = $this->getRecord();

       // Hapus foto inventaris lama jika ada file baru dan path lama tidak null
       if (
        !empty($data['foto_inventaris']) &&
        is_string($data['foto_inventaris']) &&
        !empty($record->foto_inventaris) &&
        $data['foto_inventaris'] !== $record->foto_inventaris
            ) {
                try {
                    Storage::disk('public')->delete($record->foto_inventaris);
                    Log::info("File foto inventaris lama dihapus saat edit: {$record->foto_inventaris}");
                } catch (\Exception $e) {
                    Log::warning("Gagal menghapus file foto inventaris lama: {$record->foto_inventaris}, Error: {$e->getMessage()}");
                }
            } elseif (empty($data['foto_inventaris'])) {
                // Jika user tidak upload file baru, tetap pakai file lama
                $data['foto_inventaris'] = $record->foto_inventaris;
            }

        // Hapus nota beli lama jika ada file baru dan path lama tidak null
        if (
            !empty($data['nota_beli']) &&
            is_string($data['nota_beli']) &&
            !empty($record->nota_beli) &&
            $data['nota_beli'] !== $record->nota_beli
        ) {
            try {
                Storage::disk('public')->delete($record->nota_beli);
                Log::info("File nota beli lama dihapus saat edit: {$record->nota_beli}");
            } catch (\Exception $e) {
                Log::warning("Gagal menghapus file nota beli lama: {$record->nota_beli}, Error: {$e->getMessage()}");
            }
        } elseif (empty($data['nota_beli'])) {
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
