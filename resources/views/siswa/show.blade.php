<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Detail Siswa - {{ $siswa->nama_siswa }}</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .container { max-width: 600px; margin: auto; }
        .foto { max-width: 150px; border-radius: 8px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 6px; border-bottom: 1px solid #ccc; }
        td:first-child { font-weight: bold; width: 30%; }
    </style>
</head>
<body>
<div class="container">
    <h2>Detail Siswa</h2>
    @if($siswa->foto_siswa)
        <img src="{{ asset('storage/'.$siswa->foto_siswa) }}" class="foto">
    @endif
    <table>
        <tr><td>Nama</td><td>{{ $siswa->nama_siswa }}</td></tr>
        <tr><td>NIS</td><td>{{ $siswa->nis }}</td></tr>
        <tr><td>NISN</td><td>{{ $siswa->nisn }}</td></tr>
        <tr><td>TTL</td><td>{{ $siswa->tempat_tanggal_lahir }}</td></tr>
        <tr><td>Alamat</td><td>{{ $siswa->alamat_lengkap }}</td></tr>
        <tr><td>Kontak</td><td>{!! nl2br(e($siswa->kontak)) !!}</td></tr>
    </table>
</div>
</body>
</html>
