<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RiwayatKelas extends Model
{
    use SoftDeletes;
    protected $table = 'riwayat_kelas';
    protected $fillable = [
        'data_siswa_id',
        'kelas_id',
        'guru_id',
        'tahun_ajaran_id',
        'semester_id',
        'status_id',
    ];

    // Relasi ke DataSiswa
    public function dataSiswa()
    {
        return $this->belongsTo(DataSiswa::class, 'data_siswa_id');
    }

    // Relasi ke Kelas
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    // Relasi ke Guru
    public function guru()
    {
        return $this->belongsTo(Guru::class, 'guru_id');
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

    // Relasi ke StatusSiswa
    public function status()
    {
        return $this->belongsTo(StatusSiswa::class, 'status_id');
    }

}
