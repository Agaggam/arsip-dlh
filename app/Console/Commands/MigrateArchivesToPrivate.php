<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MigrateArchivesToPrivate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'archives:migrate-to-private';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pindahkan semua file arsip dari disk public ke disk local (private) untuk keamanan.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $archives = \App\Models\Archive::whereNotNull('file_path')->withTrashed()->get();
        $moved    = 0;
        $missing  = 0;
        $skipped  = 0;

        $this->info("Memproses {$archives->count()} arsip...");
        $bar = $this->output->createProgressBar($archives->count());
        $bar->start();

        foreach ($archives as $archive) {
            // Sudah ada di disk local?
            if (\Illuminate\Support\Facades\Storage::disk('local')->exists($archive->file_path)) {
                $skipped++;
                $bar->advance();
                continue;
            }

            // Masih ada di disk public?
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($archive->file_path)) {
                $content = \Illuminate\Support\Facades\Storage::disk('public')->get($archive->file_path);
                \Illuminate\Support\Facades\Storage::disk('local')->put($archive->file_path, $content);
                \Illuminate\Support\Facades\Storage::disk('public')->delete($archive->file_path);
                $moved++;
            } else {
                $missing++;
                $this->newLine();
                $this->warn("  File tidak ditemukan: {$archive->file_path} (Arsip ID: {$archive->id})");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("✅ Selesai! Dipindahkan: {$moved} | Sudah private: {$skipped} | File hilang: {$missing}");

        return self::SUCCESS;
    }
}
