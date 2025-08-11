<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Label QR Code Siswa</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 10px; }
        .label {
            width: 200px;
            border: 1px solid #000;
            background: #fff;
            margin: 10px;
            padding: 10px;
            display: inline-block;
            text-align: center;
            border-radius: 5px;
            page-break-inside: avoid;
        }
        img { max-width: 120px; margin-bottom: 10px; }
        .header { font-size: 10px; font-weight: bold; margin-bottom: 5px; }
    </style>
</head>
<body>
@foreach($records as $siswa)
    <div class="label">
        <div class="header">YAYASAN AL-FITYAN KUBU RAYA</div>
        @php
            $url = route('siswa.show', urlencode($siswa->nis));
            $qrCodeSvg = \QrCode::format('svg')->size(100)->generate($url); // Eksplisit format SVG untuk menghindari default lain
        @endphp
        <img src="data:image/svg+xml;base64,{{ base64_encode($qrCodeSvg) }}" alt="QR Code {{ $siswa->nis }}">
        <div>{{ $siswa->nis }}</div>
        <div>{{ $siswa->nama_siswa }}</div>
    </div>
@endforeach
</body>
</html>