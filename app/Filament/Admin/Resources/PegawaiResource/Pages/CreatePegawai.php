<?php

namespace App\Filament\Admin\Resources\PegawaiResource\Pages;

use App\Filament\Admin\Resources\PegawaiResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class CreatePegawai extends CreateRecord
{
    protected static string $resource = PegawaiResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Validasi password dan konfirmasi sebelum membuat record
        if (isset($data['create_user_account']) && $data['create_user_account']) {
            if ($data['password'] !== $data['password_confirmation']) {
                throw ValidationException::withMessages([
                    'password_confirmation' => ['Konfirmasi kata sandi tidak cocok'],
                ]);
            }
            if (empty($data['username_pegawai'])) {
                throw ValidationException::withMessages([
                    'username_pegawai' => ['Username wajib diisi saat membuat akun pengguna'],
                ]);
            }
        }
        return $data;
    }

    protected function afterCreate(): void
    {
        $data = $this->form->getState();

        if (isset($data['create_user_account']) && $data['create_user_account']) {
            DB::transaction(function () use ($data) {
                // Buat user baru
                $user = User::create([
                    'name' => $data['nm_pegawai'],
                    'username' => $data['username_pegawai'], // Pastikan username diisi
                    'email' => $data['user_email'],
                    'password' => Hash::make($data['password']),
                ]);

                // Hubungkan ke pegawai
                $this->record->update(['user_id' => $user->id]);
            });
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
