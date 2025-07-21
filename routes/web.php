<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('filament.admin.auth.login');
});

Route::get('/download-template-siswa', function () {
    return response()->download(storage_path('app/public/TemplateDataSiswa/template-siswa.xlsx'));
})->name('download.template.datasiswa');

Route::get('/download-template-riwayat-kelas', function () {
    return response()->download(storage_path('app/public/TemplateRiwayatKelas/template-riwayat-kelas.xlsx'));
})->name('download.template.riwayatkelas');

// Route::get('/', function () {
//     return view('welcome');
// });
