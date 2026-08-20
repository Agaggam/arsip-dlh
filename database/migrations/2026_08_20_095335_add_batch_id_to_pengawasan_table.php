<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengawasan', function (Blueprint $table) {
            $table->uuid('batch_id')->nullable()->after('id')->index();
        });

        // Set batch_id for existing records based on unique (tahun, nama_pengawas, kecamatan, jenis_pengawasan)
        $groups = DB::table('pengawasan')->select('tahun', 'nama_pengawas', 'kecamatan', 'jenis_pengawasan')->distinct()->get();
        foreach ($groups as $g) {
            $uuid = (string) Str::uuid();
            DB::table('pengawasan')
                ->where('tahun', $g->tahun)
                ->where('nama_pengawas', $g->nama_pengawas)
                ->where('kecamatan', $g->kecamatan)
                ->where('jenis_pengawasan', $g->jenis_pengawasan)
                ->whereNull('batch_id')
                ->update(['batch_id' => $uuid]);
        }
    }

    public function down(): void
    {
        Schema::table('pengawasan', function (Blueprint $table) {
            $table->dropColumn('batch_id');
        });
    }
};
