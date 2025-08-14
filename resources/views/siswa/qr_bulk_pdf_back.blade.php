<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Belakang Kartu Pelajar</title>
    <style>
        body {
            font-family: 'Amiri', serif;
            margin: 0;
            padding: 0;
        }
        .card {
            width: 8.6cm;
            height: 5.4cm;
            background-size: 8.6cm 5.4cm;
            display: inline-block;
            margin: 5px;
            background-image: url("{{ public_path('images/idcardSiswa/idCardBack.png') }}");
            background-position: center;
            background-repeat: no-repeat;
            page-break-inside: avoid;
        }
        .title {
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 3px;
            margin-top: 10px;
            color: rgb(245, 200, 0);
        }
        .vision {
            font-size: 14px;
            text-align: center;
            margin-bottom: 5px;
            color: rgb(245, 200, 0);
            margin: 5px 10px 5px 10px;
        }
        /* .arabic {
            margin: 5px 5px 5px 5px;
            color: #faea06;
            font-family: 'Amiri', serif;
            font-size: 12px;
            direction: rtl;
            text-align: center;
        } */
        .hadith {
            font-size: 12px;
            text-align: center;
            font-style: italic;
            margin-bottom: 5px;
            color: rgb(245, 200, 0);
            margin: 5px 10px 5px 10px;
        }
        .contact {
            font-size: 8px;
            margin-top: 8px;
            color: rgb(245, 200, 0);
        }
        .contact div {
            display: flex;
            align-items: center;
            margin-bottom: 2px;
        }
        .icon {
            width: 10px;
            height: 10px;
            margin-right: 4px;
        }
    </style>
</head>
<body>
@foreach($records as $siswa)
    <div class="card">
        <div class="title">VISI</div>
        <div class="vision">
            “Membangun generasi yang beriman, menguasai ilmu pengetahuan dan mampu menghadapi tantangan zaman”.
        </div>
        {{-- <div class="arabic">
            مَنْ سَلَكَ طَرِيقًا يَلْتَمِسُ فِيهِ عِلْمًا سَهَّلَ اللَّهُ لَهُ بِهِ طَرِيقًا إِلَى الْجَنَّةِ
        </div>  --}}
        <div class="hadith">
            “Barang siapa menempuh jalan untuk mencari ilmu, Allah akan memudahkan baginya jalan menuju surga.” <br>
            (HR. Muslim)
        </div>
        <div class="contact">
            <div>
                <img src="{{ public_path('images/idcardSiswa/email.png') }}" alt="Email" class="icon">
                fityan.kuburaya@gmail.com
            </div>
        </div>
    </div>
@endforeach


</body>
</html>
