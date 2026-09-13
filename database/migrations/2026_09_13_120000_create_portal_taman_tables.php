<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel admins untuk Portal Taman
        if (!Schema::hasTable('admins')) {
            Schema::create('admins', function (Blueprint $table) {
                $table->increments('id');
                $table->string('username', 50)->unique();
                $table->string('password', 255);
            });

            // Akun default admin portal taman
            DB::table('admins')->insert([
                'id' => 1,
                'username' => 'admin',
                'password' => '$2y$10$F7w9t.KHFW.lhEMZi1Z8ROCOemCC23IOi6hsiEF9fZfMBLDBXTG5W', // default: admin123
            ]);
        }

        // 2. Tabel city_info untuk profil kota & RTH
        if (!Schema::hasTable('city_info')) {
            Schema::create('city_info', function (Blueprint $table) {
                $table->increments('id');
                $table->string('city_name', 100);
                $table->text('description')->nullable();
                $table->text('about')->nullable();
                $table->string('contact', 255)->nullable();
            });

            DB::table('city_info')->insert([
                'id' => 1,
                'city_name' => 'Batu',
                'description' => 'Portal informasi untuk menemukan taman, ruang hijau, dan tempat rekreasi di Kota Batu.',
                'about' => 'Website ini mengumpulkan informasi berbagai taman dan Ruang Terbuka Hijau (RTH) dalam satu kota agar masyarakat lebih mudah menemukan tempat untuk bersantai, berolahraga, dan berkegiatan.',
                'contact' => 'Dinas Lingkungan Hidup Kota Batu',
            ]);
        }

        // 3. Tabel parks untuk data taman kota
        if (!Schema::hasTable('parks')) {
            Schema::create('parks', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name', 150);
                $table->string('category', 80);
                $table->text('address');
                $table->string('opening_hours', 100);
                $table->text('description');
                $table->text('plant_species')->nullable();
                $table->text('facilities')->nullable();
                $table->text('image');
                $table->integer('employee_count')->default(0);
                $table->decimal('area', 12, 2)->default(0.00);
                $table->enum('status', ['Aktif', 'Dalam Perawatan', 'Pasif'])->default('Aktif');
                $table->text('map_url')->nullable();
                $table->decimal('latitude', 10, 7)->nullable();
                $table->decimal('longitude', 10, 7)->nullable();
                $table->timestamp('created_at')->useCurrent();
            });
        }

        // 4. Tabel park_images untuk galeri foto taman
        if (!Schema::hasTable('park_images')) {
            Schema::create('park_images', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('park_id');
                $table->string('image', 255);
                $table->integer('sort_order')->default(0);
                $table->timestamp('created_at')->useCurrent();

                $table->foreign('park_id')->references('id')->on('parks')->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('park_images');
        Schema::dropIfExists('parks');
        Schema::dropIfExists('city_info');
        Schema::dropIfExists('admins');
    }
};
