<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TransaksionalInventaris extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'transaksional_inventaris';

    protected $fillable = [
        'kode_inventaris',
        'no_urut_barang',
        'kategori_inventaris_id',
        'suplayer_id',
        'kategori_barang_id',
        'sumber_anggaran_id',
        'ruang_id',
        'nama_inventaris',
        'merk_inventaris',
        'material_bahan',
        'kondisi',
        'tanggal_beli',
        'jumlah_beli',
        'harga_satuan',
        'total_harga',
        'keterangan',
        'foto_inventaris',
        'nota_beli',
        'th_ajaran_id',
        'semester_id',
    ];

    protected $casts = [
        'tanggal_beli' => 'date',
        'jumlah_beli' => 'integer',
        'harga_satuan' => 'integer',
        'total_harga' => 'integer',
    ];

    public function kategoriInventaris()
    {
        return $this->belongsTo(KategoriInventaris::class);
    }

    public function suplayer()
    {
        return $this->belongsTo(Suplayer::class);
    }

    public function kategoriBarang()
    {
        return $this->belongsTo(KategoriBarang::class);
    }

    public function sumberAnggaran()
    {
        return $this->belongsTo(SumberAnggaran::class);
    }

    public function ruang()
    {
        return $this->belongsTo(Ruang::class);
    }

    public function thAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }   
}
