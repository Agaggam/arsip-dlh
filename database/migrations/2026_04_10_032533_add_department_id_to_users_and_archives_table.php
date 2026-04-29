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
        // Hubungkan Users ke Departments
        Schema::table('users', function (Blueprint $table) {
        $table->foreignId('department_id')->constrained()->onDelete('cascade');
        });

        // Hubungkan Categories ke Departments + Atur Unique Constraint
        Schema::table('categories', function (Blueprint $table) {
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            // Mencegah duplikat nama kategori di DALAM departemen yang sama
            // Tapi departemen B boleh punya nama yang sama dengan departemen A
            $table->unique(['department_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
        $table->dropForeign(['department_id']);
        $table->dropColumn('department_id');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropUnique(['department_id', 'name']);
            $table->dropForeign(['department_id']);
            $table->dropColumn('department_id');
        });
    }
};
