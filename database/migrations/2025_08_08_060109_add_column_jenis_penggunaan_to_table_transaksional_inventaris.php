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
        Schema::table('transaksional_inventaris', function (Blueprint $table) {
            $table->enum('jenis_penggunaan', ['tetap', 'permanen', 'mobile'])->after('nama_inventaris');
        });
    
        Schema::table('transaksional_inventaris', function (Blueprint $table) {
            $table->foreignId('pegawai_id')
                  ->nullable()
                  ->constrained('pegawai')
                  ->nullOnDelete()
                  ->after('jenis_penggunaan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksional_inventaris', function (Blueprint $table) {
            // Kalau ada foreign key untuk pegawai_id, drop dulu
            $table->dropForeign(['pegawai_id']);
    
            // Lalu drop kolom-kolomnya
            $table->dropColumn('pegawai_id');
            $table->dropColumn('jenis_penggunaan');
        });
    }
};
