<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\DataSiswa;
use App\Models\Kelas;
use App\Models\Pegawai;
use App\Models\TahunAjaran;
use App\Models\Semester;

class RiwayatKelasImportFailed extends Model
{
    protected $table = 'riwayat_kelas_import_faileds';

    protected $fillable = [
        'nis',
        'data_siswa_id',
        'kelas_id',
        'pegawai_id',
        'tahun_ajaran_id',
        'semester_id',
        'kelas',
        'walas',
        'catatan_gagal',
    ];

    public function dataSiswa()
    {
        return $this->belongsTo(DataSiswa::class, 'data_siswa_id');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function guru()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }
}