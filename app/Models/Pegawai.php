<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Pegawai extends Model
{
    protected $table = 'pegawai';

    protected $fillable = [
        'user_id',
        'nik',
        'nm_pegawai',
        'tempat_lahir',
        'tgl_lahir',
        'jenis_kelamin',
        'alamat',
        'phone',
        'tgl_mulai_bekerja',
        'nuptk',
        'npy',
        'status',
        'foto_pegawai',
    ];

    protected $casts = [
        'tgl_lahir' => 'date',
        'jenis_kelamin' => 'string',
    ];

// Boot method untuk handle event
protected static function boot()
{
    parent::boot();
    // Event deleting
    static::deleting(function ($pegawai) {
        // Hapus file foto dari storage
        if ($pegawai->foto_pegawai) {
            try {
                Storage::disk('public')->delete($pegawai->foto_pegawai);
                \Log::info("File foto pegawai dihapus: {$pegawai->foto_pegawai}");
            } catch (\Exception $e) {
                \Log::warning("Gagal menghapus file foto pegawai: {$pegawai->foto_pegawai}, Error: {$e->getMessage()}");
            }
        }

        // Hapus user terkait (jika ada)
        if ($pegawai->user) {
            $pegawai->user->delete();
        }
    });
}

    public function getTempatTanggalLahirAttribute()
    {
    // Atur locale Carbon ke Indonesia
    Carbon::setLocale('id');

    // Format tanggal dengan bulan penuh
    $formattedDate = $this->tgl_lahir ? $this->tgl_lahir->translatedFormat('d F Y') : '';
    $tempatLahir = $this->tempat_lahir ?? '';

    return $tempatLahir && $formattedDate ? $tempatLahir . ', ' . $formattedDate : ($tempatLahir ?: $formattedDate);
    }

    // Accessor untuk foto_pegawai
    public function getFotoPegawaiUrlAttribute()
    {
        return $this->foto_pegawai ? Storage::url($this->foto_pegawai) : asset('images/no_pic.jpg');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke SuratMasuk (untuk created_by dan updated_by)
    public function createdSuratMasuk()
    {
        return $this->hasMany(SuratMasuk::class, 'created_by');
    }

    public function updatedSuratMasuk()
    {
        return $this->hasMany(SuratMasuk::class, 'updated_by');
    }

    // Relasi ke SuratMasuk (untuk pegawai_id)
    public function suratMasuk()
    {
        return $this->hasMany(SuratMasuk::class, 'pegawai_id');
    }

    public function posisiKepegawaian()
    {
        return $this->hasMany(PosisiKepegawaian::class);
    }

    public function pendidikan()
    {
        return $this->hasMany(PendidikanPegawai::class);
    }

    public function sertifikasi()
    {
        return $this->hasMany(SertifikasiPegawai::class);
    }

    public function training()
    {
        return $this->hasMany(TrainingPegawai::class);
    }

    public function inventaris()
    {
        return $this->hasMany(TransaksionalInventaris::class);
    }
}
