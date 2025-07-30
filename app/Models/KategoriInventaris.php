<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KategoriInventaris extends Model
{
    use SoftDeletes;

    protected $table = 'kategori_inventaris';

    protected $fillable = [
        'nama_kategori_inventaris',
        'kode_kategori_inventaris',
        'deskripsi_kategori_inventaris',
    ];
}
