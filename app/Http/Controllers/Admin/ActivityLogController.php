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
     * Tampilkan daftar log aktivitas (belum diarsipkan)
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
        
        // Daftar file export Excel yang tersedia
        $exportFiles = [];
        $files = Storage::disk('local')->files('exports');
        foreach ($files as $file) {
            if (preg_match('/logs_.+\.xlsx$/', $file)) {
                $exportFiles[] = [
                    'name' => basename($file),
                    'path' => $file,
                    'size' => Storage::disk('local')->size($file),
                    'last_modified' => Storage::disk('local')->lastModified($file),
                ];
            }
        }
        // Urutkan berdasarkan nama file (berisi bulan) descending
        usort($exportFiles, function($a, $b) {
            return strcmp($b['name'], $a['name']);
        });

        return view('admin.activity-logs.index', compact('logs', 'exportFiles'));
    }

    /**
     * Download file export Excel
     */
    public function downloadExport(string $filename)
    {
        $this->secureAccess();

        $path = 'exports/' . $filename;
        if (!Storage::disk('local')->exists($path)) {
            abort(404, 'File tidak ditemukan.');
        }
        return Storage::disk('local')->download($path, $filename);
    }
}