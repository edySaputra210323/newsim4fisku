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
            height: 250px; /* Tingkatkan tinggi untuk menampung detail */
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
            max-width: 150px; /* Ukuran QR Code lebih kecil agar ada ruang untuk detail */
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
            width: 50px; /* Lebar untuk label */
            color: #333;
        }
        .detail-table td:last-child {
            width: 100px; /* Lebar untuk nilai */
            color: #555555;
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
            $ip = '127.0.0.1'; // Ganti dengan IP lokal PC kamu
            $port = '8000'; // Sesuaikan dengan port Laravel kamu
            $url = "http://127.0.0.1:8000/inventaris/" . urlencode($record->kode_inventaris);
            $qrCode = QrCode::size(100)->generate($url);
            $qrCodeBase64 = 'data:image/svg+xml;base64,' . base64_encode($qrCode);
            // $url = env('APP_URL') . '/inventaris/' . urlencode($record->kode_inventaris);
            // $qrCode = QrCode::size(100)->generate($url);
            // $qrCodeBase64 = 'data:image/svg+xml;base64,' . base64_encode($qrCode);
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