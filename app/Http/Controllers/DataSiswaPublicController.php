<?php

namespace App\Http\Controllers;

use App\Models\DataSiswa;
use Illuminate\Http\Request;

class DataSiswaPublicController extends Controller
{
    public function show($nis)
    {
        $siswa = DataSiswa::where('nis', $nis)->firstOrFail();

        return view('siswa.public_show', compact('siswa'));
    }
}
