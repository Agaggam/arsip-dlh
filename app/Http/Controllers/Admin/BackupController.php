<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
    /**
     * Helper: Hanya Super Admin yang boleh akses
     */
    private function checkSuperAdmin(): void
    {
        $user = Auth::user();
        if (!$user || !$user->isPureSuperAdmin()) {
            abort(403, 'Akses Ditolak: Hanya Super Admin yang diizinkan mengelola backup database.');
        }
    }

    /**
     * [HIGH-02] Sanitasi dan validasi filename dari URL parameter
     * Mengembalikan path absolute yang aman, atau null jika tidak valid.
     */
    private function resolveBackupPath(string $filename): ?string
    {
        // Ambil hanya basename (hapus semua path traversal seperti ../../)
        $safeFilename = basename($filename);

        // Hanya izinkan ekstensi .sql
        if (pathinfo($safeFilename, PATHINFO_EXTENSION) !== 'sql') {
            return null;
        }

        $backupDir = realpath(storage_path('app/backups'));
        if (!$backupDir) {
            return null;
        }

        $fullPath = $backupDir . DIRECTORY_SEPARATOR . $safeFilename;
        $realPath = realpath($fullPath);

        // Pastikan path final benar-benar di dalam folder backups (cegah path traversal)
        if (!$realPath || !str_starts_with($realPath, $backupDir)) {
            return null;
        }

        return $realPath;
    }

    /**
     * [CRITICAL-02] Resolve path mysqldump dengan aman (tanpa hardcode)
     */
    private function resolveMysqldumpPath(): string
    {
        // Cek dari .env / config terlebih dahulu
        $configPath = env('MYSQLDUMP_PATH', '');
        if ($configPath && file_exists($configPath)) {
            return $configPath;
        }

        // Cek lokasi umum Laragon di Windows
        $laragonPaths = [
            'C:\\laragon\\bin\\mysql\\mysql-8.0.30-winx64\\bin\\mysqldump.exe',
            'C:\\laragon\\bin\\mysql\\mysql-8.0.31-winx64\\bin\\mysqldump.exe',
            'C:\\laragon\\bin\\mysql\\mysql-8.4.0-winx64\\bin\\mysqldump.exe',
        ];

        foreach ($laragonPaths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        // Fallback ke PATH sistem
        return 'mysqldump';
    }

    /**
     * Halaman daftar file backup
     */
    public function index()
    {
        $this->checkSuperAdmin();

        if (!Storage::disk('local')->exists('backups')) {
            Storage::disk('local')->makeDirectory('backups');
        }

        $backupDir = storage_path('app/backups');
        $backups   = [];

        if (file_exists($backupDir)) {
            $files = \Illuminate\Support\Facades\File::files($backupDir);
            foreach ($files as $file) {
                if ($file->getExtension() === 'sql') {
                    $backups[] = [
                        'filename'   => $file->getFilename(),
                        'path'       => 'backups/' . $file->getFilename(),
                        'size'       => $this->formatBytes($file->getSize()),
                        'created_at' => date('Y-m-d H:i:s', $file->getMTime()),
                    ];
                }
            }
        }

        usort($backups, fn($a, $b) => strtotime($b['created_at']) - strtotime($a['created_at']));

        return view('admin.backup.index', compact('backups'));
    }

    /**
     * [CRITICAL-02] Buat backup database (.sql) baru — aman dari shell injection
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

            $filename    = 'backup_' . $dbName . '_' . date('Y-m-d_H-i-s') . '.sql';
            $storagePath = storage_path('app/backups/' . $filename);

            if (!file_exists(storage_path('app/backups'))) {
                mkdir(storage_path('app/backups'), 0755, true);
            }

            $mysqldumpPath = $this->resolveMysqldumpPath();

            // [CRITICAL-02] Gunakan escapeshellarg() pada SEMUA argumen shell
            $cmdParts = [
                escapeshellarg($mysqldumpPath),
                '--host='      . escapeshellarg($dbHost),
                '--port='      . escapeshellarg((string) $dbPort),
                '--user='      . escapeshellarg($dbUser),
                '--single-transaction',
                '--routines',
                '--triggers',
                '--result-file=' . escapeshellarg($storagePath),
                escapeshellarg($dbName),
            ];

            // Password dikirim via env var MYSQL_PWD agar tidak muncul di ps/history
            $envPrefix = '';
            if (!empty($dbPass)) {
                // Windows: set via putenv (tidak muncul di command line)
                putenv("MYSQL_PWD={$dbPass}");
                // Unix: prefix command
                if (PHP_OS_FAMILY !== 'Windows') {
                    $envPrefix = 'MYSQL_PWD=' . escapeshellarg($dbPass) . ' ';
                }
            }

            $command = $envPrefix . implode(' ', $cmdParts);
            exec($command, $output, $returnVar);

            // Bersihkan env var setelah digunakan
            if (!empty($dbPass)) {
                putenv('MYSQL_PWD');
            }

            // Fallback ke PHP native backup jika mysqldump gagal
            if ($returnVar !== 0 || !file_exists($storagePath) || filesize($storagePath) === 0) {
                Log::warning("mysqldump gagal (exit: {$returnVar}), beralih ke PHP native backup.");
                $this->generatePhpSqlBackup($storagePath);
            }

            log_activity(Auth::user(), 'backup_database', "Super Admin membuat backup database ({$filename}).");
            return back()->with('success', "Backup database `{$filename}` berhasil dibuat!");

        } catch (\Exception $e) {
            Log::error('Backup database gagal: ' . $e->getMessage());
            return back()->with('error', 'Gagal membuat backup database. Periksa log untuk detail.');
        }
    }

    /**
     * [HIGH-02] Unduh file backup database — aman dari path traversal
     */
    public function download(string $filename)
    {
        $this->checkSuperAdmin();

        $safePath = $this->resolveBackupPath($filename);

        if (!$safePath || !file_exists($safePath)) {
            abort(404, 'File backup tidak ditemukan.');
        }

        return response()->download($safePath);
    }

    /**
     * [HIGH-02] Hapus file backup database — aman dari path traversal
     */
    public function destroy(string $filename)
    {
        $this->checkSuperAdmin();

        $safePath = $this->resolveBackupPath($filename);

        if (!$safePath || !file_exists($safePath)) {
            return back()->with('error', 'File backup tidak ditemukan.');
        }

        $safeFilename = basename($safePath);
        unlink($safePath);
        log_activity(Auth::user(), 'hapus_backup', "Super Admin menghapus file backup database ({$safeFilename}).");
        return back()->with('success', 'File backup berhasil dihapus.');
    }

    /**
     * [CRITICAL-03] Restore database dari file .sql yang diunggah — dengan sanitasi & konfirmasi password
     */
    public function restore(Request $request)
    {
        $this->checkSuperAdmin();

        $request->validate([
            'backup_file' => [
                'required',
                'file',
                'max:51200', // 50MB
                function ($attribute, $value, $fail) {
                    // Validasi ekstensi secara eksplisit (MIME type .sql tidak selalu reliable)
                    if ($value->getClientOriginalExtension() !== 'sql') {
                        $fail('Hanya file dengan ekstensi .sql yang diizinkan.');
                    }
                },
            ],
            // [CRITICAL-03] Wajib konfirmasi password sebelum restore
            'restore_password' => 'required|string',
        ]);

        // Verifikasi password admin
        if (!Hash::check($request->restore_password, Auth::user()->password)) {
            return back()->with('error', 'Password konfirmasi salah. Restore database dibatalkan.');
        }

        try {
            $file       = $request->file('backup_file');
            $sqlContent = file_get_contents($file->getRealPath());

            // [CRITICAL-03] Tolak SQL yang mengandung perintah berbahaya di luar scope backup
            $forbiddenPatterns = [
                '/\bDROP\s+DATABASE\b/i',
                '/\bCREATE\s+USER\b/i',
                '/\bGRANT\b/i',
                '/\bREVOKE\b/i',
                '/\bSHUTDOWN\b/i',
                '/\bLOAD\s+DATA\s+INFILE\b/i',
                '/\bINTO\s+OUTFILE\b/i',
                '/\bINTO\s+DUMPFILE\b/i',
            ];

            foreach ($forbiddenPatterns as $pattern) {
                if (preg_match($pattern, $sqlContent)) {
                    Log::warning('Percobaan restore dengan SQL berbahaya oleh: ' . Auth::user()->email);
                    return back()->with('error', 'File SQL mengandung perintah yang tidak diizinkan dan ditolak oleh sistem keamanan.');
                }
            }

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::unprepared($sqlContent);
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            log_activity(Auth::user(), 'restore_database', 'Super Admin melakukan restore database dari file: ' . $file->getClientOriginalName());
            return back()->with('success', 'Database berhasil dipulihkan (Restore)!');

        } catch (\Exception $e) {
            Log::error('Database restore gagal: ' . $e->getMessage());
            return back()->with('error', 'Gagal memulihkan database. Periksa log untuk detail.');
        }
    }

    /**
     * Native PHP Backup fallback generator (digunakan saat mysqldump tidak tersedia)
     */
    private function generatePhpSqlBackup(string $filePath): void
    {
        $tables     = DB::select('SHOW TABLES');
        $dbName     = config('database.connections.mysql.database');
        $columnName = 'Tables_in_' . $dbName;

        $sql = "-- Backup Database Arsip DLH\n-- Date: " . date('Y-m-d H:i:s') . "\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($tables as $table) {
            $tableName = $table->$columnName ?? current((array) $table);

            $createStmt = DB::select("SHOW CREATE TABLE `{$tableName}`");
            $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
            $sql .= $createStmt[0]->{'Create Table'} . ";\n\n";

            $rows = DB::table($tableName)->get();
            foreach ($rows as $row) {
                $arrayRow = (array) $row;
                $keys     = array_keys($arrayRow);
                $values   = array_values($arrayRow);

                $escapedValues = array_map(function ($val) {
                    if (is_null($val)) {
                        return 'NULL';
                    }
                    return DB::getPdo()->quote($val);
                }, $values);

                $sql .= 'INSERT INTO `' . $tableName . '` (`' . implode('`, `', $keys) . '`) VALUES (' . implode(', ', $escapedValues) . ");\n";
            }
            $sql .= "\n";
        }

        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";
        file_put_contents($filePath, $sql);
    }

    private function formatBytes(int|float $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max((float) $bytes, 0);
        $pow   = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow   = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
