<?php

namespace App\Filament\Admin\Resources\MutasiSiswaResource\Pages;

use Filament\Actions;
use App\Models\DataSiswa;
use App\Models\StatusSiswa;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Admin\Resources\MutasiSiswaResource;

class CreateMutasiSiswa extends CreateRecord
{
    protected static string $resource = MutasiSiswaResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Mengambil tahun ajaran yang aktif
        $activeTahunAjaran = cache()->remember('active_tahun_ajaran', now()->addMinutes(1), fn () => \App\Models\TahunAjaran::where('status', true)->first());
        if (!$activeTahunAjaran) {
            Notification::make()
                ->title('Error')
                ->body('Tidak ada tahun ajaran yang aktif. Silakan aktifkan tahun ajaran terlebih dahulu.')
                ->danger()
                ->send();
            return $data;
        }
       // Mengambil semester yang aktif
       $activeSemester = cache()->remember('active_semester', now()->addMinutes(1), fn () => \App\Models\Semester::where('status', true)->first());
       if (!$activeSemester) {
           Notification::make()
               ->title('Error')
               ->body('Tidak ada semester yang aktif. Silakan aktifkan semester terlebih dahulu.')
               ->danger()
               ->send();
           return $data;
       }

        // Menambahkan ID user yang sedang login ke field 'dibuat_oleh'
        $data['tahun_ajaran_id'] = $activeTahunAjaran->id;
        $data['semester_id'] = $activeSemester->id;
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        Notification::make()
            ->title('Sukses')
            ->body('Mutasi berhasil dibuat!')
            ->success()
            ->send();

        return $this->getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        // Ambil record MutasiSiswa yang baru dibuat
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
        }
    }
}
