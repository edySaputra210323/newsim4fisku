<?php

namespace App\Filament\Admin\Resources\RiwayatKelasResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Admin\Resources\RiwayatKelasResource;

class CreateRiwayatKelas extends CreateRecord
{
    protected static string $resource = RiwayatKelasResource::class;

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
}
