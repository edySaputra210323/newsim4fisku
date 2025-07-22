<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Agenda Surat masuk</title>
    <style>
        body {
        font-family: Arial, sans-serif;
        font-size: 12px;
        margin: 20px;
    }

    .header {
        position: relative; /* Agar posisi absolute anak mengacu ke sini */
        height: 150px;
        margin-bottom: 2px;
    }

    .header img {
        position: absolute;
        left: 0;
        top: 0;
        width: 150px;
        height: 150px;
        object-fit: contain;
        margin-bottom: 2px;
    }

    .header-text {
        text-align: center;
        width: 100%;
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        font-family: 'Times New Roman', Times, serif;
        line-height: 1.5;
    }

    .header-text h1 {
        margin: 0;
        font-size: 25px;
        font-weight: bold;
    }

    .header-text h2 {
        margin: 0;
        font-size: 18px;
        font-weight: normal;
    }

    .header-placeholder {
        width: 120px;
    }

    hr {
        border: 0;
        border-top: 2px solid #000;
        margin: 0;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }

    table, th, td {
        border: 1px solid #000;
    }

    th, td {
        padding: 6px;
        text-align: left;
    }

    th {
        background-color: #f2f2f2;
        font-weight: bold;
        font-size: 12px;
        text-align: center;
    }

    .text-center {
        text-align: center;
    }

    .footer {
        margin-top: 40px;
        text-align: right;
    }

    .footer p {
        margin: 5px 0;
    }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('images/logoSMPIT.png') }}" alt="Logo SMPIT AFISKU">

        <div class="header-text">
            <h1>AGENDA SURAT MASUK</h1>
            <h1>UNIT SMPIT AL-FITYAN CABANG KUBU RAYA</h1>
            <h2>Tahun Ajaran: {{ $tahunAjaran->th_ajaran ?? 'Tidak Diketahui' }} - Semester {{ $semester->nm_semester ?? 'Tidak Diketahui' }}</h2>
        </div>

        <div class="header-placeholder"></div>
    </div>

    <hr>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="25%">Data Pengirim</th>
                <th width="10%">Tgl Terima</th>
                <th width="25%">No. Surat</th>
                <th width="10%">Tgl Surat</th>
                <th width="25%">Perihal</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($suratMasuks as $index => $surat)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $surat->nm_pengirim ?? '-' }}</td>
                    <td>{{ \Carbon\Carbon::parse($surat->tgl_terima)->format('d/m/Y') ?? '-' }}</td>
                    <td>{{ $surat->no_surat ?? '-' }}</td>
                    <td>{{ \Carbon\Carbon::parse($surat->tgl_surat)->format('d/m/Y') ?? '-' }}</td>
                    <td>{{ $surat->perihal ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Tidak ada data surat keluar untuk tahun ajaran ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer" style="margin-top: 60px; text-align: right;">
        <p>Dicetak pada: {{ \Carbon\Carbon::now()->format('d/m/Y') }}</p>
        <p>Kepala SMPIT Al-Fityan Kubu Raya</p>
        <br><br><br>
        <p style="border-bottom: 1px dotted #000; display: inline-block; padding-bottom: 5px;">
            Heru Purwanto, S.Pd.
        </p>
    </div>
</body>
</html>
