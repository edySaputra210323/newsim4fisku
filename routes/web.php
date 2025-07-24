<?php

use App\Models\DataSiswa;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

Route::get('/', function () {
    return redirect()->route('filament.admin.auth.login');
});

Route::get('/download-template-siswa', function () {
    return response()->download(storage_path('app/public/TemplateDataSiswa/template-siswa.xlsx'));
})->name('download.template.datasiswa');

Route::get('/download-template-riwayat-kelas', function () {
    return response()->download(storage_path('app/public/TemplateRiwayatKelas/template-riwayat-kelas.xlsx'));
})->name('download.template.riwayatkelas');


Route::get('/siswa/ijazah/{record}', function ($record) {
    $siswa = DataSiswa::findOrFail($record);
    // Pastikan pengguna memiliki akses (misalnya, hanya admin atau pengguna terkait)
    if (!Auth::check()) {
        abort(403, 'Unauthorized');
    }
    $filePath = $siswa->upload_ijazah_sd;
    if (Storage::disk('public')->exists($filePath)) {
        return Storage::disk('public')->response($filePath);
    }
    abort(404, 'File tidak ditemukan');
})->name('siswa.ijazah')->middleware('auth');
