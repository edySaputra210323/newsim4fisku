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

        .foto-siswa img,
        .logo img,
        .cap-stempel,
        .ttd {
            image-rendering: crisp-edges; /* ini bikin gambar tidak blur */
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
            background-image: url("{{ str_replace('\\','/', public_path('images/idcardSiswa/background_kartu.png')) }}");
            background-size: cover; /* Penuhi kartu */
            background-position: center; /* Posisikan di tengah */
            background-repeat: no-repeat; /* Jangan diulang */
            image-rendering: crisp-edges; /* jaga ketajaman */
        }
        .header {
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: row; /* Ubah ke column agar school-info di bawah logo */
            align-items: flex-start; /* teks dan logo sejajar bagian atas */
            background: rgba(253, 253, 253, 0.85);
            color: #0062d3;
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
            padding-top: 15px;
            padding-bottom: 0;
            height: 60px;
            width: 60px;
        }

        .school-info {
            align-items: center;
            display: inline-block;
            margin: 0; /* Beri jarak atas agar tidak terlalu rapat */
            padding: 0;
            text-align: center; /* Teks judul dan alamat ditengah */
            margin-top: -4px; /* naikkan teks tanpa mengganggu logo */
        }

        .school-info h1,
        .school-info p {
            margin: 0; /* hapus semua margin default */
            padding: 0;
        }

        .school-info h1 {
            margin-top: -22px; /* naik sedikit */ 
            font-size: 16px;
            font-weight: bold;
            line-height: 1; /* rapatkan */
        }

        .school-info p {
            margin-top: -28px; /* alamat ikut naik */
            font-size: 10px;
            line-height: 1;
        }
        .content {
            padding: 0px 4px 4px 4px;
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
            text-transform: uppercase;
            margin: 7px 0 10px 0;
            font-size: 10px;
        }
        .details p {
            margin: 0;
            font-size: 9px;
        }

        .footer {
    position: absolute;
    bottom: -5px; /* naikkan dari -18px ke 2px */
    left: 4px;
    right: 4px;
    text-align: center;
    font-size: 8px;
    background: transparent;
    padding-top: 2px;
}

.footer p {
    margin: 0; /* hilangkan margin default */
    line-height: 1.1;
    
}

.ttd-container {
    position: relative; /* jadi patokan posisi cap */
    display: inline-flex;
    gap: -20px;
    align-items: center;
    margin-top: -5px; /* naikkan tanda tangan dan cap */
}


.ttd {
    z-index: 1; /* Rendah, agar stempel (9999) tetap di atas */
    height: 40px;
    width: auto;
}

.cap-stempel {
    position: absolute;
    left: 0;
    top: 0; /* sejajar atas tanda tangan */
    height: 40px;
    width: auto;
    opacity: 0.90;
    z-index: 9999; /* pastikan lebih tinggi dari semua */
    pointer-events: none;
}

.footer p.nama-kepsek {
    position: relative;
    top: -10px; /* sedikit naikkan nama */
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
                <p>Jl. Raya Sungai Kakap Pal 7, Desa Pal Sembilan  <br> Kec. Sungai Kakap Kab. Kubu Raya 78381</p>
            </div>
        </div>

        {{-- Content --}}
        <div class="content">
            <table style="width:100%; border-collapse: collapse;">
                <tr>
                    <td style="width:2.5cm; vertical-align: top; padding-right:5px;">
                        <div class="photo">
                            <img 
                                src="{{ !empty($siswa->foto_siswa) && file_exists(public_path('storage/'.$siswa->foto_siswa))
                                    ? public_path('storage/'.$siswa->foto_siswa)
                                    : public_path('images/idcardSiswa/foto_default.png') 
                                }}" 
                                alt="Foto Siswa">
                        </div>
                    </td>
                    <td style="vertical-align: top;">
                        <p class="nama"><strong>{{ strtoupper($siswa->nama_siswa) }}</strong></p>
                        <table style="border-collapse: collapse; font-size:9px;">
                            <tr>
                                <td>NIS:</td>
                                <td>{{ $siswa->nis }}</td>
                                <td style="padding-left:10px;">NISN:</td>
                                <td>{{ $siswa->nisn }}</td>
                            </tr>
                        </table>
                        <p>TTL: {{ ucwords(strtolower($siswa->tempat_lahir)) }}, {{ \Carbon\Carbon::parse($siswa->tanggal_lahir)->format('d/m/Y') }}</p>
                    </td>
                </tr>
            </table>
        </div>
        

        {{-- Footer --}}
        <div class="footer">
            <p>Kepala Sekolah</p>
            <div class="ttd-container">
                <img src="{{ public_path('images/idcardSiswa/capstempel.png') }}" class="cap-stempel" alt="Cap Stempel">
                <img src="{{ public_path('images/idcardSiswa/ttdkepsek.png') }}" class="ttd" alt="Tanda Tangan">
            </div>
            <p class="nama-kepsek"><strong>Heru Purwanto, S.Pd.</strong></p>
        </div>

        {{-- QR Code --}}
        <div class="qrcode">
            @php
                $url = route('siswa.show', urlencode($siswa->token));
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
