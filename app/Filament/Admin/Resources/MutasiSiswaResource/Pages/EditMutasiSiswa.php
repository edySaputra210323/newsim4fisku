<?php

namespace App\Filament\Admin\Resources\MutasiSiswaResource\Pages;

use Filament\Actions;
use App\Models\DataSiswa;
use App\Models\StatusSiswa;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Admin\Resources\MutasiSiswaResource;

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

    protected function afterSave(): void
    {
        // Ambil record MutasiSiswa yang baru diedit
        $record = $this->record;

        // Jika tipe_mutasi adalah "Keluar", ubah status siswa menjadi "Pindah"
        if ($record->tipe_mutasi === 'Keluar') {
            // Cari ID status "Pindah" dari tabel status_siswa
            $statusPindah = StatusSiswa::where('status', 'Pindah')->first();

            // Jika status "Pindah" ditemukan, update status_id di data_siswa
            if ($statusPindah) {
                DataSiswa::where('id', $record->data_siswa_id)
                    ->update(['status_id' => $statusPindah->id]);
            } else {
                // Notifikasi jika status "Pindah" tidak ditemukan
                Notification::make()
                    ->title('Error')
                    ->body('Status "Pindah" tidak ditemukan di tabel status_siswa.')
                    ->danger()
                    ->send();
            }
        } else {
            // Jika tipe_mutasi adalah "Masuk", kembalikan status siswa ke "Aktif" (opsional)
            $statusAktif = StatusSiswa::where('status', 'Aktif')->first();
            if ($statusAktif) {
                DataSiswa::where('id', $record->data_siswa_id)
                    ->update(['status_id' => $statusAktif->id]);
            }
        }
    }
}
