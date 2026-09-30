<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Archive;
use App\Models\SurveyHarga;
use App\Models\Pengawasan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PurgeExpiredTrashCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trash:purge {--days=30 : Batas hari retensi data di tong sampah sebelum dihapus permanen}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hapus permanen data soft-deleted (arsip, usulan harga, pengawasan) dan berkas fisiknya yang telah berada di tong sampah lebih dari 30 hari';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = (int) $this->option('days');
        if ($days <= 0) {
            $days = 30;
        }

        $cutoff = now()->subDays($days);
        $this->info("Menjalankan pembersihan tong sampah untuk data sebelum: {$cutoff->toDateTimeString()} ({$days} hari lalu)...");

        // 1. Bersihkan Arsip
        $expiredArchives = Archive::onlyTrashed()->where('deleted_at', '<=', $cutoff)->get();
        $deletedArchivesCount = 0;

        foreach ($expiredArchives as $archive) {
            try {
                if ($archive->file_path && Storage::disk('local')->exists($archive->file_path)) {
                    Storage::disk('local')->delete($archive->file_path);
                }
                $archive->forceDelete();
                $deletedArchivesCount++;
            } catch (\Throwable $e) {
                Log::error("Gagal menghapus permanen arsip ID {$archive->id}: " . $e->getMessage());
            }
        }

        // 2. Bersihkan Usulan Survey Harga
        $expiredSurveys = SurveyHarga::onlyTrashed()->where('deleted_at', '<=', $cutoff)->get();
        $deletedSurveysCount = 0;

        foreach ($expiredSurveys as $survey) {
            try {
                foreach ([1, 2, 3] as $i) {
                    $key = "gambar_toko_{$i}";
                    if ($survey->$key && Storage::disk('public')->exists($survey->$key)) {
                        Storage::disk('public')->delete($survey->$key);
                    }
                }
                $survey->forceDelete();
                $deletedSurveysCount++;
            } catch (\Throwable $e) {
                Log::error("Gagal menghapus permanen survey ID {$survey->id}: " . $e->getMessage());
            }
        }

        // 3. Bersihkan Pengawasan
        $expiredPengawasans = Pengawasan::onlyTrashed()->where('deleted_at', '<=', $cutoff)->get();
        $deletedPengawasansCount = 0;

        foreach ($expiredPengawasans as $pengawasan) {
            try {
                $pengawasan->forceDelete();
                $deletedPengawasansCount++;
            } catch (\Throwable $e) {
                Log::error("Gagal menghapus permanen pengawasan ID {$pengawasan->id}: " . $e->getMessage());
            }
        }

        $totalDeleted = $deletedArchivesCount + $deletedSurveysCount + $deletedPengawasansCount;

        $this->table(
            ['Tipe Data', 'Jumlah Dihapus Permanen'],
            [
                ['Arsip', $deletedArchivesCount],
                ['Usulan Harga', $deletedSurveysCount],
                ['Pengawasan', $deletedPengawasansCount],
                ['TOTAL', $totalDeleted],
            ]
        );

        if ($totalDeleted > 0) {
            Log::info("Pembersihan Tong Sampah (Auto-Purge > {$days} hari): Berhasil menghapus permanen {$totalDeleted} item ({$deletedArchivesCount} arsip, {$deletedSurveysCount} survey, {$deletedPengawasansCount} pengawasan).");
            $this->info("✅ Berhasil menghapus permanen {$totalDeleted} data.");
        } else {
            $this->comment("ℹ️ Tidak ada data soft-deleted yang melebihi batas {$days} hari.");
        }

        return self::SUCCESS;
    }
}
