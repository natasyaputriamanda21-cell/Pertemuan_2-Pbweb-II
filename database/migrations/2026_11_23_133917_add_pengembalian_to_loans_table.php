<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menjalankan migration untuk menambahkan
     * kolom pengembalian pada tabel loans.
     */
    public function up(): void
    {
        // Mengubah tabel loans yang sudah tersedia.
        Schema::table('loans', function (Blueprint $table) {

            // Menambahkan kolom tanggal kembali.
            // Nullable berarti boleh kosong.
            $table->date('tanggal_kembali')
                ->nullable()
                ->after('tanggal_kembali_rencana');

            // Menambahkan kolom denda.
            // Default 0 berarti nilai awalnya nol.
            $table->unsignedInteger('denda')
                ->default(0)
                ->after('status');

        });
    }

    /**
     * Membatalkan perubahan migration.
     */
    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {

            // Menghapus kedua kolom yang ditambahkan.
            $table->dropColumn([
                'tanggal_kembali',
                'denda'
            ]);

        });
    }
};