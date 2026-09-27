<?php

namespace App\Http\Controllers;

use App\Models\Archive;
use App\Models\Category;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ArchiveController extends Controller
{
    /**
     * PRIVATE HELPER: Cek otoritas melalui relasi Kategori
     * Digunakan untuk Restore, dan Delete (Soft/Permanent)
     */
    private function hasArchiveAccess(Archive $archive)
    {
        $user = Auth::user();
        
        if ($user->isPureSuperAdmin()) {
            return true;
        }
        if ($user->isAdmin() && $archive->category->department_id === $user->department_id) {
            return true;
        }
        return false;
    }

/**
     * DAFTAR ARSIP (Dashboard Admin)
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->get('search');
        $categoryId = $request->get('category_id');
        $fileType = $request->get('file_type');
        $departmentId = $request->get('department_id');
        $statusFile = $request->get('status_file'); // Filter: 'tersedia' atau 'hilang'
        $month = $request->get('month'); // Filter: Bulan
        $year = $request->get('year');   // Filter: Tahun
        $sortByDownload = $request->get('sort_download'); // Filter Baru: 'terbanyak' atau 'tersedikit'

        $query = Archive::with(['category.department', 'user']);

        // Filter hak akses departemen (untuk admin biasa)
        if (!$user->isPureSuperAdmin()) {
            $query->whereHas('category', function($q) use ($user) {
                $q->where('department_id', $user->department_id);
            });
        }

        // Pencarian teks (HANYA JUDUL SAJA, deskripsi tidak usah)
        if ($search) {
            $query->where('title', 'LIKE', "%{$search}%");
        }

        // Filter kategori
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        // Filter tipe dokumen
        if ($fileType) {
            $query->where('file_type', strtoupper($fileType));
        }

        // Filter departemen (hanya untuk super admin)
        if ($user->isPureSuperAdmin() && $departmentId) {
            $query->whereHas('category', function($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            });
        }

        $dateFrom = $request->get('date_from'); // Filter: Tanggal Mulai
        $dateTo = $request->get('date_to');     // Filter: Tanggal Sampai

        // === IMPLEMENTASI FILTER BULAN & TAHUN & RENTANG TANGGAL ===
        if ($dateFrom) {
            $query->whereDate('archive_date', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('archive_date', '<=', $dateTo);
        }

        if ($month) {
            $query->whereMonth('archive_date', $month);
        }

        if ($year) {
            $query->whereYear('archive_date', $year);
        }

        // --- HITUNG STATISTIK (CARD COUNT) ---
        // Mengambil semua archive sesuai filter hak akses & parameter pencarian/waktu untuk menghitung jumlah riil di storage
        $allFilteredArchives = $query->get();
        
        $totalAktif = 0;
        $totalHilang = 0;

        foreach ($allFilteredArchives as $arch) {
            $exists = $arch->file_path && Storage::disk('local')->exists($arch->file_path);
            if ($exists) {
                $totalAktif++;
            } else {
                $totalHilang++;
            }
        }

        // --- LOGIK FILTER STATUS FILE (TERSEDIA / HILANG) ---
        if ($statusFile === 'tersedia' || $statusFile === 'hilang') {
            // Karena status file dicek fisik di storage, kita ambil id yang valid/tidak valid terlebih dahulu
            $matchingIds = $allFilteredArchives->filter(function ($arch) use ($statusFile) {
                $exists = $arch->file_path && Storage::disk('local')->exists($arch->file_path);
                return $statusFile === 'tersedia' ? $exists : !$exists;
            })->pluck('id');

            // Batasi query hanya untuk ID hasil filter fisik tadi
            $query->whereIn('id', $matchingIds);
        }

        // --- LOGIK URUTAN BERDASARKAN UNDUHAN (DOWNLOAD) ---
        // Catatan: Ganti 'download_count' sesuai nama kolom total download di tabel database Anda
        if ($sortByDownload === 'terbanyak') {
            $query->orderBy('download_count', 'desc');
        } elseif ($sortByDownload === 'tersedikit') {
            $query->orderBy('download_count', 'asc');
        } else {
            // Jika tidak memfilter download, default urutkan yang terbaru
            $query->latest();
        }

        // Eksekusi Pagination
        $archives = $query->paginate(10)->appends($request->all());

        // Data untuk dropdown filter Kategori
        $categories = Category::with('department')->when(!$user->isPureSuperAdmin(), function($q) use ($user) {
            return $q->where('department_id', $user->department_id);
        })->get();

        // Data untuk dropdown departemen
        $departments = Department::where('name', '!=', 'System')->get();

        // Data untuk dropdown tipe dokumen
        $fileTypes = Archive::distinct()->orderBy('file_type')->pluck('file_type');

        // === KEBUTUHAN DATA DROPDOWN BULAN & TAHUN ===
        // Opsi bulan statis (1 sampai 12)
        $months = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
            '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
            '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];

        // Ambil daftar tahun secara dinamis yang memang ada di database tabel archives
        $years = Archive::whereNotNull('archive_date')
            ->selectRaw('YEAR(archive_date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        $totalUnduhan = $allFilteredArchives->sum('download_count');

        return view('admin.archives.index', compact(
            'archives', 
            'categories', 
            'departments', 
            'fileTypes',
            'months', 
            'years',  
            'totalAktif',
            'totalHilang',
            'totalUnduhan'
        ));
    }

    /**
     * DAFTAR ARSIP PUBLIK (Landing Page)
     */
    public function userIndex(Request $request)
    {
        $departments = Department::where('name', '!=', 'System')->get();
        $selectedDeptId = $request->query('dept');
        $search = $request->get('search');
        $categoryId = $request->get('category_id');
        $fileType = $request->get('file_type');

        $query = Archive::with(['user', 'category.department'])->latest();

        // Filter departemen
        if ($selectedDeptId) {
            $query->whereHas('category', function($q) use ($selectedDeptId) {
                $q->where('department_id', $selectedDeptId);
            });
        }

        // Pencarian teks
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        // Filter kategori
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        // Filter format file
        if ($fileType) {
            $query->where('file_type', strtoupper($fileType));
        }

        $archives = $query->paginate(10)->appends($request->all());

        $groupedArchives = $archives->getCollection()->groupBy(function($item) {
            return $item->category->name ?? 'Umum';
        });

        $currentDeptName = $selectedDeptId 
            ? $departments->firstWhere('id', $selectedDeptId)->name ?? 'Semua Bidang' 
            : 'Semua Bidang';

        // Kategori untuk dropdown filter
        if ($selectedDeptId) {
            $categories = Category::where('department_id', $selectedDeptId)->get();
        } else {
            $categories = Category::with('department')->get();
        }

        // Daftar format file unik
        $fileTypes = Archive::distinct()->orderBy('file_type')->pluck('file_type');

        return view('archive', compact(
            'groupedArchives', 
            'archives', 
            'departments', 
            'currentDeptName', 
            'categories',
            'fileTypes'
        ));
    }

/**
     * SIMPAN ARSIP BARU (Hirarki: archives/{dept_slug}/{YYYY}/{MM})
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if ($user->isUser()) {
            return back()->with('error', 'Akses Ditolak: Role User tidak memiliki izin.');
        }

        if ($user->role->name === 'super_admin' && !$user->isPureSuperAdmin()) {
            return back()->with('error', 'Akses Ilegal: Anda adalah Super Admin tetapi tidak terdaftar!');
        }

        $request->validate([
            'title'        => 'required|string|max:255',
            'category_id'  => 'required|exists:categories,id',
            'file'         => [
                'required',
                'file',
                'max:10240',
                // [MEDIUM-03] Validasi MIME type dari konten file, bukan hanya ekstensi
                function ($attribute, $value, $fail) {
                    $finfo    = new \finfo(FILEINFO_MIME_TYPE);
                    $mimeType = $finfo->file($value->getRealPath());
                    $allowed  = [
                        'application/pdf',
                        'image/jpeg',
                        'image/png',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    ];
                    if (!in_array($mimeType, $allowed)) {
                        $fail("Format file tidak diizinkan. Tipe terdeteksi: {$mimeType}");
                    }
                },
            ],
            'description'  => 'nullable|string|max:5000',
            'archive_date' => 'nullable|date',
        ]);

        $duplicate = Archive::where('title', $request->title)
            ->where('category_id', $request->category_id)
            ->withTrashed()
            ->exists();

        if ($duplicate) {
            return back()->with('error', 'Judul arsip sudah digunakan dalam kategori ini. Silakan gunakan judul lain.');
        }

        // Ambil data kategori sekaligus memuat data departemen terkait (Eager Loading)
        $category = Category::with('department')->findOrFail($request->category_id);

        if (!$user->isPureSuperAdmin()) {
            if ($category->department_id !== $user->department_id) {
                return back()->with('error', 'Akses Ditolak: Kategori ini milik bidang lain!');
            }
        }

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            
            // 1. Tentukan penamaan berkas unik
            $fileName = \Illuminate\Support\Str::slug($request->title) . '-' . time() . '.' . $file->getClientOriginalExtension();
            
            // 2. Baca slug departemen secara dinamis dari kategori target
            $deptSlug = $category->department->slug ?? 'default-dept';
            
            // 3. Susun folder berhirarki rapi: archives/slug-bidang/YYYY/MM
            $folderPath = "archives/{$deptSlug}/" . now()->format('Y/m'); 
            
            // 4. Simpan ke private storage (tidak dapat diakses langsung via URL)
            $path = $file->storeAs($folderPath, $fileName, 'local');

            $ArchiveDate = $request->filled('archive_date')
                ? \Carbon\Carbon::parse($request->archive_date)
                : now();

            $archive = Archive::create([
                'title'           => $request->title,
                'hash_token'      => \Illuminate\Support\Str::random(16),
                'file_path'       => $path, 
                'file_type'       => strtoupper($file->getClientOriginalExtension()),
                'file_size'       => $this->formatBytes($file->getSize()),
                'category_id'     => $request->category_id,
                'user_id'         => $user->id,
                'description'     => $request->description,
                'download_count'  => 0,
                'archive_date'    => $ArchiveDate,
            ]);

            // LOG AKTIVITAS
            log_activity($user, 'tambah_arsip', "Arsip ({$archive->title}) Dengan (ID: {$archive->id}) berhasil diunggah ke kategori ({$category->name}).");

            // Kirim Notifikasi Email ke Super Admin, Admin, dan User di Bidang yang Sama
            try {
                $recipients = \App\Models\User::where('status', 'approved')
                    ->where(function($q) use ($category) {
                        $q->where('department_id', $category->department_id)
                          ->orWhereHas('role', function($rq) {
                              $rq->where('name', 'super_admin');
                          });
                    })
                    ->where('id', '!=', $user->id)
                    ->get();

                foreach ($recipients as $recipient) {
                    $recipient->notify(new \App\Notifications\NewArchiveNotification($archive));
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Gagal mengirim email notifikasi arsip baru: " . $e->getMessage());
            }

            return back()->with('success', 'Arsip berhasil diunggah!');
        }

        return back()->with('error', 'Gagal mengunggah file.');
    }

    /**
     * UPDATE ARSIP (Hirarki: archives/{dept_slug}/{YYYY}/{MM} saat berkas diganti)
     */
    public function update(Request $request, Archive $archive)
    {
        $user = Auth::user();

        // 1. Proteksi Hak Akses Berdasarkan Role
        if ($user->isUser()) {
            return back()->with('error', 'Akses Ditolak: Role User tidak memiliki izin.');
        }

        if ($user->role->name === 'super_admin' && !$user->isPureSuperAdmin()) {
            return back()->with('error', 'Akses Ilegal: Anda adalah Super Admin tetapi tidak terdaftar!');
        }

        // 2. Validasi Inputan Data Form
        $request->validate([
            'title'        => 'required|string|max:255',
            'category_id'  => 'required|exists:categories,id',
            'file'         => [
                'nullable',
                'file',
                'max:20480',
                // [MEDIUM-03] Validasi MIME type dari konten file
                function ($attribute, $value, $fail) {
                    if (!$value) return;
                    $finfo    = new \finfo(FILEINFO_MIME_TYPE);
                    $mimeType = $finfo->file($value->getRealPath());
                    $allowed  = [
                        'application/pdf',
                        'image/jpeg',
                        'image/png',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'application/msword',
                        'application/vnd.ms-excel',
                        'text/csv',
                        'image/webp',
                    ];
                    if (!in_array($mimeType, $allowed)) {
                        $fail("Format file tidak diizinkan. Tipe terdeteksi: {$mimeType}");
                    }
                },
            ],
            'description'  => 'nullable|string|max:5000',
            'archive_date' => 'nullable|date',
        ]);

        // 3. Validasi Duplikasi Judul (Kecuali untuk ID Arsip ini sendiri)
        $duplicate = Archive::where('title', $request->title)
            ->where('category_id', $request->category_id)
            ->where('id', '!=', $archive->id) 
            ->withTrashed()
            ->exists();

        if ($duplicate) {
            return back()->with('error', 'Judul arsip sudah digunakan dalam kategori ini. Silakan gunakan judul lain.');
        }

        // 4. Validasi Batasan Departemen/Bidang beserta Eager Loading
        $category = Category::with('department')->findOrFail($request->category_id);

        if (!$user->isPureSuperAdmin()) {
            if ($category->department_id !== $user->department_id) {
                return back()->with('error', 'Akses Ditolak: Kategori ini milik bidang lain!');
            }
        }

        // Simpan info nama lama untuk keperluan pencatatan log aktivitas
        $oldTitle = $archive->title;

        // 5. Pengolahan Penggantian File Fisik (Jika mengunggah file baru)
        if ($request->hasFile('file')) {
            // Hapus file fisik lama di dalam private storage jika tercatat & ada berkasnya
            if ($archive->file_path && Storage::disk('local')->exists($archive->file_path)) {
                Storage::disk('local')->delete($archive->file_path);
            }

            // Simpan file baru ke private storage
            $file = $request->file('file');
            $fileName = Str::slug($request->title) . '-' . time() . '.' . $file->getClientOriginalExtension();
            
            // Ambil nama slug bidang terbaru
            $deptSlug = $category->department->slug ?? 'default-dept';
            $folderPath = "archives/{$deptSlug}/" . now()->format('Y/m');
            
            $path = $file->storeAs($folderPath, $fileName, 'local');

            // Update informasi berkas fisik di database
            $archive->file_path = $path;
            $archive->file_type = strtoupper($file->getClientOriginalExtension());
            $archive->file_size = $this->formatBytes($file->getSize());
        }

        // 6. Update Informasi Metadata Teks ke Database
        $archive->title = $request->title;
        $archive->category_id = $request->category_id;
        $archive->description = $request->description;

        // Logika tanggal arsip: jika diisi gunakan inputan, jika dikosongkan pertahankan tanggal lama
        if ($request->filled('archive_date')) {
            $archive->archive_date = \Carbon\Carbon::parse($request->archive_date);
        }

        $archive->save();

        // 7. Catat Log Aktivitas
        log_activity(
            $user, 
            'ubah_arsip', 
            "User ({$user->name}) mengubah arsip: ({$oldTitle}) menjadi ({$archive->title}) Dengan (ID: {$archive->id}) pada kategori ({$category->name})."
        );

        // 8. Redirect dengan session flash message sukses
        return redirect()->route('admin.archives.index')->with('success', 'Arsip berhasil diperbarui.');
    }

/**
 * STREAM PREVIEW ARSIP (Satu fungsi untuk Admin & User berdasarkan Hash Token)
 */
public function preview($token)
{
    // Ambil data arsip berdasarkan hash_token unik
    $archive = Archive::where('hash_token', $token)->firstOrFail();

    $user = Auth::user();

    // Jika pembuka dokumen adalah Admin/Super Admin, pastikan dia berhak melihat departemen tersebut
    if (!$user->isUser()) {
        if (!$this->hasArchiveAccess($archive)) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki wewenang melihat dokumen departemen ini.');
        }
    }

    // Gunakan private disk (local) — file tidak dapat diakses langsung via URL
    if (!Storage::disk('local')->exists($archive->file_path)) {
        abort(404, 'File fisik dokumen tidak ditemukan atau telah dihapus dari server storage.');
    }

    $filePath = Storage::disk('local')->path($archive->file_path);
    $mimeType = Storage::disk('local')->mimeType($archive->file_path);

    // Gunakan basename untuk menghindari path injection di header
    $safeFilename = basename($archive->file_path);

    // Stream file secara langsung dengan security headers
    return response()->file($filePath, [
        'Content-Type'           => $mimeType,
        'Content-Disposition'    => 'inline; filename="' . $safeFilename . '"',
        'X-Content-Type-Options' => 'nosniff',
        'Cache-Control'          => 'no-store, no-cache, must-revalidate',
    ]);
}

    /**
     * DOWNLOAD ARSIP
     */
    public function download(Archive $archive)
    {
        $user = Auth::user();

        // [HIGH-01] Cek otorisasi: Admin hanya bisa download arsip departemennya sendiri
        if (!$user->isUser()) {
            if (!$this->hasArchiveAccess($archive)) {
                abort(403, 'Akses Ditolak: Anda tidak berhak mengunduh arsip dari departemen lain.');
            }
        }

        // Gunakan private disk (local)
        if (!Storage::disk('local')->exists($archive->file_path)) {
            return back()->with('error', 'File fisik tidak ditemukan di server.');
        }

        // Increment hits download
        $archive->increment('download_count');

        // Catat ke log
        log_activity(
            $user,
            'unduh_arsip',
            "User ({$user->name}) mengunduh arsip: ({$archive->title}) (ID: {$archive->id})"
        );

        // Nama file aman: slug judul + ekstensi
        $safeFilename = Str::slug($archive->title) . '.' . strtolower($archive->file_type);

        return Storage::disk('local')->download($archive->file_path, $safeFilename);
    }

