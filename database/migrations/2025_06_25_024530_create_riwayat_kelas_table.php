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
        Schema::create('riwayat_kelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('data_siswa_id')->constrained('data_siswa');
            $table->foreignId('kelas_id')->constrained('kelas');
            $table->foreignId('pegawai_id')->nullable()->constrained('pegawai')->nullOnDelete();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajaran');
            $table->foreignId('semester_id')->constrained('semester');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_kelas');
    }
};
