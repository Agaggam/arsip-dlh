<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ActivityLogController extends Controller
{
    /**
     * Private helper: Pastikan hanya super admin asli yang bisa mengakses.
     */
    private function secureAccess()
    {
        if (!Auth::user()->isPureSuperAdmin()) {
            abort(403, 'Akses Ditolak: Hanya super admin asli yang diizinkan.');
        }
    }

    /**
     * Tampilkan daftar log aktivitas beserta file arsip excel
     */
    public function index(Request $request)
    {
        $this->secureAccess();

        $search = $request->get('search');
        
        $logs = ActivityLog::with('user')
            ->when($search, function ($query, $search) {
                return $query->where('activity', 'LIKE', "%{$search}%")
                             ->orWhere('description', 'LIKE', "%{$search}%")
                             ->orWhere('ip_address', 'LIKE', "%{$search}%")
                             ->orWhere('causer_name', 'LIKE', "%{$search}%")
                             ->orWhere('causer_email', 'LIKE', "%{$search}%")
                             ->orWhereHas('user', function ($q) use ($search) {
                                 $q->where('name', 'LIKE', "%{$search}%")
                                   ->orWhere('email', 'LIKE', "%{$search}%");
                             });
            })
            ->latest()
            ->paginate(20);
        
        $exportFiles = [];

        // PERBAIKAN UTAMA PATH: Cukup tulis 'exports' karena disk 'local' root-nya sudah di 'app/private'
        $targetFolder = 'exports'; 

        if (Storage::disk('local')->exists($targetFolder)) {
            $files = Storage::disk('local')->files($targetFolder);
            
            foreach ($files as $file) {
                $filenameOnly = basename($file);
                
                // PERBAIKAN REGEX: Menggunakan 'logs.*' agar nama file seperti 'Logs_Juni_2026.xlsx' lolos filter aman
                if (preg_match('/.*logs.*\.xlsx$/i', $filenameOnly) || preg_match('/.*logs.*\.xls$/i', $filenameOnly)) {
                    $exportFiles[] = [
                        'name' => $filenameOnly,
                        'path' => $file,
                        'size' => Storage::disk('local')->size($file),
                        'last_modified' => Storage::disk('local')->lastModified($file),
                    ];
                }
            }
        }
        
        // Urutkan berdasarkan waktu modifikasi terbaru (file baru ada di paling atas)
        usort($exportFiles, function($a, $b) {
            return $b['last_modified'] <=> $a['last_modified'];
        });

        return view('admin.activity-logs.index', compact('logs', 'exportFiles'));
    }

    /**
     * [HIGH-03] Download file export Excel — aman dari path traversal
     */
    public function downloadExport(string $filename)
    {
        $this->secureAccess();

        // Sanitasi: ambil hanya basename (hapus path traversal seperti ../../)
        $safeFilename = basename($filename);

        // Validasi ekstensi: hanya izinkan .xlsx dan .xls
        $ext = strtolower(pathinfo($safeFilename, PATHINFO_EXTENSION));
        if (!in_array($ext, ['xlsx', 'xls'])) {
            abort(404, 'File tidak ditemukan.');
        }

        $path = 'exports/' . $safeFilename;

        if (!Storage::disk('local')->exists($path)) {
            abort(404, 'File tidak ditemukan.');
        }

        return Storage::disk('local')->download($path, $safeFilename);
    }
}