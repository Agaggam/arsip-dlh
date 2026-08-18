<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BackupController extends Controller
{
    /**
     * Helper: Hanya Super Admin yang boleh akses
     */
    private function checkSuperAdmin()
    {
        $user = Auth::user();
        if (!$user || !$user->isPureSuperAdmin()) {
            abort(403, 'Akses Ditolak: Hanya Super Admin yang diizinkan mengelola backup database.');
        }
    }

    /**
     * Halaman daftar file backup
     */
    public function index()
    {
        $this->checkSuperAdmin();

        // Pastikan folder backup ada
        if (!Storage::disk('local')->exists('backups')) {
            Storage::disk('local')->makeDirectory('backups');
        }

        // Gunakan glob atau scandir agar lebih reliable di Windows
        $backupDir = storage_path('app/backups');
        $backups = [];

        if (file_exists($backupDir)) {
            $files = \Illuminate\Support\Facades\File::files($backupDir);
            foreach ($files as $file) {
                if ($file->getExtension() === 'sql') {
                    $backups[] = [
                        'filename' => $file->getFilename(),
                        'path' => 'backups/' . $file->getFilename(),
                        'size' => $this->formatBytes($file->getSize()),
                        'created_at' => date('Y-m-d H:i:s', $file->getMTime()),
                    ];
                }
            }
        }

        // Urutkan backup terbaru di atas
        usort($backups, function ($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });

        return view('admin.backup.index', compact('backups'));
    }

    /**
     * Buat backup database (.sql) baru
     */
    public function create()
    {
        $this->checkSuperAdmin();

        try {
            $dbName = config('database.connections.mysql.database');
            $dbHost = config('database.connections.mysql.host');
            $dbPort = config('database.connections.mysql.port');
            $dbUser = config('database.connections.mysql.username');
            $dbPass = config('database.connections.mysql.password');

            $filename = 'backup_' . $dbName . '_' . date('Y-m-d_H-i-s') . '.sql';
            $storagePath = storage_path('app/backups/' . $filename);

            if (!file_exists(storage_path('app/backups'))) {
                mkdir(storage_path('app/backups'), 0755, true);
            }

            // Path mysqldump di Laragon / Environment
            $mysqldumpPath = 'C:\\laragon\\bin\\mysql\\mysql-8.0.30-winx64\\bin\\mysqldump.exe';
            if (!file_exists($mysqldumpPath)) {
                $mysqldumpPath = 'mysqldump';
            }

            $passParam = !empty($dbPass) ? "-p\"{$dbPass}\"" : "";
            $command = "\"{$mysqldumpPath}\" -h{$dbHost} -P{$dbPort} -u{$dbUser} {$passParam} {$dbName} > \"{$storagePath}\"";

            // Jalankan shell command
            exec($command, $output, $returnVar);

            // Fallback jika mysqldump gagal: PHP native backup generator
            if ($returnVar !== 0 || !file_exists($storagePath) || filesize($storagePath) === 0) {
                $this->generatePhpSqlBackup($storagePath);
            }

            log_activity(Auth::user(), 'backup_database', "Super Admin membuat backup database ({$filename}).");

            return back()->with('success', "Backup database `{$filename}` berhasil dibuat!");
        } catch (\Exception $e) {
            return back()->with('error', "Gagal membuat backup database: " . $e->getMessage());
        }
    }

    /**
     * Unduh file backup database
     */
    public function download($filename)
    {
        $this->checkSuperAdmin();

        $path = storage_path('app/backups/' . $filename);
        if (!file_exists($path)) {
            return back()->with('error', 'File backup tidak ditemukan.');
        }

        return response()->download($path);
    }

    /**
     * Hapus file backup database
     */
    public function destroy($filename)
    {
        $this->checkSuperAdmin();

        $path = storage_path('app/backups/' . $filename);
        if (file_exists($path)) {
            unlink($path);
            log_activity(Auth::user(), 'hapus_backup', "Super Admin menghapus file backup database ({$filename}).");
            return back()->with('success', 'File backup berhasil dihapus.');
        }

        return back()->with('error', 'File backup tidak ditemukan.');
    }

    /**
     * Restore database dari file .sql yang diunggah
     */
    public function restore(Request $request)
    {
        $this->checkSuperAdmin();

        $request->validate([
            'backup_file' => 'required|file|mimes:sql,txt|max:51200', // max 50MB
        ]);

        try {
            $file = $request->file('backup_file');
            $sqlContent = file_get_contents($file->getRealPath());

            // Nonaktifkan Foreign Key Check selama restore
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::unprepared($sqlContent);
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            log_activity(Auth::user(), 'restore_database', "Super Admin melakukan restore database dari file: " . $file->getClientOriginalName());

            return back()->with('success', 'Database berhasil dipulihkan (Restore)!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memulihkan database: ' . $e->getMessage());
        }
    }

    /**
     * Native PHP Backup fallback generator
     */
    private function generatePhpSqlBackup(string $filePath)
    {
        $tables = DB::select('SHOW TABLES');
        $dbName = config('database.connections.mysql.database');
        $columnName = "Tables_in_" . $dbName;

        $sql = "-- Backup Database Arsip DLH\n-- Date: " . date('Y-m-d H:i:s') . "\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($tables as $table) {
            $tableName = $table->$columnName ?? current((array)$table);

            // Table Structure
            $createStmt = DB::select("SHOW CREATE TABLE `{$tableName}`");
            $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
            $sql .= $createStmt[0]->{'Create Table'} . ";\n\n";

            // Table Data
            $rows = DB::table($tableName)->get();
            foreach ($rows as $row) {
                $arrayRow = (array) $row;
                $keys = array_keys($arrayRow);
                $values = array_values($arrayRow);

                $escapedValues = array_map(function ($val) {
                    if (is_null($val)) return "NULL";
                    return DB::getPdo()->quote($val);
                }, $values);

                $sql .= "INSERT INTO `{$tableName}` (`" . implode("`, `", $keys) . "`) VALUES (" . implode(", ", $escapedValues) . ");\n";
            }
            $sql .= "\n";
        }

        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";
        file_put_contents($filePath, $sql);
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
