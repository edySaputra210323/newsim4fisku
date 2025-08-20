<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Transport;
use App\Models\NilaiSiswa;
use App\Models\JarakTempuh;
use App\Models\MutasiSiswa;
use App\Models\StatusSiswa;
use Illuminate\Support\Str;
use App\Models\RiwayatKelas;
use App\Models\PekerjaanOrtu;
use App\Models\PendidikanOrtu;
use App\Models\PenghasilanOrtu;
use App\Models\PrestasiPelanggaran;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;

class DataSiswa extends Model
{
    use SoftDeletes;
    protected $table = 'data_siswa';
    protected $fillable = [
        'nama_siswa',
        'nis',
        'nisn',
        'nik',
        'virtual_account',
        'no_hp',
        'email',
        'agama',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'negara',
        'provinsi',
        'kabupaten',
        'kecamatan',
        'kelurahan',
        'alamat',
        'rt',
        'rw',
        'kode_pos',
        'yatim_piatu',
        'penyakit',
        'jumlah_saudara',
        'anak_ke',
        'dari_bersaudara',
        'jarak_tempuh_id',
        'transport_id',
        'asal_sekolah',
        'npsn',
        'angkatan',
        'tanggal_masuk',
        'tanggal_keluar',
        'lanjut_sma_dimana',
        'status_id',
        'pindah_id',
        'dokumen_pendukung',
        'upload_ijazah_sd',
        'foto_siswa',
        'nm_ayah',
        'pendidikan_ayah_id',
        'pekerjaan_ayah_id',
        'penghasilan_ayah_id',
        'no_hp_ayah',
        'nm_ibu',
        'pendidikan_ibu_id',
        'pekerjaan_ibu_id',
        'penghasilan_ibu_id',
        'no_hp_ibu',
        'nm_wali',
        'pendidikan_wali_id',
        'pekerjaan_wali_id',
        'penghasilan_wali_id',
        'no_hp_wali',
        'user_id',
        'unit_id',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_masuk' => 'date',
        'tanggal_keluar' => 'date',
        'jenis_kelamin' => 'string',
    ];

    public function getTempatTanggalLahirAttribute()
    {
    // Atur locale Carbon ke Indonesia
    Carbon::setLocale('id');

    // Format tanggal dengan bulan penuh
    $formattedDate = $this->tanggal_lahir ? $this->tanggal_lahir->translatedFormat('d F Y') : '';
    $tempatLahir = $this->tempat_lahir ? ucfirst(strtolower($this->tempat_lahir)) : ''; // Ubah tempat_lahir ke lowercase

    return $tempatLahir && $formattedDate ? $tempatLahir . ', ' . $formattedDate : ($tempatLahir ?: $formattedDate);
    }

    public function getKontakAttribute()
    {
        $email = $this->email ?? '';
        $noHp = $this->no_hp ?? '';
        
        // Gabungkan email dan no_hp dengan pemisah baris baru untuk tampilan vertikal
        return $email && $noHp ? "$email\n$noHp" : ($email ?: $noHp);
    }

    public function getStatusAttribute(): string
    {
        return $this->UpdateStatusSiswa?->aktif  ? 'Aktif'  :
               ($this->UpdateStatusSiswa?->pindah ? 'Lulus' :
               ($this->UpdateStatusSiswa?->pindah ? 'Pindah' :
               ($this->UpdateStatusSiswa?->pindah ? 'Cuti' :
               ($this->UpdateStatusSiswa?->lulus  ? 'Drop Out'  : '-'))));
    }

    // Accessor untuk alamat
    public function getAlamatLengkapAttribute()
    {
        $parts = [];

        // Tambahkan alamat (jalan)
        if (!empty($this->alamat)) {
            $parts[] = strtolower($this->alamat);
        }

        // Tambahkan RT dan RW
        if (!empty($this->rt) && !empty($this->rw)) {
            $parts[] = strtolower("rt {$this->rt}. rw {$this->rw}");
        } elseif (!empty($this->rt)) {
            $parts[] = strtolower("rt {$this->rt}");
        } elseif (!empty($this->rw)) {
            $parts[] = strtolower("rw {$this->rw}");
        }

        // Tambahkan kelurahan
        if (!empty($this->kelurahan)) {
            $parts[] = strtolower($this->kelurahan);
        }

        // Tambahkan kecamatan
        if (!empty($this->kecamatan)) {
            $parts[] = strtolower("kec. {$this->kecamatan}");
        }

        // Tambahkan kabupaten
        if (!empty($this->kabupaten)) {
            $parts[] = strtolower(" {$this->kabupaten}");
        }

        // Tambahkan provinsi
        if (!empty($this->provinsi)) {
            $parts[] = strtolower($this->provinsi);
        }

        // Tambahkan kode pos
        if (!empty($this->kode_pos)) {
            $parts[] = strtolower("kode pos: {$this->kode_pos}");
        }

        // Gabungkan semua bagian dengan koma dan spasi
        return !empty($parts) ? implode(', ', $parts) : '-';
    }

    public function getStatusJumlahSaudaraAttribute()
    {
        $parts = [];

        if (!empty($this->anak_ke)) {
            $parts[] = "Anak ke {$this->anak_ke}";
        }

        if (!empty($this->jumlah_saudara)) {
            $parts[] = "dari {$this->jumlah_saudara} bersaudara";
        }

        return !empty($parts) ? implode(', ', $parts) : '-';
    }

