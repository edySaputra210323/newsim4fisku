<?php

namespace App\Http\Controllers;

use Mpdf\Mpdf;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\DataSiswa; // Ganti jadi App\Models\Siswa jika model direname

class SiswaController extends Controller
{
    /**
     * Menampilkan detail siswa berdasarkan token (public view).
     *
     * @param string $token
     * @return \Illuminate\View\View
     */
    public function show($token)
    {
        $siswa = DataSiswa::where('token', $token)->firstOrFail();
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