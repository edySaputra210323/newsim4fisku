<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barcode Labels</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 10px;
        }
        .label {
            width: 200px;
            height: 250px;
            border: 2px solid #000;
            background-color: #fff;
            margin: 10px;
            padding: 10px;
            display: inline-block;
            vertical-align: top;
            text-align: center;
            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
        }
        .barcode-img {
            max-width: 150px;
            max-height: 150px;
            height: auto;
            margin-bottom: 10px;
        }
        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 10px;
        }
        .detail-table td {
            padding: 5px;
            text-align: left;
            border-bottom: 1px dashed #ccc;
        }
        .detail-table td:first-child {
            font-weight: bold;
            width: 50px;
            color: #333;
        }
        .detail-table td:last-child {
            width: 100px;
            color: #555;
        }
        .header {
            font-size: 10px;
            font-weight: bold;
            margin-bottom: 11px;
            color: #2c3e50;
        }
    </style>
</head>
<body>
    @foreach($records as $record)
        <div class="label">
            <div class="header">SMPIT AL-FITYAN KUBU RAYA</div>
            <?php
                // Tambahkan data lebih lengkap ke QR Code
                $qrData = 'Kode: ' . $record->kode_inventaris . "\n" .
                          'Nama: ' . $record->nama_inventaris . "\n" .
                          'Tanggal Beli: ' . $record->tanggal_beli->format('d/m/Y') . "\n" .
                          'Ruang: ' . ($record->ruang->nama_ruangan ?? 'N/A');
                // Gunakan format PNG untuk kompatibilitas lebih baik
                $qrCode = QrCode::size(150)->format('png')->encoding('UTF-8')->generate($qrData);
                $qrCodeBase64 = 'data:image/png;base64,' . base64_encode($qrCode);
            ?>
            <img src="{{ $qrCodeBase64 }}" class="barcode-img">
            <table class="detail-table">
                <tr><td colspan="2" style="text-align: center">{{ $record->kode_inventaris }}</td></tr>
                <tr><td>Nama:</td><td>{{ $record->nama_inventaris }}</td></tr>
                <tr><td>Ruang:</td><td>{{ $record->ruang->nama_ruangan ?? 'N/A' }}</td></tr>
                <tr><td>Tgl Beli:</td><td>{{ $record->tanggal_beli->format('d/m/Y') }}</td></tr>
            </table>
        </div>
    @endforeach
</body>
</html>