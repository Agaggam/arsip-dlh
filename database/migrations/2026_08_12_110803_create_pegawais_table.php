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
        Schema::create('pegawais', function (Blueprint $table) {
            $table->id();
            $table->char('nik', 16)->unique()->comment('Nomor Induk Kependudukan, 16 digit');
            $table->string('nip_nrk')->nullable()->comment('NIP untuk PNS/P3K atau Nomor Register untuk BSN');
            $table->string('nama_lengkap')->comment('Nama lengkap termasuk gelar');
            $table->enum('jenis_kelamin', ['L', 'P'])->comment('L = Laki-laki, P = Perempuan');
            $table->enum('kategori', ['ASN (PNS)', 'P3K Penuh Waktu', 'P3K Paruh Waktu', 'BSN / Non-ASN']);
            $table->string('pangkat_golongan')->nullable()->comment('Pangkat dan golongan ruang');
            $table->string('jabatan')->comment('Jabatan saat ini');
            $table->string('unit_kerja')->comment('Unit kerja atau divisi/bidang');
            $table->date('tmt_sk')->nullable()->comment('Terhitung Mulai Tanggal SK berlaku');
            $table->string('masa_kontrak')->nullable()->comment('Tanggal akhir kontrak, atau "Permanen" untuk PNS');
            $table->decimal('jam_kerja_mingguan', 4, 1)->default(37.5)->comment('Total jam kerja per minggu');
            $table->string('pendidikan_terakhir')->nullable()->comment('Jenjang dan jurusan pendidikan terakhir');
            $table->string('no_hp', 20)->nullable();
            $table->string('email')->nullable();
            $table->enum('status', ['Aktif', 'Cuti', 'Tugas Belajar', 'Non-Aktif'])->default('Aktif');
            $table->index('unit_kerja');
            $table->index('kategori');
            $table->index('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pegawais');
    }
};
