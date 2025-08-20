<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Statistik extends Model
{
    protected $table = 'statistik';
    protected $fillable = ['keterangan', 'jumlah'];
    public $timestamps = false;
}
