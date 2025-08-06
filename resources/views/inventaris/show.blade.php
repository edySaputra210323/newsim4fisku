<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Inventaris</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            border: 1px solid #ccc;
            padding: 20px;
            border-radius: 5px;
        }
        h1 {
            font-size: 24px;
            color: #2c3e50;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table td {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        table td:first-child {
            font-weight: bold;
            width: 30%;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Detail Inventaris</h1>
        <table>
            <tr><td>Kode Inventaris</td><td>{{ $inventaris->kode_inventaris }}</td></tr>
            <tr><td>Nama</td><td>{{ $inventaris->nama_inventaris }}</td></tr>
            <tr><td>Ruang</td><td>{{ $inventaris->ruang->nama_ruangan ?? 'N/A' }}</td></tr>
            <tr><td>Tanggal Beli</td><td>{{ $inventaris->tanggal_beli->format('d/m/Y') }}</td></tr>
        </table>
    </div>
</body>
</html>