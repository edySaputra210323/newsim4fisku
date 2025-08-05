<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransaksionalInventaris;

class InventarisController extends Controller
{
    public function show($kode_inventaris)
    {
        $inventaris = TransaksionalInventaris::where('kode_inventaris', $kode_inventaris)->firstOrFail();

        return view('inventaris.show', compact('inventaris'));
    }
}
