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
    
            /* Background PNG */
            background-image: url('{{ public_path("background_kartu.png") }}');
            background-size: cover; /* Penuhi kartu */
            background-position: center; /* Posisikan di tengah */
            background-repeat: no-repeat; /* Jangan diulang */
        }
        .header {
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: row; /* Ubah ke column agar school-info di bawah logo */
            align-items: center; /* Sejajarkan secara horizontal ke tengah */
            background: rgba(253, 253, 253, 0.85);
            color: #0188ff;
            border-radius: 6px;
            border: 1px solid #83808027;
        }

        .logo {
            display: inline-block;
            align-items: center;
            justify-content: center; /* Sejajarkan logo ke tengah */
        }

        .logo img {
            padding-right: 5px;
            padding-left: 8px;
            padding-top: 10px;
            padding-bottom: 0;
            height: 50px;
            width: 50px;
        }

        .school-info {
            align-items: center;
            display: inline-block;
            margin: 0; /* Beri jarak atas agar tidak terlalu rapat */
            padding: 0;
            text-align: center; /* Teks judul dan alamat ditengah */
        }

        .school-info h1,
        .school-info p {
            margin: 0; /* hapus semua margin default */
            padding: 0;
        }

        .school-info h1 {
            font-size: 14px;
            font-weight: bold;
            line-height: 1; /* rapatkan */
        }

        .school-info p {
            font-size: 8px;
            line-height: 1;
        }
        .content {
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
            margin: 10px 0 10px 0;
            font-size: 10px;
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
            background: rgba(255,255,255,0.5); /* Biar teks kaki terbaca */
        }
        .ttd {
            height: 40px;
            width: 40px;
            margin-top: -18px;
            margin-bottom: -20px;
        }
        .qrcode {
            position: absolute;
            bottom: 4px;
            right: 4px;
            width: 1.5cm;
            height: 1.5cm;
            background: #fff; /* Supaya QR tetap jelas terbaca */
            padding: 2px;
            border-radius: 4px;
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
            <div class="logo">
                <img src="{{ public_path('images/copIdcard.png') }}" alt="Logo">
            </div>
            <div class="school-info">
                <h1>SMPIT AL-FITYAN KUBU RAYA</h1>
                <p>Jl. Raya Sungai Kakap Pal 7, Desa Pal Sembilan  <br> Kec. Sungai Kakap Kab. Kubu Raya</p>
            </div>
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
            <img src="{{ public_path('ttdkepsek.png') }}" class="ttd" alt="Tanda Tangan">
            <p><strong>Heru Purwanto, S.Pd.</strong></p>
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
