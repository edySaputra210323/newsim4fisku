<?php

namespace App\Models;

use App\Models\Ruangan;
use Illuminate\Database\Eloquent\Model;

class Gedung extends Model
{
    protected $table = 'gedung';

    protected $fillable = [
        'nama_gedung',
        'kode_gedung',
        'deskripsi_gedung',
    ];

    public function ruangan()
    {
        return $this->hasMany(Ruangan::class);
    }
}
