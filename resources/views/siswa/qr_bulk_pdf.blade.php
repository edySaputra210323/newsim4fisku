<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Kartu Pelajar</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .card {
            width: 8.6cm;
            height: 5.4cm;
            border: 1px solid #000;
            border-radius: 6px;
            overflow: hidden;
            display: inline-block;
            margin: 5px;
            position: relative;
            page-break-inside: avoid;
        }
        .header {
            display: flex;
            align-items: center;
            padding: 4px;
            background: #2e31f8;
            color: #fcf6f6;
        }
        .header img {
            height: 18px;
            margin-right: 5px;
        }
        .header h1 {
            font-size: 10px;
            margin: 0;
            line-height: 1.2;
        }
        .content {
            display: flex;
            flex-direction: row;
            align-items: flex-start; /* Pastikan mulai dari atas foto */
            padding: 4px;
            font-size: 9px;
        }
        .photo {
    width: 2.5cm;
    height: 3.2cm;
    border: 1px solid #ccc;
    background: #fff;
}
.photo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}
.nama {
    margin: 0 0 10px 0;
    font-size: 10px;
}
        .details {
            display: flex;
            flex-direction: column;
            justify-content: flex-start; /* Pastikan isi mulai dari atas */
            vertical-align: top; /* Untuk PDF rendering */
        }
        .details .nama {
            margin: 0 0 4px 0; /* Jarak bawah sedikit dari nama */
        }
        .details .id-row {
            display: flex;
            gap: 10px; /* Jarak antara NIS dan NISN */
            margin-bottom: 4px;
        }
        .details p {
            margin: 0;
            font-size: 9px;
        }
        .footer {
            position: absolute;
            bottom: 4px;
            left: 4px;
            right: 4px;
            text-align: center;
            font-size: 8px;
        }
        .ttd {
            height: 15px;
            margin-top: -5px;
        }
        .qrcode {
            position: absolute;
            bottom: 4px;
            right: 4px;
            width: 1.5cm;
            height: 1.5cm;
        }
        .qrcode img {
            width: 100%;
            height: auto;
        }
    </style>
</head>
<body>
@foreach($records as $siswa)
    <div class="card">
        {{-- Header --}}
        <div class="header">
            <img src="{{ public_path('logo_sekolah.png') }}" alt="Logo">
            <h1>SMPIT AL-FITYAN KUBU RAYA</h1>
        </div>

        {{-- Content --}}
        <div class="content">
            <table style="width:100%; border-collapse: collapse;">
                <tr>
                    <td style="width:2.5cm; vertical-align: top; padding-right:5px;">
                        <div class="photo">
                            <img src="{{ public_path('foto_siswa/'.$siswa->foto_siswa) }}" alt="Foto Siswa">
                        </div>
                    </td>
                    <td style="vertical-align: top;">
                        <p class="nama"><strong>{{ $siswa->nama_siswa }}</strong></p>
                        <table style="border-collapse: collapse; font-size:9px;">
                            <tr>
                                <td>NIS:</td>
                                <td>{{ $siswa->nis }}</td>
                                <td style="padding-left:10px;">NISN:</td>
                                <td>{{ $siswa->nisn }}</td>
                            </tr>
                        </table>
                        <p>TTL: {{ $siswa->tempat_lahir }}, {{ \Carbon\Carbon::parse($siswa->tanggal_lahir)->format('d/m/Y') }}</p>
                    </td>
                </tr>
            </table>
        </div>
        

        {{-- Footer --}}
        <div class="footer">
            <p>Kepala Sekolah</p>
            <img src="{{ public_path('ttd_kepsek.png') }}" class="ttd" alt="Tanda Tangan">
            <p><strong>Nama Kepala Sekolah</strong></p>
        </div>

        {{-- QR Code --}}
        <div class="qrcode">
            @php
                $url = route('siswa.show', urlencode($siswa->nis));
                $qrCodeSvg = base64_encode(
                    QrCode::format('svg')->size(80)->generate($url)
                );
            @endphp
            <img src="data:image/svg+xml;base64,{{ $qrCodeSvg }}">
        </div>
    </div>
@endforeach
</body>
</html>
