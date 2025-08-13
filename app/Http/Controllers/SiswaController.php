<?php

namespace App\Http\Controllers;

use App\Models\DataSiswa; // Ganti jadi App\Models\Siswa jika model direname
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class SiswaController extends Controller
{
    /**
     * Menampilkan detail siswa berdasarkan NIS (public view).
     *
     * @param string $nis
     * @return \Illuminate\View\View
     */
    public function show($nis)
    {
        $siswa = DataSiswa::where('nis', $nis)->firstOrFail();
        return view('siswa.show', compact('siswa'));
    }

    /**
     * Generate PDF QR code untuk siswa terpilih (bulk atau single).
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function generateQrPdf(Request $request)
    {
        $ids = explode(',', $request->ids);
        $records = DataSiswa::whereIn('id', $ids)->get();

        $pdf = Pdf::loadView('siswa.qr_bulk_pdf', compact('records')) // Gunakan nama yang sama untuk konsistensi
        ->setOption('isRemoteEnabled', true)
        ->setOption('isHtml5ParserEnabled', true)
        ->setOption('isPhpEnabled', true);

        return $pdf->download('label-siswa-qr.pdf');
    }

    // public function generateQrPdfBack(Request $request)
    // {
    // $ids = explode(',', $request->ids);
    // $records = DataSiswa::whereIn('id', $ids)->get();

    // $pdf = Pdf::loadView('siswa.qr_bulk_pdf_back', compact('records'))
    //     ->setPaper('a4', 'portrait') // Sama seperti sisi depan
    //     ->setOption('isRemoteEnabled', true)
    //     ->setOption('isHtml5ParserEnabled', true)
    //     ->setOption('dpi', 300) // biar background tajam
    //     ->setOption('defaultFont', 'sans-serif');

    // return $pdf->download('label-siswa-qr-back.pdf');
    // }

    // public function generateQrPdfWithBack(Request $request)
    // {
    // $ids = explode(',', $request->ids);
    // $records = DataSiswa::whereIn('id', $ids)->get();

    // // Render sisi depan
    // $htmlFront = view('siswa.qr_bulk_pdf', compact('records'))->render();

    // // Render sisi belakang
    // $htmlBack = view('siswa.qr_bulk_pdf_back', compact('records'))->render();

    // // Gabungkan HTML dengan page break agar sisi belakang muncul di halaman berikutnya
    // $combinedHtml = $htmlFront . '<div style="page-break-before: always;"></div>' . $htmlBack;

    // $pdf = Pdf::loadHTML($combinedHtml)
    //     ->setOption('isRemoteEnabled', true)
    //     ->setOption('isHtml5ParserEnabled', true)
    //     ->setOption('dpi', 300)
    //     ->setOption('defaultFont', 'sans-serif');

    // return $pdf->download('kartu-pelajar-depan-belakang.pdf');
    // }
}