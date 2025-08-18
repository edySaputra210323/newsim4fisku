<?php

namespace App\Models;

use App\Models\Pegawai;
use App\Models\Ruangan;
use App\Models\Semester;
use App\Models\Suplayer;
use App\Models\TahunAjaran;
use App\Models\KategoriBarang;
use App\Models\SumberAnggaran;
use App\Models\KategoriInventaris;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TransaksionalInventaris extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'transaksional_inventaris';

    protected $fillable = [
        'kode_inventaris',
        'no_urut_barang',
        'kategori_inventaris_id',
        'suplayer_id',
        'kategori_barang_id',
        'sumber_anggaran_id',
        'ruang_id',
        'nama_inventaris',
        'merk_inventaris',
        'material_bahan',
        'kondisi',
        'tanggal_beli',
        'jumlah_beli',
        'harga_satuan',
        'total_harga',
        'keterangan',
        'foto_inventaris',
        'nota_beli',
        'jenis_penggunaan',
        'pegawai_id',
        'th_ajaran_id',
        'semester_id',
    ];

    protected $casts = [
        'tanggal_beli' => 'date',
        'jumlah_beli' => 'integer',
        'harga_satuan' => 'integer',
        'total_harga' => 'integer',
    ];

    protected static function boot()
    {
                parent::boot();

            // Event deleting untuk menghapus file saat record dihapus
            static::deleting(function ($inventaris) {
                if ($inventaris->foto_inventaris) {
                    try {
                        Storage::disk('public')->delete($inventaris->foto_inventaris);
                        \Log::info("File foto inventaris dihapus: {$inventaris->foto_inventaris}");
                    } catch (\Exception $e) {
                        \Log::warning("Gagal menghapus file foto inventaris: {$inventaris->foto_inventaris}, Error: {$e->getMessage()}");
                    }
                }

                if ($inventaris->nota_beli) {
                    try {
                        Storage::disk('public')->delete($inventaris->nota_beli);
                        \Log::info("File nota beli dihapus: {$inventaris->nota_beli}");
                    } catch (\Exception $e) {
                        \Log::warning("Gagal menghapus file nota beli: {$inventaris->nota_beli}, Error: {$e->getMessage()}");
                    }
                }
            });

            // Event updating untuk menghapus file lama hanya jika ada file baru
            static::updating(function ($inventaris) {
                if ($inventaris->isDirty('foto_inventaris') && $inventaris->getOriginal('foto_inventaris')) {
                    try {
                        Storage::disk('public')->delete($inventaris->getOriginal('foto_inventaris'));
                        \Log::info("File foto inventaris lama dihapus: {$inventaris->getOriginal('foto_inventaris')}");
                    } catch (\Exception $e) {
                        \Log::warning("Gagal menghapus file foto inventaris lama: {$inventaris->getOriginal('foto_inventaris')}, Error: {$e->getMessage()}");
                    }
                }

                if ($inventaris->isDirty('nota_beli') && $inventaris->getOriginal('nota_beli')) {
                    try {
                        Storage::disk('public')->delete($inventaris->getOriginal('nota_beli'));
                        \Log::info("File nota beli lama dihapus: {$inventaris->getOriginal('nota_beli')}");
                    } catch (\Exception $e) {
                        \Log::warning("Gagal menghapus file nota beli lama: {$inventaris->getOriginal('nota_beli')}, Error: {$e->getMessage()}");
                    }
                }
            });
    }

    public function kategoriInventaris()
    {
        return $this->belongsTo(KategoriInventaris::class);
    }

    public function suplayer()
    {
        return $this->belongsTo(Suplayer::class);
    }


    public function pengguna()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }

    public function kategoriBarang()
    {
        return $this->belongsTo(KategoriBarang::class);
    }

    public function sumberAnggaran()
    {
        return $this->belongsTo(SumberAnggaran::class);
    }

    public function ruang()
    {
        return $this->belongsTo(Ruangan::class);
    }

    public function thAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }  
    
    // Helper method untuk menghasilkan base kode inventaris (opsional)
    public static function generateBaseKodeInventaris($kategoriBarangId, $ruangId, $tanggalBeli)
    {
        // Ambil kode kategori barang
        $kategoriBarang = KategoriBarang::find($kategoriBarangId);
        $kodeKategori = $kategoriBarang ? $kategoriBarang->kode_kategori_barang : 'XXX';

        // Ambil kode ruangan
        $ruangan = Ruangan::find($ruangId);
        $kodeRuangan = $ruangan ? $ruangan->kode_ruangan : 'XXX';

        // Ambil tahun dari tanggal_beli
        $tahun = $tanggalBeli ? date('Y', strtotime($tanggalBeli)) : date('Y');

        // Ambil nomor urut terakhir
        $lastRecord = self::withTrashed()->count();
        $nomorUrut = str_pad($lastRecord + 1, 3, '0', STR_PAD_LEFT);

        // Kode unit sekolah (hardcoded)
        $kodeUnit = 'SMP';

        return sprintf('%s/%s/%s/%s/%s', $nomorUrut, $kodeUnit, $kodeKategori, $kodeRuangan, $tahun);
    }
}
