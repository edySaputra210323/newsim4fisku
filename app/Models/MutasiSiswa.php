<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MutasiSiswa extends Model
{
    use SoftDeletes;
    protected $table = 'mutasi_siswa';
    protected $fillable = [
        'data_siswa_id',
        'tahun_ajaran_id',
        'semester_id',
        'tipe_mutasi',
        'tanggal_mutasi',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_mutasi' => 'date',
        'tipe_mutasi' => 'string',
    ];

    // Relasi ke DataSiswa
    public function dataSiswa()
    {
        return $this->belongsTo(DataSiswa::class, 'data_siswa_id');
    }

    // Relasi ke TahunAjaran
    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }

    // Relasi ke Semester
    public function semester()
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }

    // Relasi ke DataSiswa (pindah)
    public function dataSiswaPindah()
    {
        return $this->hasOne(DataSiswa::class, 'pindah_id');
    }
}
