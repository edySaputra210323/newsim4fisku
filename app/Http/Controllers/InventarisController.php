<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransaksionalInventaris;

class InventarisController extends Controller
{
    public function show($kode_inventaris)
    {
        $kode_inventaris = urldecode($kode_inventaris);
        $inventaris = TransaksionalInventaris::where('kode_inventaris', $kode_inventaris)->first();
        
        if (!$inventaris) {
            return response()->json([
                'message' => 'Inventaris tidak ditemukan',
                'kode_inventaris' => $kode_inventaris,
                'database_query' => TransaksionalInventaris::where('kode_inventaris', $kode_inventaris)->toSql()
            ], 404);
        }
        
        return view('inventaris.show', compact('inventaris'));
    }
}