    public function getAsalSekolahNpsnAttribute()
    {
        $parts = [];

        if (!empty($this->asal_sekolah)) {
            $parts[] = "{$this->asal_sekolah}";
        }

        if (!empty($this->npsn)) {
            $parts[] = "- {$this->npsn}";
        }

        return !empty($parts) ? implode(', ', $parts) : '-';
    }

    // Boot method untuk handle event
    protected static function boot()
    {
        parent::boot();

         // Saat membuat siswa baru, generate token unik
         static::creating(function ($siswa) {
            if (empty($siswa->token)) {
                $siswa->token = Str::uuid(); // bisa juga Str::random(12)
            }
        });
        
        // Event deleting
        static::deleting(function ($data_siswa) {
        // Hapus file foto dari storage
        if ($data_siswa->foto_siswa) {
            try {
                Storage::disk('public')->delete($data_siswa->foto_siswa);
                \Log::info("File foto pegawai dihapus: {$data_siswa->foto_siswa}");
            } catch (\Exception $e) {
                \Log::warning("Gagal menghapus file foto pegawai: {$data_siswa->foto_siswa}, Error: {$e->getMessage()}");
            }
        }

        // Hapus user terkait (jika ada)
        if ($data_siswa->user) {
            $data_siswa->user->delete();
        }
    });
}

    // Relasi ke JarakTempuh
    public function jarakTempuh()
    {
        return $this->belongsTo(JarakTempuh::class, 'jarak_tempuh_id');
    }

    // Relasi ke Transport
    public function transport()
    {
        return $this->belongsTo(Transport::class, 'transport_id');
    }

    // Relasi ke StatusSiswa
    public function UpdateStatusSiswa()
    {
        return $this->belongsTo(StatusSiswa::class, 'status_id');
    }

    // Relasi ke MutasiSiswa (pindah)
    public function pindah()
    {
        return $this->belongsTo(MutasiSiswa::class, 'pindah_id');
    }

    // Relasi ke PendidikanOrtu (ayah)
    public function pendidikanAyah()
    {
        return $this->belongsTo(PendidikanOrtu::class, 'pendidikan_ayah_id');
    }

    // Relasi ke PekerjaanOrtu (ayah)
    public function pekerjaanAyah()
    {
        return $this->belongsTo(PekerjaanOrtu::class, 'pekerjaan_ayah_id');
    }

    // Relasi ke PenghasilanOrtu (ayah)
    public function penghasilanAyah()
    {
        return $this->belongsTo(PenghasilanOrtu::class, 'penghasilan_ayah_id');
    }

    // Relasi ke PendidikanOrtu (ibu)
    public function pendidikanIbu()
    {
        return $this->belongsTo(PendidikanOrtu::class, 'pendidikan_ibu_id');
    }

    // Relasi ke PekerjaanOrtu (ibu)
    public function pekerjaanIbu()
    {
        return $this->belongsTo(PekerjaanOrtu::class, 'pekerjaan_ibu_id');
    }

    // Relasi ke PenghasilanOrtu (ibu)
    public function penghasilanIbu()
    {
        return $this->belongsTo(PenghasilanOrtu::class, 'penghasilan_ibu_id');
    }

    // Relasi ke PendidikanOrtu (wali)
    public function pendidikanWali()
    {
        return $this->belongsTo(PendidikanOrtu::class, 'pendidikan_wali_id');
    }

    // Relasi ke PekerjaanOrtu (wali)
    public function pekerjaanWali()
    {
        return $this->belongsTo(PekerjaanOrtu::class, 'pekerjaan_wali_id');
    }

    // Relasi ke PenghasilanOrtu (wali)
    public function penghasilanWali()
    {
        return $this->belongsTo(PenghasilanOrtu::class, 'penghasilan_wali_id');
    }

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi ke MutasiSiswa
    public function mutasiSiswa()
    {
        return $this->hasMany(MutasiSiswa::class, 'data_siswa_id');
    }

    // Relasi ke RiwayatKelas
    public function riwayatKelas()
    {
        return $this->hasMany(RiwayatKelas::class, 'data_siswa_id');
    }

    // Relasi ke PrestasiPelanggaran
    public function prestasiPelanggaran()
    {
        return $this->hasMany(PrestasiPelanggaran::class, 'data_siswa_id');
    }

    // Relasi ke NilaiSiswa
    public function nilaiSiswa()
    {
        return $this->hasMany(NilaiSiswa::class, 'data_siswa_id');
    }

    public function getNamaNisAttribute()
    {
        return "{$this->nama_siswa} - {$this->nis}";
    }

    public function scopeAktif($q)
    {
        return $q->whereHas('UpdateStatusSiswa', fn ($s) =>
            $s->whereRaw('LOWER(status) = ?', ['aktif'])
        );
    }

    public function scopePerempuan($q)
    {
        return $q->whereIn('jenis_kelamin', ['P', 'Perempuan']);
    }

    public function scopeLaki($q)
    {
        return $q->whereIn('jenis_kelamin', ['L', 'Laki-laki']);
    }
}
    
