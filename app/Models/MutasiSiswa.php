<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MutasiSiswa extends Model
{
    use SoftDeletes;
    protected $table = 'mutasi_siswa';
    protected $fillable = [
        'data_siswa_id',
        'tahun_ajaran_id',
        'semester_id',
        'kelas_id',
        'asal_sekolah',
        'sekolah_tujuan',
        'dokumen_mutasi',
        'nomor_mutasi_masuk',
        'nomor_mutasi_keluar',
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

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function getInfoMutasiAttribute(): string
{
    $tipe = strtolower($this->tipe_mutasi ?? '');

    if ($tipe === 'masuk') {
        $asal = $this->asal_sekolah ?: '-';
        $nomor = $this->nomor_mutasi_masuk ?: '-';
        return "{$asal} ({$nomor})";
    }

    if ($tipe === 'keluar') {
        $tujuan = $this->sekolah_tujuan ?: '-';
        $nomor = $this->nomor_mutasi_keluar ?: '-';
        return "{$tujuan} ({$nomor})";
    }

    return '-';
}

}