/**
     * SOFT DELETE (Pindahkan ke Sampah)
     */
    public function destroy(Request $request, Archive $archive)
    {
        // 1. Cek Hak Akses Terlebih Dahulu
        if (!$this->hasArchiveAccess($archive)) {
            return back()->with('error', 'Anda tidak berhak menghapus arsip ini.');
        }

        $user = Auth::user();
        $title = $archive->title;

        // 2. Validasi Input delete_reason
        $request->validate([
            'delete_reason' => 'nullable|string|max:255',
        ]);

        // 3. Ambil alasan asli atau null jika kosong
        // $request->delete_reason otomatis bernilai null jika form tidak diisi / kosong
        $reason = $request->filled('delete_reason') ? $request->delete_reason : null;

        // 4. Simpan alasan ke model arsip (bisa bernilai string atau null) sebelum di-soft delete
        $archive->delete_reason = $reason;
        $archive->save(); 

        // 5. Jalankan Soft Delete
        $archive->delete();

        // 6. Tentukan teks tampilan untuk Log & Alert jika nilainya null
        $displayText = $reason ?? 'Tidak diberi alasan';

        // 7. Catat Aktivitas Log dengan format kustom
        $logMessage = "Arsip ({$title}) (ID: {$archive->id}) dipindahkan atau dinonaktifkan oleh ({$user->name}). Alasan: ({$displayText}).";
        log_activity($user, 'hapus_sementara_arsip', $logMessage);

        // 8. Kembalikan dengan feedback message
        return back()->with('success', "Arsip berhasil dipindahkan ke tempat sampah. Keterangan: {$displayText}.");
    }

    /**
     * EXPORT EXCEL ARSIP
     */
    public function exportExcel(Request $request, ?Archive $archive = null)
    {
        $user = Auth::user();
        if (!$user) {
            abort(401);
        }

        if ($archive) {
            $filename = 'Arsip_' . \Illuminate\Support\Str::slug($archive->title) . '_' . date('Ymd') . '.xlsx';
            return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\ArchiveExport([], $archive), $filename);
        }

        $filters = $request->only(['search', 'category_id', 'department_id', 'file_type']);
        if (!$user->isPureSuperAdmin() && $user->isAdmin()) {
            $filters['department_id'] = $user->department_id;
        }

        $filename = 'Rekap_Arsip_Digital_' . date('Ymd_His') . '.xlsx';
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\ArchiveExport($filters), $filename);
    }

    /**
     * EXPORT PDF ARSIP
     */
    public function exportPdf(Request $request, ?Archive $archive = null)
    {
        $user = Auth::user();
        if (!$user) {
            abort(401);
        }

        if ($archive) {
            $archives = collect([$archive]);
            $filename = 'Arsip_' . \Illuminate\Support\Str::slug($archive->title) . '_' . date('Ymd') . '.pdf';
        } else {
            $filters = $request->only(['search', 'category_id', 'department_id', 'file_type']);
            $query = Archive::with(['category.department', 'user']);

            if (!$user->isPureSuperAdmin() && $user->isAdmin()) {
                $query->whereHas('category', fn($q) => $q->where('department_id', $user->department_id));
            }
            if (!empty($filters['search'])) {
                $query->where('title', 'LIKE', '%' . $filters['search'] . '%');
            }
            if (!empty($filters['category_id'])) {
                $query->where('category_id', $filters['category_id']);
            }
            if (!empty($filters['department_id'])) {
                $query->whereHas('category', fn($q) => $q->where('department_id', $filters['department_id']));
            }
            if (!empty($filters['file_type'])) {
                $query->where('file_type', $filters['file_type']);
            }

            $archives = $query->latest('archive_date')->get();
            $filename = 'Rekap_Arsip_Digital_' . date('Ymd_His') . '.pdf';
        }

        $tanggal = \Carbon\Carbon::now()->translatedFormat('d F Y');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.archives.pdf', compact('archives', 'tanggal'))
            ->setPaper('a4', 'landscape')
            ->setOptions([
                'dpi'                  => 150,
                'defaultFont'          => 'helvetica',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => false,
            ]);

        return $pdf->download($filename);
    }

    /**
     * FORMAT UKURAN FILE (Helper)
     */
    private function formatBytes(int|float $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}