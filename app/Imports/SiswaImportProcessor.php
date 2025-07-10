<?php

namespace App\Imports;

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
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SiswaImportProcessor implements ToCollection, WithHeadingRow
{
    // Header wajib di Excel
    protected $requiredHeaders = [
        'status',
        'nik',
        'no_virtual_account',
        'nama_siswa',
        'jenis_kelamin',
        'email',
        'no_hp',
        'nis',
        'nisn',
        'tempat_lahir',
        'tanggal_lahir',
        'agama',
        'alamat',
        'rt',
        'rw',
        'kabupaten_kota',
        'kecamatan',
        'desa_lurah',
        'transportasi',
        'yatim_piatu',
        'jarak_rumah',
        'waktu_tempuh',
        'jumlah_saudara',
        'anak_ke',
        'dari_bersaudara',
        'nama_ayah',
        'pendidikan_ayah',
        'pekerjaan_ayah',
        'penghasilan_ayah',
        'no_hp_ayah',
        'nama_ibu',
        'pendidikan_ibu',
        'pekerjaan_ibu',
        'penghasilan_ibu',
        'no_hp_ibu',
        'nama_wali',
        'pendidikan_wali',
        'pekerjaan_wali',
        'penghasilan_wali',
        'no_hp_wali',
        'unit',
        'angkatan',
    ];

    public function collection(Collection $rows)
    {
        // Validasi header Excel
        $headers = array_keys($rows->first()->toArray());
        $missingHeaders = array_diff($this->requiredHeaders, $headers);

        // Jika ada header yang hilang
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

        foreach ($rows as $keyIndex => $row) {
            // Lewati baris kosong
            if (array_filter($row->toArray()) === []) {
                continue;
            }

            $index = $keyIndex + 2; // Nomor baris (dimulai dari 2 karena header di baris 1)
            $errorMessage = null;

            // Validasi unit
            $unit_id = null;
                if (!in_array($row['unit'], [null, '', '-', '#N/A'])) {
                        $unit_id = $this->searchInArray($unitMap, 'nm_unit', $row['unit']);
                            if (!$unit_id) {
                                if ($errorMessage) $errorMessage .= ", ";
                                $errorMessage .= 'Unit [' . $row['unit'] . '] tidak ditemukan di DATA MASTER';
                            }
                        } else {
                            if ($errorMessage) $errorMessage .= ", ";
                            $errorMessage .= 'Unit tidak boleh kosong';
                        }

            // Validasi dan generate NIS
                        $nis = null;
            if (!in_array($row['nis'], [null, '', '-', '#N/A'])) {
                // Jika NIS diisi di Excel, validasi format dan keunikan
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
                // Cek apakah data ada di SiswaImportFailed berdasarkan no_virtual_account
                $failedImport = SiswaImportFailed::where('virtual_account', $row['no_virtual_account'])->first();
                if ($failedImport && $failedImport->nis && preg_match('/^\d{2}\d{5}$/', $failedImport->nis)) {
                    $nis = $failedImport->nis;
                    \Log::info("Reusing NIS {$nis} from SiswaImportFailed for no_virtual_account: {$row['no_virtual_account']}");
                } else {
                    $nis = $this->generateNis($unit_id, $unitMap);
                    if (!$nis) {
                        if ($errorMessage) $errorMessage .= ", ";
                        $errorMessage .= 'Gagal generate NIS otomatis';
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

            // Validasi no_virtual_account
            if (in_array($row['no_virtual_account'], [null, '', '-', '#N/A'])) {
                if ($errorMessage) $errorMessage .= ", ";
                $errorMessage .= 'No Virtual Account tidak boleh kosong';
            } else {
                $vaExists = DataSiswa::where('virtual_account', $row['no_virtual_account'])
                    ->where('nama_siswa', '!=', $row['nama_siswa'])
                    ->exists();
                if ($vaExists) {
                    if ($errorMessage) $errorMessage .= ", ";
                    $errorMessage .= 'No Virtual Account [' . $row['no_virtual_account'] . '] sudah ada di database';
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

            // Validasi email (opsional, unik jika diisi)
            if (!in_array($row['email'], [null, '', '-', '#N/A'])) {
                $emailExists = DataSiswa::where('email', $row['email'])
                    ->where('virtual_account', '!=', $row['no_virtual_account'])
                    ->exists();
                if ($emailExists) {
                    if ($errorMessage) $errorMessage .= ", ";
                    $errorMessage .= 'Email [' . $row['email'] . '] sudah ada di database';
                }
            }

            // Validasi nisn (boleh kosong)
            $nisn = $row['nisn'] ?? null;
            if (!in_array($nisn, [null, '', '-', '#N/A']) && DataSiswa::where('nisn', $nisn)
                ->where('virtual_account', '!=', $row['no_virtual_account'])
                ->exists()) {
                if ($errorMessage) $errorMessage .= ", ";
                $errorMessage .= 'NISN [' . $nisn . '] sudah ada di database';
            }

            // Validasi tempat_lahir
            if (in_array($row['tempat_lahir'], [null, '', '-', '#N/A'])) {
                if ($errorMessage) $errorMessage .= ", ";
                $errorMessage .= 'Tempat lahir tidak boleh kosong';
            }

            // Validasi dan konversi tanggal_lahir (opsional)
           $tanggal_lahir = null;
           if (!in_array($row['tanggal_lahir'], [null, '', '-', '#N/A'])) {
               // Bersihkan input
               $tanggal_lahir_raw = trim($row['tanggal_lahir']);
               // Coba beberapa format tanggal
               $formats = ['d/m/Y', 'd-m-Y', 'd.m.Y', 'Y-m-d'];
               $parsed = false;

               foreach ($formats as $format) {
                   try {
                       $tanggal_lahir = Carbon::createFromFormat($format, $tanggal_lahir_raw)->format('Y-m-d');
                       $parsed = true;
                       break;
                   } catch (\Exception $e) {
                       // Lanjutkan ke format berikutnya
                   }
               }

               // Jika tidak ada format yang cocok
               if (!$parsed) {
                   if ($errorMessage) $errorMessage .= ", ";
                   $errorMessage .= 'Format tanggal lahir [' . $tanggal_lahir_raw . '] tidak valid, harus d/m/Y (contoh: 28/10/1993)';
               }
           }

           // Validasi dan konversi tanggal_masuk
           $tanggal_masuk = null;
           if (in_array($row['tanggal_masuk'], [null, '', '-', '#N/A'])) {
               if ($errorMessage) $errorMessage .= ", ";
               $errorMessage .= 'Tanggal masuk tidak boleh kosong';
           } else {
               // Bersihkan input
               $tanggal_masuk_raw = trim($row['tanggal_masuk']);
               // Coba beberapa format tanggal
               $formats = ['d/m/Y', 'd-m-Y', 'd.m.Y', 'Y-m-d'];
               $parsed = false;

               foreach ($formats as $format) {
                   try {
                       $tanggal_masuk = Carbon::createFromFormat($format, $tanggal_masuk_raw)->format('Y-m-d');
                       $parsed = true;
                       break;
                   } catch (\Exception $e) {
                       // Lanjutkan ke format berikutnya
                   }
               }

               // Jika tidak ada format yang cocok
               if (!$parsed) {
                   if ($errorMessage) $errorMessage .= ", ";
                   $errorMessage .= 'Format tanggal masuk [' . $tanggal_masuk_raw . '] tidak valid, harus d/m/Y (contoh: 28/10/1993)';
               }
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
            if (!in_array($row['yatim_piatu'], ['Yatim', 'Piatu', 'Yatim Piatu', null, '', '-', '#N/A'])) {
                if ($errorMessage) $errorMessage .= ", ";
                $errorMessage .= 'Yatim piatu [' . $row['yatim_piatu'] . '] tidak valid, harus Ya atau Tidak';
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

            // Validasi waktu_tempuh (diabaikan karena tidak ada di tabel)
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

            // Jika ada error, simpan ke SiswaImportFailed
            if ($errorMessage) {
                $errorMessage = "Error pada baris ke-$index: " . $errorMessage;
                SiswaImportFailed::updateOrCreate(
                    ['virtual_account' => $row['no_virtual_account']],
                    [
                        'nis' => $nis,
                        'nisn' => $row['nisn'],
                        'nama_siswa' => $row['nama_siswa'],
                        'nik' => $row['nik'],
                        'no_hp' => $row['no_hp'],
                        'email' => $row['email'],
                        'agama' => $row['agama'],
                        'jenis_kelamin' => $row['jenis_kelamin'],
                        'tempat_lahir' => $row['tempat_lahir'],
                        'tanggal_lahir' => $tanggal_lahir,
                        'alamat' => $row['alamat'],
                        'rt' => $row['rt'],
                        'rw' => $row['rw'],
                        'kabupaten' => $row['kabupaten_kota'],
                        'kecamatan' => $row['kecamatan'],
                        'kelurahan' => $row['desa_lurah'],
                        'jarak_tempuh_id' => $jarak_tempuh_id,
                        'transport_id' => $transport_id,
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
                        'catatan_gagal' => $errorMessage,
                    ]
                );

                Notification::make()
                    ->title('SISTEM')
                    ->body('Ada sebagian atau semua data gagal diimpor')
                    ->danger()
                    ->send();
            } else {
                // Hapus data lama di SiswaImportFailed
                SiswaImportFailed::where('virtual_account', $row['no_virtual_account'])->delete();

                // Simpan atau perbarui data ke DataSiswa
                DataSiswa::updateOrCreate(
                    ['virtual_account' => $row['no_virtual_account']],
                    [
                        'nis' => $nis,
                        'nisn' => $row['nisn'],
                        'nik' => $row['nik'],
                        'nama_siswa' => $row['nama_siswa'],
                        'virtual_account' => $row['no_virtual_account'],
                        'no_hp' => $row['no_hp'],
                        'email' => $row['email'],
                        'agama' => $row['agama'],
                        'jenis_kelamin' => $row['jenis_kelamin'],
                        'tempat_lahir' => $row['tempat_lahir'],
                        'tanggal_lahir' => $tanggal_lahir,
                        'alamat' => $row['alamat'],
                        'rt' => $row['rt'],
                        'rw' => $row['rw'],
                        'kabupaten' => $row['kabupaten_kota'],
                        'kecamatan' => $row['kecamatan'],
                        'kelurahan' => $row['desa_lurah'],
                        'yatim_piatu' => $row['yatim_piatu'],
                        'jumlah_saudara' => $row['jumlah_saudara'],
                        'anak_ke' => $row['anak_ke'],
                        'dari_bersaudara' => $row['dari_bersaudara'],
                        'jarak_tempuh_id' => $jarak_tempuh_id,
                        'transport_id' => $transport_id,
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
                        'user_id' => auth()->id(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );

                Notification::make()
                    ->title('SISTEM')
                    ->body('Data berhasil diimpor')
                    ->success()
                    ->send();
            }
        }
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
            return null; // Tidak bisa generate NIS tanpa unit_id
        }

        // Ambil kode unit dari unitMap
        $kode_unit = null;
        foreach ($unitMap as $unit) {
            if ($unit['id'] === $unit_id) {
                $kode_unit = $unit['kode_unit'];
                break;
            }
        }

        if (!$kode_unit) {
            return null; // Kode unit tidak ditemukan
        }

        // Ambil NIS terakhir untuk unit tertentu
        $lastSiswa = DataSiswa::where('unit_id', $unit_id)
            ->orderBy('nis', 'desc')
            ->first();

        // Jika tidak ada data untuk unit ini, mulai dari kode_unit + 00001
        if (!$lastSiswa || !$lastSiswa->nis) {
            return $kode_unit . str_pad(1, 5, '0', STR_PAD_LEFT); // Contoh: 0300001
        }

        // Ambil nomor urut dari NIS terakhir
        $lastNis = $lastSiswa->nis;
        $lastNumber = (int) substr($lastNis, 2); // Ambil 5 digit terakhir (misalnya, 00770)
        $newNumber = $lastNumber + 1;

        // Format NIS baru (misalnya, 0300771)
        $newNis = $kode_unit . str_pad($newNumber, 5, '0', STR_PAD_LEFT);

        // Pastikan NIS unik
        while (DataSiswa::where('nis', $newNis)->exists()) {
            $newNumber++;
            $newNis = $kode_unit . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
        }

        return $newNis;
    }
}