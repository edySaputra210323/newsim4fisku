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
        Schema::create('pegawai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('nik', 16)->unique()->nullable()->comment('Nomor Induk Kependudukan');
            $table->string('nm_pegawai');
            $table->string('tempat_lahir')->nullable();
            $table->date('tgl_lahir')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->text('alamat')->nullable();
            $table->string('phone', 15)->nullable();
            $table->date('tgl_mulai_bekerja')->nullable();
            $table->string('nuptk', 16)->unique()->nullable()->comment('Nomor Unik Pendidik dan Tenaga Kependidikan');
            $table->string('npy', 10)->unique()->nullable()->comment('Nomor Pegawai Yayasan');
            $table->boolean('status')->default(false)->nullable();
            $table->string('foto_pegawai')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pegawai');
    }
};
