<?php

namespace App\Imports;

use Log;
use Carbon\Carbon;
use App\Models\Unit;
use App\Models\DataSiswa;
use App\Models\Transport;
use App\Models\JarakTempuh;
use App\Models\StatusSiswa;
use App\Models\PekerjaanOrtu;
use App\Models\PendidikanOrtu;
use App\Models\PenghasilanOrtu;
use App\Models\SiswaImportFailed;
use Illuminate\Support\Collection;
use Filament\Notifications\Notification;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SiswaImportProcessor implements ToCollection, WithHeadingRow
{
    protected $requiredHeaders = [
        'status', 'nik', 'no_virtual_account', 'nama_siswa', 'jenis_kelamin', 'email',
        'no_hp', 'nis', 'nisn', 'tempat_lahir', 'tanggal_lahir', 'agama', 'alamat',
        'rt', 'rw', 'kabupaten_kota', 'kecamatan', 'desa_lurah', 'transportasi',
        'asal_sekolah', 'npsn', 'yatim_piatu', 'jarak_rumah', 'waktu_tempuh', 'jumlah_saudara', 'anak_ke',
        'dari_bersaudara', 'nama_ayah', 'pendidikan_ayah', 'pekerjaan_ayah',
        'penghasilan_ayah', 'no_hp_ayah', 'nama_ibu', 'pendidikan_ibu',
        'pekerjaan_ibu', 'penghasilan_ibu', 'no_hp_ibu', 'nama_wali',
        'pendidikan_wali', 'pekerjaan_wali', 'penghasilan_wali', 'no_hp_wali',
        'unit', 'angkatan', 'tanggal_masuk'
    ];

    public function collection(Collection $rows)
    {
        // Validasi header
        $headers = array_keys($rows->first()->toArray());
        $missingHeaders = array_diff($this->requiredHeaders, $headers);
        if (!empty($missingHeaders)) {
            $quotedHeaders = array_map(fn($header) => "'$header'", $missingHeaders);
            Notification::make()
                ->title('Gagal Impor Data Siswa')
                ->body('Tidak dapat menemukan header ' . implode(', ', $quotedHeaders) . ' pada file Excel')
                ->danger()
                ->send();
            return;
        }

        // Ambil data master
        $jarakTempuhMap = JarakTempuh::select('id', 'nama_jarak_tempuh')->get()->toArray();
        $transportMap = Transport::select('id', 'nama_transport')->get()->toArray();
        $statusMap = StatusSiswa::select('id', 'status')->get()->toArray();
        $pendidikanMap = PendidikanOrtu::select('id', 'jenjang_pendidikan')->get()->toArray();
        $pekerjaanMap = PekerjaanOrtu::select('id', 'nama_pekerjaan')->get()->toArray();
        $penghasilanMap = PenghasilanOrtu::select('id', 'penghasilan')->get()->toArray();
        $unitMap = Unit::select('id', 'nm_unit', 'kode_unit')->get()->toArray();

        // Kumpulkan data valid dan error
        $validRows = [];
        $errors = [];
        $existingVas = [];

        foreach ($rows as $keyIndex => $row) {
            if (array_filter($row->toArray()) === []) {
                continue;
            }

            $index = $keyIndex + 2;
            $errorMessage = null;

            // Validasi unit
            $unit_id = null;
            if (!in_array($row['unit'], [null, '', '-', '#N/A'])) {
                $unit_id = $this->searchInArray($unitMap, 'nm_unit', $row['unit']);
                if (!$unit_id) {
                    if ($errorMessage) $errorMessage .= ", ";
                    $errorMessage .= 'Unit [' . $row['unit'] . '] tidak ditemukan di DATA MASTER';
                } else {
                    $kode_unit = null;
                    foreach ($unitMap as $unit) {
                        if ($unit['id'] === $unit_id) {
                            $kode_unit = $unit['kode_unit'];
                            break;
                        }
                    }
                    if (!$kode_unit || !preg_match('/^\d{2}$/', $kode_unit)) {
                        if ($errorMessage) $errorMessage .= ", ";
                        $errorMessage .= 'Kode unit untuk [' . $row['unit'] . '] tidak valid atau belum diatur';
                    }
                }
            } else {
                if ($errorMessage) $errorMessage .= ", ";
                $errorMessage .= 'Unit tidak boleh kosong';
            }

            // Validasi no_virtual_account
            if (in_array($row['no_virtual_account'], [null, '', '-', '#N/A'])) {
                if ($errorMessage) $errorMessage .= ", ";
                $errorMessage .= 'No Virtual Account tidak boleh kosong';
            } else {
                $existingSiswa = DataSiswa::where('virtual_account', $row['no_virtual_account'])->first();
                if ($existingSiswa) {
                    if ($errorMessage) $errorMessage .= ", ";
                    $errorMessage .= "No Virtual Account [{$row['no_virtual_account']}] sudah ada di database dengan NIS [{$existingSiswa->nis}]. Hapus data lama atau isi NIS di Excel dengan [{$existingSiswa->nis}]";
                    $existingVas[$row['no_virtual_account']] = $existingSiswa->nis;
                }
            }

            // Validasi dan generate NIS
            $nis = null;
            if (!in_array($row['nis'], [null, '', '-', '#N/A'])) {
                if (!preg_match('/^\d{2}\d{5}$/', $row['nis'])) {
                    if ($errorMessage) $errorMessage .= ", ";
                    $errorMessage .= 'NIS [' . $row['nis'] . '] tidak valid, harus 7 digit angka (2 digit kode unit + 5 digit urutan)';
                } else {
                    $nisExists = DataSiswa::where('nis', $row['nis'])
                        ->where('virtual_account', '!=', $row['no_virtual_account'])
                        ->exists() || SiswaImportFailed::where('nis', $row['nis'])
                        ->where('virtual_account', '!=', $row['no_virtual_account'])
                        ->exists();
                    if ($nisExists) {
                        if ($errorMessage) $errorMessage .= ", ";
                        $errorMessage .= 'NIS [' . $row['nis'] . '] sudah ada di database';
                    } else {
                        $nis = $row['nis'];
                    }
                }
            } else {
                $failedImport = SiswaImportFailed::where('virtual_account', $row['no_virtual_account'])->first();
                if ($failedImport && $failedImport->nis && preg_match('/^\d{2}\d{5}$/', $failedImport->nis)) {
                    $nis = $failedImport->nis;
                    Log::info("Reusing NIS {$nis} from SiswaImportFailed for no_virtual_account: {$row['no_virtual_account']}");
                } else {
                    $nis = $this->generateNis($unit_id, $unitMap);
                    if (!$nis) {
                        if ($errorMessage) $errorMessage .= ", ";
                        $errorMessage .= 'Gagal generate NIS otomatis';
                    }
                }
            }

            // Validasi nama_siswa
            if (in_array($row['nama_siswa'], [null, '', '-', '#N/A'])) {
                if ($errorMessage) $errorMessage .= ", ";
                $errorMessage .= 'Nama siswa tidak boleh kosong';
            }

            // Validasi jenis_kelamin
            if (!in_array($row['jenis_kelamin'], ['L', 'P'])) {
                if ($errorMessage) $errorMessage .= ", ";
                $errorMessage .= 'Jenis kelamin [' . $row['jenis_kelamin'] . '] tidak valid, harus L atau P';
            }

            // Validasi email
            if (!in_array($row['email'], [null, '', '-', '#N/A'])) {
                $emailExists = DataSiswa::where('email', $row['email'])
                    ->where('virtual_account', '!=', $row['no_virtual_account'])
                    ->exists();
                if ($emailExists) {
                    if ($errorMessage) $errorMessage .= ", ";
                    $errorMessage .= 'Email [' . $row['email'] . '] sudah ada di database';
                }
            }

            // Validasi nisn
            $nisn = $row['nisn'] ?? null;
            if (!in_array($nisn, [null, '', '-', '#N/A']) && DataSiswa::where('nisn', $nisn)
                ->where('virtual_account', '!=', $row['no_virtual_account'])
                ->exists()) {
                if ($errorMessage) $errorMessage .= ", ";
                $errorMessage .= 'NISN [' . $nisn . '] sudah ada di database';
            }

        // === VALIDASI TANGGAL LAHIR ===
        $tanggal_lahir = null;
        if (in_array($row['tanggal_lahir'], [null, '', '-', '#N/A'])) {
            if ($errorMessage) $errorMessage .= ", ";
            $errorMessage .= 'Tanggal lahir tidak boleh kosong';
        } else {
            $tanggal_lahir_raw = trim($row['tanggal_lahir']);

            if (is_numeric($tanggal_lahir_raw)) {
                try {
                    $carbonDate = Carbon::instance(Date::excelToDateTimeObject($tanggal_lahir_raw));
                    $tanggal_lahir = $carbonDate->format('Y-m-d');
                } catch (\Exception $e) {
                    if ($errorMessage) $errorMessage .= ", ";
                    $errorMessage .= 'Format tanggal lahir [' . $tanggal_lahir_raw . '] tidak valid, harus d/m/Y (contoh: 28/10/1993)';
                }
            } else {
                $formats = ['d/m/Y', 'd-m-Y', 'd.m.Y', 'Y-m-d'];
                $parsed = false;
                foreach ($formats as $format) {
                    try {
                        $tanggal_lahir = Carbon::createFromFormat($format, $tanggal_lahir_raw)->format('Y-m-d');
                        $parsed = true;
                        break;
                    } catch (\Exception $e) {
                        // lanjut
                    }
                }
                if (!$parsed) {
                    if ($errorMessage) $errorMessage .= ", ";
                    $errorMessage .= 'Format tanggal lahir [' . $tanggal_lahir_raw . '] tidak valid, harus d/m/Y (contoh: 28/10/1993)';
                }
            }
        }

        // === VALIDASI TANGGAL MASUK ===
        $tanggal_masuk = null;
        if (in_array($row['tanggal_masuk'], [null, '', '-', '#N/A'])) {
            if ($errorMessage) $errorMessage .= ", ";
            $errorMessage .= 'Tanggal masuk tidak boleh kosong';
        } else {
            $tanggal_masuk_raw = trim($row['tanggal_masuk']);

            if (is_numeric($tanggal_masuk_raw)) {
                try {
                    $carbonDate = Carbon::instance(Date::excelToDateTimeObject($tanggal_masuk_raw));
                    $tanggal_masuk = $carbonDate->format('Y-m-d');
                } catch (\Exception $e) {
                    if ($errorMessage) $errorMessage .= ", ";
                    $errorMessage .= 'Format tanggal masuk [' . $tanggal_masuk_raw . '] tidak valid, harus d/m/Y (contoh: 28/10/1993)';
                }
            } else {
                $formats = ['d/m/Y', 'd-m-Y', 'd.m.Y', 'Y-m-d'];
                $parsed = false;
                foreach ($formats as $format) {
                    try {
                        $tanggal_masuk = Carbon::createFromFormat($format, $tanggal_masuk_raw)->format('Y-m-d');
                        $parsed = true;
                        break;
                    } catch (\Exception $e) {
                        // lanjut
                    }
                }
                if (!$parsed) {
                    if ($errorMessage) $errorMessage .= ", ";
                    $errorMessage .= 'Format tanggal masuk [' . $tanggal_masuk_raw . '] tidak valid, harus d/m/Y (contoh: 28/10/1993)';
                }
            }
        }

            // Validasi status
            $status_id = null;
            if (!in_array($row['status'], [null, '', '-', '#N/A'])) {
                $status_id = $this->searchInArray($statusMap, 'status', $row['status']);
                if (!$status_id) {
                    if ($errorMessage) $errorMessage .= ", ";
                    $errorMessage .= 'Status [' . $row['status'] . '] tidak ditemukan di DATA MASTER';
                }
            } else {
                $status_id = 1; // Default: Aktif
            }

            // Validasi nik
            $nik = $row['nik'] ?? null;
            if (!in_array($nik, [null, '', '-', '#N/A']) && DataSiswa::where('nik', $nik)
                ->where('virtual_account', '!=', $row['no_virtual_account'])
                ->exists()) {
                if ($errorMessage) $errorMessage .= ", ";
                $errorMessage .= 'NIK [' . $nik . '] sudah ada di database';
            }

            // Validasi transportasi
            $transport_id = null;
            if (!in_array($row['transportasi'], [null, '', '-', '#N/A'])) {
                $transport_id = $this->searchInArray($transportMap, 'nama_transport', $row['transportasi']);
                if (!$transport_id) {
                    if ($errorMessage) $errorMessage .= ", ";
                    $errorMessage .= 'Transportasi [' . $row['transportasi'] . '] tidak ditemukan di DATA MASTER';
                }
            } else {
                if ($errorMessage) $errorMessage .= ", ";
                $errorMessage .= 'Transportasi tidak boleh kosong';
            }

            // Validasi yatim_piatu
            if (!in_array($row['yatim_piatu'], ['Yatim', 'Piatu', 'Yatim Piatu', 'Tidak', null, '', '-', '#N/A'])) {
                if ($errorMessage) $errorMessage .= ", ";
                $errorMessage .= 'Yatim piatu [' . $row['yatim_piatu'] . '] tidak valid, harus Yatim, Piatu, Yatim Piatu, atau Tidak';
            }

            // Validasi jarak_rumah
            $jarak_tempuh_id = null;
            if (!in_array($row['jarak_rumah'], [null, '', '-', '#N/A'])) {
                $jarak_tempuh_id = $this->searchInArray($jarakTempuhMap, 'nama_jarak_tempuh', $row['jarak_rumah']);
                if (!$jarak_tempuh_id) {
                    if ($errorMessage) $errorMessage .= ", ";
                    $errorMessage .= 'Jarak rumah [' . $row['jarak_rumah'] . '] tidak ditemukan di DATA MASTER';
                }
            } else {
                if ($errorMessage) $errorMessage .= ", ";
                $errorMessage .= 'Jarak rumah tidak boleh kosong';
            }

            // Validasi waktu_tempuh
            $waktu_tempuh = $row['waktu_tempuh'] ?? null;

            // Validasi angkatan
            if (in_array($row['angkatan'], [null, '', '-', '#N/A'])) {
                if ($errorMessage) $errorMessage .= ", ";
                $errorMessage .= 'Angkatan tidak boleh kosong';
            }

            // Validasi pendidikan_ayah
            $pendidikan_ayah_id = null;
            if (!in_array($row['pendidikan_ayah'], [null, '', '-', '#N/A'])) {
                $pendidikan_ayah_id = $this->searchInArray($pendidikanMap, 'jenjang_pendidikan', $row['pendidikan_ayah']);
                if (!$pendidikan_ayah_id) {
                    if ($errorMessage) $errorMessage .= ", ";
                    $errorMessage .= 'Pendidikan ayah [' . $row['pendidikan_ayah'] . '] tidak ditemukan di DATA MASTER';
                }
            }

            // Validasi pekerjaan_ayah
            $pekerjaan_ayah_id = null;
            if (!in_array($row['pekerjaan_ayah'], [null, '', '-', '#N/A'])) {
                $pekerjaan_ayah_id = $this->searchInArray($pekerjaanMap, 'nama_pekerjaan', $row['pekerjaan_ayah']);
                if (!$pekerjaan_ayah_id) {
                    if ($errorMessage) $errorMessage .= ", ";
                    $errorMessage .= 'Pekerjaan ayah [' . $row['pekerjaan_ayah'] . '] tidak ditemukan di DATA MASTER';
                }
            }

            // Validasi penghasilan_ayah
            $penghasilan_ayah_id = null;
            if (!in_array($row['penghasilan_ayah'], [null, '', '-', '#N/A'])) {
                $penghasilan_ayah_id = $this->searchInArray($penghasilanMap, 'penghasilan', $row['penghasilan_ayah']);
                if (!$penghasilan_ayah_id) {
                    if ($errorMessage) $errorMessage .= ", ";
                    $errorMessage .= 'Penghasilan ayah [' . $row['penghasilan_ayah'] . '] tidak ditemukan di DATA MASTER';
                }
            }

            // Validasi pendidikan_ibu
            $pendidikan_ibu_id = null;
            if (!in_array($row['pendidikan_ibu'], [null, '', '-', '#N/A'])) {
                $pendidikan_ibu_id = $this->searchInArray($pendidikanMap, 'jenjang_pendidikan', $row['pendidikan_ibu']);
                if (!$pendidikan_ibu_id) {
                    if ($errorMessage) $errorMessage .= ", ";
                    $errorMessage .= 'Pendidikan ibu [' . $row['pendidikan_ibu'] . '] tidak ditemukan di DATA MASTER';
                }
            }

            // Validasi pekerjaan_ibu
            $pekerjaan_ibu_id = null;
            if (!in_array($row['pekerjaan_ibu'], [null, '', '-', '#N/A'])) {
                $pekerjaan_ibu_id = $this->searchInArray($pekerjaanMap, 'nama_pekerjaan', $row['pekerjaan_ibu']);
                if (!$pekerjaan_ibu_id) {
                    if ($errorMessage) $errorMessage .= ", ";
                    $errorMessage .= 'Pekerjaan ibu [' . $row['pekerjaan_ibu'] . '] tidak ditemukan di DATA MASTER';
                }
            }

            // Validasi penghasilan_ibu
            $penghasilan_ibu_id = null;
            if (!in_array($row['penghasilan_ibu'], [null, '', '-', '#N/A'])) {
                $penghasilan_ibu_id = $this->searchInArray($penghasilanMap, 'penghasilan', $row['penghasilan_ibu']);
                if (!$penghasilan_ibu_id) {
                    if ($errorMessage) $errorMessage .= ", ";
                    $errorMessage .= 'Penghasilan ibu [' . $row['penghasilan_ibu'] . '] tidak ditemukan di DATA MASTER';
                }
            }

            // Validasi pendidikan_wali
            $pendidikan_wali_id = null;
            if (!in_array($row['pendidikan_wali'], [null, '', '-', '#N/A'])) {
                $pendidikan_wali_id = $this->searchInArray($pendidikanMap, 'jenjang_pendidikan', $row['pendidikan_wali']);
                if (!$pendidikan_wali_id) {
                    if ($errorMessage) $errorMessage .= ", ";
                    $errorMessage .= 'Pendidikan wali [' . $row['pendidikan_wali'] . '] tidak ditemukan di DATA MASTER';
                }
            }

            // Validasi pekerjaan_wali
            $pekerjaan_wali_id = null;
            if (!in_array($row['pekerjaan_wali'], [null, '', '-', '#N/A'])) {
                $pekerjaan_wali_id = $this->searchInArray($pekerjaanMap, 'nama_pekerjaan', $row['pekerjaan_wali']);
                if (!$pekerjaan_wali_id) {
                    if ($errorMessage) $errorMessage .= ", ";
                    $errorMessage .= 'Pekerjaan wali [' . $row['pekerjaan_wali'] . '] tidak ditemukan di DATA MASTER';
                }
            }

            // Validasi penghasilan_wali
            $penghasilan_wali_id = null;
            if (!in_array($row['penghasilan_wali'], [null, '', '-', '#N/A'])) {
                $penghasilan_wali_id = $this->searchInArray($penghasilanMap, 'penghasilan', $row['penghasilan_wali']);
                if (!$penghasilan_wali_id) {
                    if ($errorMessage) $errorMessage .= ", ";
                    $errorMessage .= 'Penghasilan wali [' . $row['penghasilan_wali'] . '] tidak ditemukan di DATA MASTER';
                }
            }

            // Simpan data ke array
            $rowData = [
                'nis' => $nis,
                'nisn' => $nisn,
                'nik' => $row['nik'],
                'virtual_account' => $row['no_virtual_account'],
                'nama_siswa' => $row['nama_siswa'],
                'no_hp' => $row['no_hp'],
                'email' => $row['email'],
                'agama' => $row['agama'],
                'jenis_kelamin' => $row['jenis_kelamin'],
                'tempat_lahir' => $row['tempat_lahir'],
                'tanggal_lahir' => $tanggal_lahir,
                'tanggal_masuk' => $tanggal_masuk,
                'alamat' => $row['alamat'],
                'rt' => $row['rt'],
                'rw' => $row['rw'],
                'kabupaten' => $row['kabupaten_kota'],
                'kecamatan' => $row['kecamatan'],
                'kelurahan' => $row['desa_lurah'],
                'jarak_tempuh_id' => $jarak_tempuh_id,
                'transport_id' => $transport_id,
                'asal_sekolah' => $row['asal_sekolah'],
                'npsn' => $row['npsn'],
                'yatim_piatu' => $row['yatim_piatu'],
                'jumlah_saudara' => $row['jumlah_saudara'],
                'anak_ke' => $row['anak_ke'],
                'dari_bersaudara' => $row['dari_bersaudara'],
                'angkatan' => $row['angkatan'],
                'status_id' => $status_id,
                'nm_ayah' => $row['nama_ayah'],
                'pendidikan_ayah_id' => $pendidikan_ayah_id,
                'pekerjaan_ayah_id' => $pekerjaan_ayah_id,
                'penghasilan_ayah_id' => $penghasilan_ayah_id,
                'no_hp_ayah' => $row['no_hp_ayah'],
                'nm_ibu' => $row['nama_ibu'],
                'pendidikan_ibu_id' => $pendidikan_ibu_id,
                'pekerjaan_ibu_id' => $pekerjaan_ibu_id,
                'penghasilan_ibu_id' => $penghasilan_ibu_id,
                'no_hp_ibu' => $row['no_hp_ibu'],
                'nm_wali' => $row['nama_wali'],
                'pendidikan_wali_id' => $pendidikan_wali_id,
                'pekerjaan_wali_id' => $pekerjaan_wali_id,
                'penghasilan_wali_id' => $penghasilan_wali_id,
                'no_hp_wali' => $row['no_hp_wali'],
                'unit_id' => $unit_id,
                'ditambah_oleh' => auth()->id(),
                'catatan_gagal' => $errorMessage ? "Error pada baris ke-$index: $errorMessage" : 'Menunggu perbaikan data lain',
            ];

            if ($errorMessage) {
                $errors[$index] = $rowData;
            } else {
                $validRows[$index] = $rowData;
            }
        }

        // Jika ada error atau duplikat no_virtual_account, simpan semua ke siswa_import_failed
        if (!empty($errors) || !empty($existingVas)) {
            foreach ($existingVas as $va => $nis) {
                Notification::make()
                    ->title('Data Sudah Ada')
                    ->body("No Virtual Account [$va] sudah ada di database dengan NIS [$nis]. Hapus data lama atau isi NIS di Excel dengan [$nis].")
                    ->danger()
                    ->send();
            }

            foreach ($rows as $keyIndex => $row) {
                if (array_filter($row->toArray()) === []) {
                    continue;
                }
                $index = $keyIndex + 2;
                $rowData = isset($errors[$index]) ? $errors[$index] : $validRows[$index];
                SiswaImportFailed::updateOrCreate(
                    ['virtual_account' => $rowData['virtual_account']],
                    $rowData
                );
            }

            Notification::make()
                ->title('Gagal Impor Data Siswa')
                ->body('Impor dibatalkan karena ada data error atau duplikat. Perbaiki data di Excel dan impor ulang.')
                ->danger()
                ->send();
            return;
        }

        // Jika semua valid, simpan ke data_siswa
        foreach ($validRows as $rowData) {
            SiswaImportFailed::where('virtual_account', $rowData['virtual_account'])->delete();
            DataSiswa::updateOrCreate(
                ['virtual_account' => $rowData['virtual_account']],
                [
                    'nis' => $rowData['nis'],
                    'nisn' => $rowData['nisn'],
                    'nik' => $rowData['nik'],
                    'nama_siswa' => $rowData['nama_siswa'],
                    'no_hp' => $rowData['no_hp'],
                    'email' => $rowData['email'],
                    'agama' => $rowData['agama'],
                    'jenis_kelamin' => $rowData['jenis_kelamin'],
                    'tempat_lahir' => $rowData['tempat_lahir'],
                    'tanggal_lahir' => $rowData['tanggal_lahir'],
                    'tanggal_masuk' => $rowData['tanggal_masuk'],
                    'alamat' => $rowData['alamat'],
                    'rt' => $rowData['rt'],
                    'rw' => $rowData['rw'],
                    'kabupaten' => $rowData['kabupaten'],
                    'kecamatan' => $rowData['kecamatan'],
                    'kelurahan' => $rowData['kelurahan'],
                    'yatim_piatu' => $rowData['yatim_piatu'],
                    'jumlah_saudara' => $rowData['jumlah_saudara'],
                    'anak_ke' => $rowData['anak_ke'],
                    'dari_bersaudara' => $rowData['dari_bersaudara'],
                    'jarak_tempuh_id' => $rowData['jarak_tempuh_id'],
                    'asal_sekolah' => $rowData['asal_sekolah'],
                    'npsn' => $rowData['npsn'],
                    'transport_id' => $rowData['transport_id'],
                    'angkatan' => $rowData['angkatan'],
                    'status_id' => $rowData['status_id'],
                    'nm_ayah' => $rowData['nm_ayah'],
                    'pendidikan_ayah_id' => $rowData['pendidikan_ayah_id'],
                    'pekerjaan_ayah_id' => $rowData['pekerjaan_ayah_id'],
                    'penghasilan_ayah_id' => $rowData['penghasilan_ayah_id'],
                    'no_hp_ayah' => $rowData['no_hp_ayah'],
                    'nm_ibu' => $rowData['nm_ibu'],
                    'pendidikan_ibu_id' => $rowData['pendidikan_ibu_id'],
                    'pekerjaan_ibu_id' => $rowData['pekerjaan_ibu_id'],
                    'penghasilan_ibu_id' => $rowData['penghasilan_ibu_id'],
                    'no_hp_ibu' => $rowData['no_hp_ibu'],
                    'nm_wali' => $rowData['nm_wali'],
                    'pendidikan_wali_id' => $rowData['pendidikan_wali_id'],
                    'pekerjaan_wali_id' => $rowData['pekerjaan_wali_id'],
                    'penghasilan_wali_id' => $rowData['penghasilan_wali_id'],
                    'no_hp_wali' => $rowData['no_hp_wali'],
                    'unit_id' => $rowData['unit_id'],
                    'user_id' => auth()->id(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        Notification::make()
            ->title('SISTEM')
            ->body('Data berhasil diimpor')
            ->success()
            ->send();
    }

    protected function searchInArray(array $data, string $searchKey, string $searchValue): ?int
    {
        foreach ($data as $item) {
            if ($item[$searchKey] === $searchValue) {
                return $item['id'];
            }
        }
        return null;
    }

    protected function generateNis(?int $unit_id, array $unitMap): ?string
    {
        if (!$unit_id) {
            Log::error("generateNis: unit_id is null");
            return null;
        }

        $kode_unit = null;
        foreach ($unitMap as $unit) {
            if ($unit['id'] === $unit_id) {
                $kode_unit = $unit['kode_unit'];
                break;
            }
        }

        if (!$kode_unit || !preg_match('/^\d{2}$/', $kode_unit)) {
            Log::error("generateNis: Invalid or missing kode_unit for unit_id {$unit_id}");
            return null;
        }

        $lastSiswa = DataSiswa::where('unit_id', $unit_id)
            ->whereNotNull('nis')
            ->whereRaw('nis REGEXP ? AND LENGTH(nis) = 7', ['^[0-9]{2}[0-9]{5}$'])
            ->orderBy('nis', 'desc')
            ->first();

        if (!$lastSiswa || !$lastSiswa->nis) {
            $newNis = sprintf('%02s%05d', $kode_unit, 1);
            Log::info("generateNis: No valid previous NIS found for unit_id {$unit_id}, starting with {$newNis}");
            return $newNis;
        }

        $lastNis = $lastSiswa->nis;
        if (!preg_match('/^\d{2}\d{5}$/', $lastNis)) {
            Log::error("generateNis: Last NIS [{$lastNis}] has invalid format for unit_id {$unit_id}");
            return null;
        }

        $lastNumber = (int) substr($lastNis, 2);
        $newNumber = $lastNumber + 1;

        if ($newNumber > 99999) {
            Log::error("generateNis: Sequence number exceeded 99999 for unit_id {$unit_id}");
            return null;
        }

        $newNis = sprintf('%02s%05d', $kode_unit, $newNumber);

        $attempts = 0;
        $maxAttempts = 100;
        while (DataSiswa::where('nis', $newNis)->exists() || SiswaImportFailed::where('nis', $newNis)->exists()) {
            if ($attempts >= $maxAttempts) {
                Log::error("generateNis: Failed to find unique NIS after {$maxAttempts} attempts for unit_id {$unit_id}");
                return null;
            }
            $newNumber++;
            if ($newNumber > 99999) {
                Log::error("generateNis: Sequence number exceeded 99999 for unit_id {$unit_id}");
                return null;
            }
            $newNis = sprintf('%02s%05d', $kode_unit, $newNumber);
            $attempts++;
        }

        Log::info("generateNis: Generated NIS = {$newNis} for unit_id {$unit_id}");
        return $newNis;
    }
}