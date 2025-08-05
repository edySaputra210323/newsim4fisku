<!DOCTYPE html>
<html>
<head>
    <title>Detail Inventaris</title>
</head>
<body>
    <h1>{{ $inventaris->nama_inventaris }}</h1>
    <p><strong>Kode:</strong> {{ $inventaris->kode_inventaris }}</p>
    <p><strong>Merk:</strong> {{ $inventaris->merk_inventaris }}</p>
    <p><strong>Ruang:</strong> {{ $inventaris->ruang->nama_ruangan ?? '-' }}</p>
    <p><strong>Tanggal Beli:</strong> {{ $inventaris->tanggal_beli->format('d/m/Y') }}</p>
    <p><strong>Total Harga:</strong> Rp {{ number_format($inventaris->total_harga, 0, ',', '.') }}</p>
    <p><strong>Keterangan:</strong> {{ $inventaris->keterangan }}</p>
</body>
</html>
