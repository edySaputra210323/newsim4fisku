<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Suplayer extends Model
{
    use SoftDeletes;
    protected $table = 'suplayer';

    protected $fillable = [
        'nama_suplayer',
        'alamat_suplayer',
        'no_telp_suplayer',
    ];
}
