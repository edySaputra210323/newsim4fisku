<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Belakang Kartu Pelajar</title>
    <style>
        body {
            margin: 0;
            padding: 0;
        }
        .card {
            width: 8.6cm;
            height: 5.4cm;
            display: inline-block;
            margin: 5px;
            background-image: url("{{ str_replace('\\','/', public_path('images/idcardSiswa/idCardBack.png')) }}");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            page-break-inside: avoid;
        }
    </style>
</head>
<body>
@foreach($records as $siswa)
    <div class="card"></div>
@endforeach
</body>
</html>
