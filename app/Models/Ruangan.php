<?php

namespace App\Models;

use App\Models\Gedung;
use Illuminate\Database\Eloquent\Model;

class Ruangan extends Model
{
    protected $table = 'ruangan';

    protected $fillable = [
        'gedung_id',
        'nama_ruangan',
        'kode_ruangan',
        'deskripsi_ruangan',
    ];

    public function gedung()
    {
        return $this->belongsTo(Gedung::class);
    }
}
