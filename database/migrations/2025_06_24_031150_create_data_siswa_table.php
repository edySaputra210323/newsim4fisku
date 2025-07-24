<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('data_siswa', function (Blueprint $table) {
            $table->id();
            $table->string('nama_siswa', 100);
            $table->string('nis', 20)->unique()->nullable();
            $table->string('nisn', 20)->unique()->nullable();
            $table->string('nik', 20)->unique()->nullable();
            $table->string('virtual_account', 20)->unique();
            $table->string('no_hp', 15)->nullable();
            $table->string('email', 100)->nullable()->unique();
            $table->string('agama', 50)->nullable();
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('tempat_lahir', 50);
            $table->date('tanggal_lahir');
            $table->string('negara', 50)->nullable();
            $table->string('provinsi', 50)->nullable();
            $table->string('kabupaten', 50)->nullable();
            $table->string('kecamatan', 50)->nullable();
            $table->string('kelurahan', 50)->nullable();
            $table->text('alamat')->nullable();
            $table->string('rt', 10)->nullable();
            $table->string('rw', 10)->nullable();
            $table->string('kode_pos', 10)->nullable();
            $table->enum('yatim_piatu', ['Yatim', 'Piatu', 'Yatim Piatu'])->nullable();
            $table->string('penyakit', 100)->nullable();
            $table->string('jumlah_saudara', 10)->nullable();
            $table->string('anak_ke', 10)->nullable();
            $table->string('dari_bersaudara', 10)->nullable();
            $table->foreignId('jarak_tempuh_id')->nullable()->constrained('jarak_tempuh')->nullOnDelete();
            $table->foreignId('transport_id')->nullable()->constrained('transport')->nullOnDelete();
            $table->string('asal_sekolah', 255)->nullable();
            $table->string('npsn', 20)->nullable();
            $table->string('angkatan', 50);
            $table->date('tanggal_masuk')->nullable();
            $table->date('tanggal_keluar')->nullable();
            $table->string('lanjut_sma_dimana', 255)->nullable();
            $table->string('upload_ijazah_sd')->nullable();
            $table->string('dokumen_pendukung')->nullable();
            $table->string('foto_siswa', 100)->nullable();
            $table->foreignId('status_id')->nullable()->constrained('status_siswa')->default(1)->nullOnDelete(); // Default: Aktif;
            $table->string('nm_ayah', 100)->nullable();
            $table->foreignId('pendidikan_ayah_id')->nullable()->constrained('pendidikan_ortu')->nullOnDelete();
            $table->foreignId('pekerjaan_ayah_id')->nullable()->constrained('pekerjaan_ortu')->nullOnDelete();
            $table->foreignId('penghasilan_ayah_id')->nullable()->constrained('penghasilan_ortu')->nullOnDelete();
            $table->string('no_hp_ayah', 15)->nullable();
            $table->string('nm_ibu', 100)->nullable();
            $table->foreignId('pendidikan_ibu_id')->nullable()->constrained('pendidikan_ortu')->nullOnDelete();
            $table->foreignId('pekerjaan_ibu_id')->nullable()->constrained('pekerjaan_ortu')->nullOnDelete();
            $table->foreignId('penghasilan_ibu_id')->nullable()->constrained('penghasilan_ortu')->nullOnDelete();
            $table->string('no_hp_ibu', 15)->nullable();
            $table->string('nm_wali', 100)->nullable();
            $table->foreignId('pendidikan_wali_id')->nullable()->constrained('pendidikan_ortu')->nullOnDelete();
            $table->foreignId('pekerjaan_wali_id')->nullable()->constrained('pekerjaan_ortu')->nullOnDelete();
            $table->foreignId('penghasilan_wali_id')->nullable()->constrained('penghasilan_ortu')->nullOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained('unit')->nullOnDelete();
            $table->string('no_hp_wali', 15)->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_siswa');
    }
};
