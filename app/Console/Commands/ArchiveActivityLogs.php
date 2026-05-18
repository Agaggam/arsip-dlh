<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use App\Exports\ActivityLogsExport;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;

class ArchiveActivityLogs extends Command
{
    protected $signature = 'logs:archive';
    protected $description = 'Export logs from previous month to Excel and delete them';

    public function handle()
    {
        // $lastMonth = now()->subMonthNoOverflow();
        $lastMonth = now(); //Testing
        $startOfMonth = $lastMonth->copy()->startOfMonth();
        $endOfMonth = $lastMonth->copy()->endOfMonth();

        $logs = ActivityLog::whereBetween('created_at', [$startOfMonth, $endOfMonth])->get();

        if ($logs->isEmpty()) {
            $this->info("Tidak ada log untuk bulan " . $lastMonth->translatedFormat('F Y'));
            return;
        }

        // Nama file: logs_NamaBulan_Tahun.xlsx (contoh: logs_Mei_2026.xlsx)
        $fileName = 'Logs_' . $lastMonth->translatedFormat('F') . '_' . $lastMonth->year . '.xlsx';
        $path = 'exports/' . $fileName;

        Excel::store(new ActivityLogsExport($logs), $path, 'local');

        ActivityLog::whereBetween('created_at', [$startOfMonth, $endOfMonth])->delete();

        $this->info("Berhasil ekspor {$logs->count()} log ke {$path} dan dihapus dari database.");
    }
}