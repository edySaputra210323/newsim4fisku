<?php

namespace App\Http\Controllers;

use App\Models\DataSiswa; // Ganti jadi App\Models\Siswa jika model direname
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Dompdf\Dompdf;
use Dompdf\Options;

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

    public function generateQrPdfBack(Request $request)
    {
    $ids = explode(',', $request->ids);
    $records = DataSiswa::whereIn('id', $ids)->get();

    $pdf = Pdf::loadView('siswa.qr_bulk_pdf_back', compact('records'))
    ->setPaper('a4', 'portrait')
    ->setOption('isRemoteEnabled', true)
    ->setOption('isHtml5ParserEnabled', true)
    ->setOption('dpi', 300) // ini kunci biar tajam
    ->setOption('defaultFont', 'amiri');

    $dompdf = new Dompdf($options);
    $dompdf->loadHtml(view('siswa.qr_bulk_pdf_back', compact('records'))->render());
    $dompdf->render();
    $dompdf->stream('kartu.pdf', ['Attachment' => true]);

    return $dompdf->download('label-siswa-qr-back.pdf');
    }
}