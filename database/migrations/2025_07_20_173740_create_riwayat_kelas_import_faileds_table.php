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
        Schema::create('riwayat_kelas_import_faileds', function (Blueprint $table) {
            $table->id();
            $table->string('nis')->nullable();
            $table->foreignId('data_siswa_id')->nullable()->constrained('data_siswa')->nullOnDelete();
            $table->foreignId('kelas_id')->nullable()->constrained('kelas')->nullOnDelete();
            $table->foreignId('pegawai_id')->nullable()->constrained('pegawai')->nullOnDelete();
            $table->foreignId('tahun_ajaran_id')->nullable()->constrained('tahun_ajaran')->nullOnDelete();
            $table->foreignId('semester_id')->nullable()->constrained('semester')->nullOnDelete();
            $table->string('kelas')->nullable();
            $table->string('walas')->nullable();
            $table->text('catatan_gagal')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_kelas_import_faileds');
    }
};
