<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KategoriBarang extends Model
{
    use SoftDeletes;

    protected $table = 'kategori_barang';

    protected $fillable = [
        'nama_kategori_barang',
        'kode_kategori_barang',
        'deskripsi_kategori_barang',
    ];
}
