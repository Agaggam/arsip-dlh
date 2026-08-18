<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('survey_harga', function (Blueprint $table) {
            $table->uuid('id')->primary(); // UUID sebagai Primary Key
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('department_id')->constrained()->cascadeOnDelete();

            // Kelompok SSH/SBU/HSPK/ASB
            $table->enum('kelompok', ['SSH', 'SBU', 'HSPK', 'ASB']);

            // Data Barang/Jasa Utama
            $table->string('judul');
            $table->string('kode_komponen')->nullable();
            $table->string('kode_rekening')->nullable();
            $table->string('spesifikasi_singkat')->nullable();
            $table->text('spesifikasi_detail')->nullable();
            $table->string('satuan'); // Unit, Buah, Meter, Jam, dll
            $table->bigInteger('harga_usulan'); // Harga rata-rata yang diusulkan

            // Survei Toko 1
            $table->string('nama_toko_1')->nullable();
            $table->bigInteger('harga_toko_1')->nullable();
            $table->string('gambar_toko_1')->nullable(); // path file
            $table->string('link_belanja_1')->nullable();

            // Survei Toko 2
            $table->string('nama_toko_2')->nullable();
            $table->bigInteger('harga_toko_2')->nullable();
            $table->string('gambar_toko_2')->nullable();
            $table->string('link_belanja_2')->nullable();

            // Survei Toko 3
            $table->string('nama_toko_3')->nullable();
            $table->bigInteger('harga_toko_3')->nullable();
            $table->string('gambar_toko_3')->nullable();
            $table->string('link_belanja_3')->nullable();

            // Status Workflow
            $table->enum('status', ['diajukan', 'disetujui', 'ditolak'])->default('diajukan');
            $table->text('catatan_admin')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('survey_harga');
    }
};

