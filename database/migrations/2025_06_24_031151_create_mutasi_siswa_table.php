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
        Schema::create('mutasi_siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('data_siswa_id')->constrained('data_siswa');
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajaran');
            $table->foreignId('semester_id')->constrained('semester');
            $table->enum('tipe_mutasi', ['Keluar', 'Masuk']);
            $table->date('tanggal_mutasi');
            $table->string('asal_sekolah')->nullable();
            $table->string('sekolah_tujuan')->nullable();
            $table->string('dokumen_mutasi')->nullable();
            $table->string('nomor_mutasi_masuk')->nullable();
            $table->string('nomor_mutasi_keluar')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mutasi_siswa');
    }
};
