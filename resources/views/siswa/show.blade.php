{{-- <!DOCTYPE html>
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
</html> --}}


<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Detail Siswa - {{ $siswa->nama_siswa }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: Arial, sans-serif; }
        .kop-sekolah { margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 15px; }
        .kop-sekolah img { max-height: 150px; margin-bottom: 10px; }
        .kop-sekolah h2 { margin: 0; font-weight: bold; font-size: 2rem; }
        .kop-sekolah p { margin: 0; font-size: 1rem; color: #555; }
        .judul-siswa { font-weight: bold; font-size: 1.6rem; margin-bottom: 15px; }
        .foto { max-width: 150px; border-radius: 8px; margin-bottom: 20px; }
        .border-divider { border-left: 2px solid #ccc; }
        .bg-siswa { background: #f8f9fa; padding: 20px; border-radius: 8px; height: 100%; }
    </style>
</head>
<body>
<div class="container-fluid px-4">

<!-- Kop Sekolah -->
<div class="kop-sekolah d-flex flex-column flex-md-row align-items-center justify-content-center text-center text-md-start">
    <!-- Logo kiri / atas -->
    <div class="me-md-3 mb-3 mb-md-0">
        <img src="{{ asset('images/logoSMPIT.png') }}" alt="Logo Sekolah" class="img-fluid" style="max-height:150px;">
    </div>

    <!-- Nama & alamat kanan / bawah -->
    <div>
        <h2 class="fw-bold mb-1">Sekolah Menengah Pertama Islam Terpadu Al-Fityan Kubu Raya</h2>
        <p class="mb-0">
            Jl. Raya Sungai Kakap Pal 7, Desa Pal Sembilan, Kecamatan Sungai Kakap<br>
            Kabupaten Kubu Raya, Kalimantan Barat, Kode Pos 78381
        </p>
    </div>
</div>

    <div class="row">
        <!-- Judul & Foto Siswa -->
        <div class="col-md-4">
            <div class="bg-siswa text-center">
                <div class="judul-siswa">DATA SISWA</div>
                @if($siswa->foto_siswa)
                    <img src="{{ asset('storage/'.$siswa->foto_siswa) }}" class="foto">
                @endif
            </div>
        </div>

        <!-- Data Siswa -->
        <div class="col-md-8 border-divider">
            <div class="table-responsive ps-3">
                <table class="table table-striped w-100">
                    <tr><th>Nama</th><td>{{ $siswa->nama_siswa }}</td></tr>
                    <tr><th>NIS</th><td>{{ $siswa->nis }}</td></tr>
                    <tr><th>NISN</th><td>{{ $siswa->nisn }}</td></tr>
                    <tr><th>TTL</th><td>{{ $siswa->tempat_tanggal_lahir }}</td></tr>
                    <tr><th>Alamat</th><td>{{ $siswa->alamat_lengkap }}</td></tr>
                    <tr><th>Kontak</th><td>{!! nl2br(e($siswa->kontak)) !!}</td></tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            @php
                                $status = $siswa->UpdateStatusSiswa->status ?? null;
                            @endphp

                            @if($status === 'Aktif')
                                <span class="badge bg-success">{{ $status }}</span>
                            @elseif($status === 'Lulus')
                                <span class="badge bg-warning text-dark">{{ $status }}</span>
                            @elseif(is_null($status))
                                <span class="badge bg-secondary">Tidak Ada Status</span>
                            @else
                                <span class="badge bg-danger">{{ $status }}</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Garis Pemisah -->
    <hr class="my-4">

    <!-- Disclaimer -->
    <div class="alert alert-warning">
        <strong>Disclaimer:</strong> Data ini hanya digunakan untuk keperluan resmi sekolah. 
        Segala bentuk penyalahgunaan, pemalsuan, atau penggandaan informasi tanpa izin 
        akan dikenakan sanksi sesuai dengan ketentuan hukum yang berlaku.
    </div>

</div>
</body>
</html>




