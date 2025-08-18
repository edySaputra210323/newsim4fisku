<?php

use App\Models\DataSiswa;
use App\Models\MutasiSiswa;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\InventarisController;
use App\Http\Controllers\DataSiswaPublicController;

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

Route::get('/siswa/dokumen_mutasi/{record}', function ($record) {
    // Pastikan user sudah login
    if (!Auth::check()) {
        abort(403, 'Unauthorized');
    }

    // Ambil data mutasi
    $mutasi = MutasiSiswa::findOrFail($record);

    // Ambil path dokumen
    $filePath = $mutasi->dokumen_mutasi;

    // Cek apakah file ada di disk 'local'
    if (Storage::disk('local')->exists($filePath)) {
        return Storage::disk('local')->response($filePath);
    }

    // Jika file tidak ditemukan
    abort(404, 'File tidak ditemukan');
    })->name('siswa.dokumen_mutasi')->middleware('auth');

    Route::get('/inventaris/{kode_inventaris}', [InventarisController::class, 'show']);

    // Route::get('/siswa/cetak-qrcode-siswa', [DataSiswaPublicController::class, 'cetakQrcode'])
    // ->name('siswa.cetak_qrcode');
    Route::controller(SiswaController::class)->group(function () {
        Route::get('/siswa/{token}', 'show')->name('siswa.show');
        Route::post('/siswa/generate-qr-pdf', 'generateQrPdf')->name('siswa.generate_qr_pdf');
        Route::post('/siswa/generate-qr-pdf-back', 'generateQrPdfBack')->name('siswa.generate_qr_pdf_back');
    });