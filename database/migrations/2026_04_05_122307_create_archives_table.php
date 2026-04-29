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
        Schema::create('archives', function (Blueprint $table) {
            $table->id();
            $table->string('title')->index(); // Index untuk pencarian judul
            $table->string('file_path'); // Path penyimpanan file
            $table->string('file_type')->nullable(); // Mime type (pdf, png, dll)
            $table->string('file_size')->nullable(); // Ukuran file (KB/MB)
            
            // Relasi ke tabel categories & users
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Statistik & Deskripsi
            $table->integer('download_count')->default(0);
            $table->text('description')->nullable();
            
            // Timestamps (created_at & updated_at) dan Soft Deletes (deleted_at)
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('archives');
    }
};
