<?php

namespace App\Models;

use App\Models\Gedung;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ruangan extends Model
{
    use SoftDeletes;

    protected $table = 'ruangan';

    protected $fillable = [
        'gedung_id',
        'nama_ruangan',
        'lantai',
        'kode_ruangan',
        'deskripsi_ruangan',
    ];

    public function gedung()
    {
        return $this->belongsTo(Gedung::class);
    }
}
