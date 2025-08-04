<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barcode Labels</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .label {
            width: 200px;
            height: 200px;
            border: 1px solid #000;
            margin: 10px;
            padding: 10px;
            display: inline-block;
            vertical-align: top;
            text-align: center;
        }
        .barcode-img {
            max-width: 180px;
            max-height: 180px;
            height: auto;
        }
        .detail {
            font-size: 12px;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    @foreach($records as $record)
        <div class="label">
            <?php
                // Gunakan QrCode facade untuk menghasilkan QR Code
                $qrCode = QrCode::size(50)->generate($record->kode_inventaris);
                // Konversi ke base64 jika diperlukan (opsional, tergantung format)
                $qrCodeBase64 = 'data:image/svg+xml;base64,' . base64_encode($qrCode);
            ?>
            <img src="{{ $qrCodeBase64 }}" class="barcode-img">
            <div class="detail">
                <strong>Kode:</strong> {{ $record->kode_inventaris }}<br>
                <strong>Nama:</strong> {{ $record->nama_inventaris }}<br>
                <strong>Kategori:</strong> {{ $record->kategori_inventaris->nama_kategori_inventaris ?? 'N/A' }}<br>
                <strong>Ruang:</strong> {{ $record->ruangan->nama_ruangan ?? 'N/A' }}<br>
                <strong>Tanggal Beli:</strong> {{ $record->tanggal_beli->format('d/m/Y') }}<br>
                <strong>Harga:</strong> {{ number_format($record->total_harga, 0, ',', '.') }} IDR
            </div>
        </div>
    @endforeach
</body>
</html>