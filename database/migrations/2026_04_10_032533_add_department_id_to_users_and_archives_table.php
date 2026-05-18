<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Users
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
        });

        // Categories
        Schema::table('categories', function (Blueprint $table) {
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            $table->unique(['name', 'department_id']);
        });
    }

    public function down(): void
    {
        // Users
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropColumn('department_id');
        });

        // Categories
        Schema::table('categories', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropUnique(['name', 'department_id']);
            $table->dropColumn('department_id');
        });
    }
};