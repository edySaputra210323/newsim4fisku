<?php

namespace App\Models;

use App\Models\Ruangan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Gedung extends Model
{
    use SoftDeletes;

    protected $table = 'gedung';

    protected $fillable = [
        'nama_gedung',
        'kode_gedung',
        'deskripsi_gedung',
    ];

    public function ruangans()
    {
        return $this->hasMany(Ruangan::class);
    }
}
