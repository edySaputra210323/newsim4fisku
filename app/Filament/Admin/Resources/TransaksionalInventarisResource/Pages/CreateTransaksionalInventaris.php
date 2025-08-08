<?php

namespace App\Filament\Admin\Resources\TransaksionalInventarisResource\Pages;

use App\Filament\Admin\Resources\TransaksionalInventarisResource;
use App\Models\KategoriBarang;
use App\Models\Ruangan;
use App\Models\TransaksionalInventaris;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateTransaksionalInventaris extends CreateRecord
{
    protected static string $resource = TransaksionalInventarisResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Mengambil tahun ajaran yang aktif
        $activeTahunAjaran = cache()->remember('active_tahun_ajaran', now()->addMinutes(60), fn () => \App\Models\TahunAjaran::where('status', true)->first());
        if (!$activeTahunAjaran) {
            Notification::make()
                ->title('Error')
                ->body('Tidak ada tahun ajaran yang aktif. Silakan aktifkan tahun ajaran terlebih dahulu.')
                ->danger()
                ->persistent()
                ->send();
            $this->halt(); // Hentikan proses
        }

        // Mengambil semester yang aktif
        $activeSemester = cache()->remember('active_semester', now()->addMinutes(60), fn () => \App\Models\Semester::where('th_ajaran_id', $activeTahunAjaran->id)->where('status', true)->first());
        if (!$activeSemester) {
            Notification::make()
                ->title('Error')
                ->body('Tidak ada semester yang aktif untuk tahun ajaran ini. Silakan aktifkan semester terlebih dahulu.')
                ->danger()
                ->persistent()
                ->send();
            $this->halt(); // Hentikan proses
        }

        // Menambahkan th_ajaran_id dan semester_id ke data
        $data['th_ajaran_id'] = $activeTahunAjaran->id;
        $data['semester_id'] = $activeSemester->id;

        return $data; // Selalu kembalikan array
    }

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        // Validasi input
        $jumlahBeli = (int) ($data['jumlah_beli'] ?: 1);
        $hargaSatuan = (int) str_replace(['.', 'Rp ', ','], '', $data['harga_satuan'] ?: '0');
        // $totalHarga = (int) str_replace(['.', 'Rp ', ','], '', $data['total_harga'] ?: '0');

        // Validasi batas
        if ($jumlahBeli < 1 || $jumlahBeli > 1000) {
            Notification::make()
                ->title('Error')
                ->body('Jumlah beli harus antara 1 dan 1000.')
                ->danger()
                ->persistent()
                ->send();
            $this->halt();
        }
        if ($hargaSatuan < 0 || $hargaSatuan > 1000000000) {
            Notification::make()
                ->title('Error')
                ->body('Harga satuan harus antara 0 dan Rp 1.000.000.000.')
                ->danger()
                ->persistent()
                ->send();
            $this->halt();
        }

        // Validasi relasi
        $kategoriBarang = KategoriBarang::find($data['kategori_barang_id']);
        if (!$kategoriBarang) {
            Notification::make()
                ->title('Error')
                ->body('Kategori barang tidak ditemukan.')
                ->danger()
                ->persistent()
                ->send();
            $this->halt();
        }
        $kodeKategori = $kategoriBarang->kode_kategori_barang;

        $ruangan = Ruangan::find($data['ruang_id']);
        if (!$ruangan) {
            Notification::make()
                ->title('Error')
                ->body('Ruangan tidak ditemukan.')
                ->danger()
                ->persistent()
                ->send();
            $this->halt();
        }
        $kodeRuangan = $ruangan->kode_ruangan;

        // Ambil tahun dari tanggal_beli
        $tahun = isset($data['tanggal_beli']) ? date('Y', strtotime($data['tanggal_beli'])) : date('Y');

        // Ambil nomor urut terakhir
        $lastRecord = TransaksionalInventaris::withTrashed()->count();
        if ($lastRecord >= 999) {
            Notification::make()
                ->title('Peringatan')
                ->body('Jumlah barang telah mencapai atau melebihi 999. Silakan atur ulang sistem atau perpanjang format nomor urut.')
                ->warning()
                ->persistent()
                ->send();
            $this->halt();
        }
        $nomorUrut = str_pad($lastRecord + 1, 4, '0', STR_PAD_LEFT);


        // Kode unit sekolah
        $kodeUnit = 'SMP';

        // Base kode inventaris
$baseKodeInventaris = sprintf('%s-%s-%s-%s-%s', $nomorUrut, $kodeUnit, $kodeKategori, $kodeRuangan, $tahun);

$records = [];

// Buat entri sebanyak jumlah_beli
for ($i = 1; $i <= $jumlahBeli; $i++) {
    // Tentukan kodeInventaris di dalam loop
    $kodeInventaris = $jumlahBeli == 1 ? $baseKodeInventaris : $baseKodeInventaris . '-' . $i;
    $records[] = TransaksionalInventaris::create([
        'kode_inventaris' => $kodeInventaris,
        'no_urut_barang' => $lastRecord + $i,
        'kategori_inventaris_id' => $data['kategori_inventaris_id'],
        'suplayer_id' => $data['suplayer_id'] ?? null,
        'kategori_barang_id' => $data['kategori_barang_id'],
        'sumber_anggaran_id' => $data['sumber_anggaran_id'] ?? null,
        'ruang_id' => $data['ruang_id'],
        'nama_inventaris' => $data['nama_inventaris'],
        'merk_inventaris' => $data['merk_inventaris'] ?? null,
        'material_bahan' => $data['material_bahan'] ?? null,
        'kondisi' => $data['kondisi'],
        'tanggal_beli' => $data['tanggal_beli'],
        'jumlah_beli' => $jumlahBeli,
        'harga_satuan' => $hargaSatuan,
        'total_harga' => $hargaSatuan, // Harga satuan per unit
        'keterangan' => $data['keterangan'] ?? null,
        'foto_inventaris' => $data['foto_inventaris'] ?? null,
        'nota_beli' => $data['nota_beli'] ?? null,
        'jenis_penggunaan' => $data['jenis_penggunaan'],
        'pegawai_id' => $data['pegawai_id'] ?? null,
        'th_ajaran_id' => $data['th_ajaran_id'],
        'semester_id' => $data['semester_id'],
    ]);
}

        return end($records);
    }

    protected function getRedirectUrl(): string
    {
        Notification::make()
            ->title('Sukses')
            ->body('Transaksional inventaris berhasil dibuat!')
            ->success()
            ->send();

        return $this->getResource()::getUrl('index');
    }
}