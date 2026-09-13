<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tambah kolom is_system ke tabel departments.
     * Kolom ini lebih aman daripada bergantung pada nama 'System'.
     */
    public function up(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->boolean('is_system')->default(false)->after('description');
        });

        // Tandai departemen 'System' sebagai is_system = true
        DB::table('departments')
            ->where('name', 'System')
            ->update(['is_system' => true]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->dropColumn('is_system');
        });
    }
};
