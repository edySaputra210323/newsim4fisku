<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SumberAnggaran extends Model
{
    use SoftDeletes;
    protected $table = 'sumber_anggaran';

    protected $fillable = [
        'nama_sumber_anggaran',
    ];
}
