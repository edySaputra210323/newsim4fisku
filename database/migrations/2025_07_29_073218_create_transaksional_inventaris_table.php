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
        Schema::create('transaksional_inventaris', function (Blueprint $table) {
            $table->id();
            $table->string('kode_inventaris')->unique();
            $table->integer('no_urut_barang')->unique();
            $table->foreignId('kategori_inventaris_id')->constrained('kategori_inventaris')->cascadeOnDelete();
            $table->foreignId('suplayer_id')->nullable()->constrained('suplayer')->nullOnDelete();
            $table->foreignId('kategori_barang_id')->constrained('kategori_barang')->cascadeOnDelete();
            $table->foreignId('sumber_anggaran_id')->constrained('sumber_anggaran')->cascadeOnDelete();
            $table->foreignId('ruang_id')->constrained('ruangan')->cascadeOnDelete();
            $table->string('nama_inventaris');
            $table->string('merk_inventaris')->nullable();
            $table->string('material_bahan')->nullable();
            $table->string('kondisi')->nullable();
            $table->date('tanggal_beli');
            $table->integer('jumlah_beli');
            $table->integer('harga_satuan');
            $table->integer('total_harga');
            $table->string('kesehatan_barang')->nullable();
            $table->string('keterangan')->nullable();
            $table->string('foto_inventaris')->nullable();
            $table->string('nota_beli')->nullable();
            $table->foreignId('th_ajaran_id')->constrained('tahun_ajaran')->cascadeOnDelete();
            $table->foreignId('semester_id')->constrained('semester')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksional_inventaris');
    }
};
