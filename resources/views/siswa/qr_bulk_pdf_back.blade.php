<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Belakang Kartu Pelajar</title>
    <style>
        body {
            font-family: Arial, serif;
            margin: 0;
            padding: 0;
        }
        .card {
            width: 8.6cm;
            height: 5.4cm;
    background-size: cover; /* samakan */
            border: 1px solid #000;
            border-radius: 6px;
            display: inline-block;
            margin: 5px;
            /* margin-bottom: 20px; ✅ jarak antar kartu secara vertikal */
            background-image: url("{{ public_path('images/idcardSiswa/idCardBack.png') }}");
            background-position: center;
            background-repeat: no-repeat;
            page-break-inside: avoid;
            overflow: hidden; /* 🔥 penting: biar background ikut radius */
        }
        .title {
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 3px;
            margin-top: 10px;
            color: rgb(75, 74, 73);
        }
        .vision {
            font-size: 12px;
            font-weight: bold;
            text-align: center;
            color: rgb(75, 74, 73);
            margin: 5px 10px;   /* kiri-kanan sama */
            text-align: center; /* bikin teks rata kiri-kanan */
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
            padding-top: 12px;
            color: rgb(75, 74, 73);
            margin: 5px 10px;   /* kiri-kanan sama */
            text-align: center; /* bikin teks rata kiri-kanan */
        }
        
    </style>
</head>
<body>
@foreach($records as $siswa)
    <div class="card">
        <div class="title">VISI</div>
        <div class="vision">
            “Terwujudnya Generasi yang Sholeh, Unggul Dalam Prestasi, Berakhlaqul Karimah dan Berwawasan Lingkungan”.
        </div>
        {{-- <div class="arabic">
            مَنْ سَلَكَ طَرِيقًا يَلْتَمِسُ فِيهِ عِلْمًا سَهَّلَ اللَّهُ لَهُ بِهِ طَرِيقًا إِلَى الْجَنَّةِ
        </div>  --}}
        <div class="hadith">
            “Barang siapa menempuh jalan untuk mencari ilmu, Allah akan memudahkan baginya jalan menuju surga.” <br>
            (HR. Muslim)
        </div>
        {{-- <div class="contact">
            <div class="contact-item">
                <img src="{{ public_path('images/idcardSiswa/email.png') }}" alt="Email" class="icon">
                <span class="contact-text">fityan.kuburaya@gmail.com</span>
            </div>
            <div class="contact-item">
                <img src="{{ public_path('images/idcardSiswa/phon.png') }}" alt="Phone" class="icon">
                <span class="contact-text"> +62 896-0407-0304</span>
            </div>
        </div> --}}

        <table style="width: 100%; margin-top: 10px; background-color: #cecece;">
            <tr>
                <!-- Kolom Kontak -->
                <td style="vertical-align: top; width: 70%; padding-left: 10px;">
                    <div style="display: flex; align-items: center; margin-bottom: 4px;">
                        <img src="{{ public_path('images/idcardSiswa/email.png') }}" alt="Email" style="width: 10px; height: 10px; margin-right: 5px;">
                        <span style="font-size: 12px; color: rgb(0, 0, 0); font-weight: bold; font-family: serif">fityan.kuburaya@gmail.com</span>
                    </div>
                    <div style="display: flex; align-items: center;">
                        <img src="{{ public_path('images/idcardSiswa/phon.png') }}" alt="Phone" style="width: 10px; height: 10px; margin-right: 5px;">
                        <span style="font-size: 12px; color: rgb(0, 0, 0); font-weight: bold; font-family: serif">+62 896-0407-0304</span>
                    </div>
                </td>
        
                <!-- Kolom Logo -->
                <td style="vertical-align: top; text-align: right; width: 30%;">
                    <img src="{{ public_path('images/logoSMPIT.png') }}"
                         alt="Logo" 
                         style="height: 60px; width: 60px; margin-right: 3px;">
                </td>
            </tr>
        </table>
    </div>
@endforeach


</body>
</html>
