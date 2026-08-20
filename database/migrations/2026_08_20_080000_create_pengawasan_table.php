<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengawasan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Info Pengawasan
            $table->unsignedSmallInteger('tahun');
            $table->string('nama_pengawas');
            $table->enum('kecamatan', ['Kecamatan Batu', 'Kecamatan Bumiaji', 'Kecamatan Junrejo']);
            $table->enum('jenis_pengawasan', ['langsung', 'tidak_langsung']);

            // Data Usaha
            $table->string('nama_usaha');
            $table->string('skala_usaha'); // SPPL, UKL-UPL, AMDAL, dll
            $table->date('waktu_pengawasan');

            // Hasil Pengawasan — masing-masing enum: v, p, x, -
            $hasilColumns = [
                'izin_lingkungan', 'wajib_ubah_dok', 'oss_rba', 'lap',
                'grease_trap', 'ipal', 'kett_teknis_ipal', 'pantau_ipal',
                'iplc_pertek', 'pantau_udara', 'inv_lb3', 'tps_b3',
                'kett_teknis_b3', 'izin_rintek', 'sures_biopori', 'pilah_sampah',
            ];

            foreach ($hasilColumns as $col) {
                $table->enum($col, ['v', 'p', 'x', '-'])->default('-');
            }

            // Summary auto-hitung
            $table->unsignedSmallInteger('jml_v')->default(0);
            $table->unsignedSmallInteger('jml_p')->default(0);
            $table->unsignedSmallInteger('jml_x')->default(0);

            // Keterangan
            $table->text('keterangan')->nullable();

            // Status Workflow
            $table->enum('status', ['diajukan', 'disetujui', 'ditolak'])->default('diajukan');
            $table->text('catatan_admin')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengawasan');
    }
};
