<?php

namespace App\Imports;

use Log;
use App\Models\Kelas;
use App\Models\Pegawai;
use App\Models\DataSiswa;
use App\Models\TahunAjaran;
use App\Models\Semester;
use App\Models\RiwayatKelas;
use App\Models\RiwayatKelasImportFailed;
use Illuminate\Support\Collection;
use Filament\Notifications\Notification;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class RiwayatKelasImportProcessor implements ToCollection, WithHeadingRow
{
    protected $requiredHeaders = ['nis', 'kelas', 'walas'];

    public function collection(Collection $rows)
    {
        // Validasi tahun ajaran dan semester aktif
        $activeTahunAjaran = TahunAjaran::where('status', true)->first();
        $activeSemester = $activeTahunAjaran
            ? Semester::where('th_ajaran_id', $activeTahunAjaran->id)->where('status', true)->first()
            : null;

        if (!$activeTahunAjaran || !$activeSemester) {
            Notification::make()
                ->title('Gagal Impor Data Rombel')
                ->body('Tidak ada tahun ajaran atau semester aktif. Aktifkan terlebih dahulu.')
                ->danger()
                ->send();
            return;
        }

        // Validasi header
        $headers = array_keys($rows->first()->toArray());
        $missingHeaders = array_diff($this->requiredHeaders, $headers);
        if (!empty($missingHeaders)) {
            $quotedHeaders = array_map(fn($header) => "'$header'", $missingHeaders);
            Notification::make()
                ->title('Gagal Impor Data Rombel')
                ->body('Tidak dapat menemukan header ' . implode(', ', $quotedHeaders) . ' pada file Excel')
                ->danger()
                ->send();
            return;
        }

        // Ambil data master
        $kelasMap = Kelas::select('id', 'nama_kelas')->get()->toArray();
        $pegawaiMap = Pegawai::select('id', 'nm_pegawai')
            ->whereNotNull('nm_pegawai') // Pastikan nm_pegawai tidak null
            ->get()
            ->toArray();
        $siswaMap = DataSiswa::select('id', 'nis')->get()->toArray();

        // Log data untuk debugging
        \Log::info('Pegawai Map: ' . json_encode($pegawaiMap));
        \Log::info('Kelas Map: ' . json_encode($kelasMap));
        \Log::info('Siswa Map: ' . json_encode($siswaMap));

        // Kumpulkan data valid dan error
        $validRows = [];
        $errors = [];

        foreach ($rows as $keyIndex => $row) {
            if (array_filter($row->toArray()) === []) {
                continue;
            }

            $index = $keyIndex + 2;
            $errorMessage = null;

            // Log row untuk debugging
            \Log::info("Processing row {$index}: " . json_encode($row->toArray()));

            // Validasi NIS
            $siswa_id = null;
            if (in_array($row['nis'], [null, '', '-', '#N/A'])) {
                $errorMessage = 'NIS tidak boleh kosong';
            } else {
                $siswa_id = $this->searchInArray($siswaMap, 'nis', trim($row['nis']));
                if (!$siswa_id) {
                    $errorMessage = "NIS [{$row['nis']}] tidak ditemukan di database siswa";
                } else {
                    // Cek apakah siswa sudah terdaftar di riwayat_kelas untuk tahun ajaran dan semester aktif
                    $existingRiwayat = RiwayatKelas::where('data_siswa_id', $siswa_id)
                        ->where('tahun_ajaran_id', $activeTahunAjaran->id)
                        ->where('semester_id', $activeSemester->id)
                        ->exists();
                    if ($existingRiwayat) {
                        $errorMessage = "Siswa dengan NIS [{$row['nis']}] sudah terdaftar di rombel untuk tahun ajaran dan semester ini";
                    }
                }
            }

            // Validasi kelas
            $kelas_id = null;
            if (in_array($row['kelas'], [null, '', '-', '#N/A'])) {
                if ($errorMessage) $errorMessage .= ", ";
                $errorMessage .= 'Kelas tidak boleh kosong';
            } else {
                $kelas_id = $this->searchInArray($kelasMap, 'nama_kelas', trim($row['kelas']));
                if (!$kelas_id) {
                    if ($errorMessage) $errorMessage .= ", ";
                    $errorMessage .= "Kelas [{$row['kelas']}] tidak ditemukan di database kelas";
                }
            }

            // Validasi wali kelas
            $guru_id = null;
            if (in_array($row['walas'], [null, '', '-', '#N/A'])) {
                if ($errorMessage) $errorMessage .= ", ";
                $errorMessage .= 'Wali kelas tidak boleh kosong';
            } else {
                $guru_id = $this->searchInArray($pegawaiMap, 'nm_pegawai', trim($row['walas']));
                if (!$guru_id) {
                    if ($errorMessage) $errorMessage .= ", ";
                    $errorMessage .= "Wali kelas [{$row['walas']}] tidak ditemukan di database pegawai";
                }
            }

            // Simpan data ke array
            $rowData = [
                'data_siswa_id' => $siswa_id,
                'kelas_id' => $kelas_id,
                'pegawai_id' => $guru_id,
                'tahun_ajaran_id' => $activeTahunAjaran->id,
                'semester_id' => $activeSemester->id,
                'nis' => $row['nis'],
                'kelas' => $row['kelas'],
                'walas' => $row['walas'],
                'catatan_gagal' => $errorMessage ? "Error pada baris ke-$index: $errorMessage" : null,
            ];

            if ($errorMessage) {
                $errors[$index] = $rowData;
            } else {
                $validRows[$index] = $rowData;
            }
        }

        // Jika ada error, simpan ke riwayat_kelas_import_failed
        if (!empty($errors)) {
            foreach ($errors as $index => $rowData) {
                RiwayatKelasImportFailed::updateOrCreate(
                    [
                        'nis' => $rowData['nis'],
                        'tahun_ajaran_id' => $activeTahunAjaran->id,
                        'semester_id' => $activeSemester->id,
                    ],
                    $rowData
                );
            }

            Notification::make()
                ->title('Gagal Impor Data Rombel')
                ->body('Impor dibatalkan karena ada data error. Perbaiki data di Excel dan impor ulang.')
                ->danger()
                ->send();
            return;
        }

        // Jika semua valid, simpan ke riwayat_kelas
        foreach ($validRows as $rowData) {
            RiwayatKelas::create([
                'data_siswa_id' => $rowData['data_siswa_id'],
                'kelas_id' => $rowData['kelas_id'],
                'pegawai_id' => $rowData['pegawai_id'],
                'tahun_ajaran_id' => $rowData['tahun_ajaran_id'],
                'semester_id' => $rowData['semester_id'],
                'created_by' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        Notification::make()
            ->title('SISTEM')
            ->body('Data rombel berhasil diimpor')
            ->success()
            ->send();
    }

    protected function searchInArray(array $data, string $searchKey, string $searchValue): ?int
    {
        foreach ($data as $item) {
            if (isset($item[$searchKey]) && $item[$searchKey] === trim($searchValue)) {
                return $item['id'];
            }
        }
        return null;
    }
}