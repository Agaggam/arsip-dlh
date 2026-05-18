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

    $query = Archive::with(['category.department', 'user']);

    // Filter hak akses departemen (untuk admin biasa)
    if (!$user->isPureSuperAdmin()) {
        $query->whereHas('category', function($q) use ($user) {
            $q->where('department_id', $user->department_id);
        });
    }

    // Pencarian teks (judul)
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

    // Filter tipe dokumen
    if ($fileType) {
        $query->where('file_type', strtoupper($fileType));
    }

    // Filter departemen (hanya untuk super admin, karena admin biasa sudah terfilter otomatis)
    if ($user->isPureSuperAdmin() && $departmentId) {
        $query->whereHas('category', function($q) use ($departmentId) {
            $q->where('department_id', $departmentId);
        });
    }

    $archives = $query->latest()->paginate(10)->appends($request->all());

    // Data untuk dropdown filter (kategori dan tipe dokumen)
    $categories = Category::when(!$user->isPureSuperAdmin(), function($q) use ($user) {
        return $q->where('department_id', $user->department_id);
    })->get();

    // Data untuk dropdown departemen (hanya untuk super admin)
    $departments = Department::where('name', '!=', 'System')->get();

    // Data untuk dropdown tipe dokumen (unik)
    $fileTypes = Archive::distinct()->orderBy('file_type')->pluck('file_type');

    return view('admin.archives.index', compact('archives', 'categories', 'departments', 'fileTypes'));
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
    $fileType = $request->get('file_type'); // filter format

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

    // Filter format file (case insensitive)
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

    // daftar format file unik (untuk dropdown)
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
 * SIMPAN ARSIP BARU
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
        'file'         => 'required|file|mimes:pdf,jpg,jpeg,png,docx,xlsx|max:10240',
        'description'  => 'nullable|string',
        'archive_date' => 'nullable|date',
    ]);

    $duplicate = Archive::where('title', $request->title)
        ->where('category_id', $request->category_id)
        ->withTrashed()
        ->exists();

    if ($duplicate) {
        return back()->with('error', 'Judul arsip sudah digunakan dalam kategori ini. Silakan gunakan judul lain.');
    }

    $category = Category::findOrFail($request->category_id);

    if (!$user->isPureSuperAdmin()) {
        if ($category->department_id !== $user->department_id) {
            return back()->with('error', 'Akses Ditolak: Kategori ini milik bidang lain!');
        }
    }

    if ($request->hasFile('file')) {
        $file = $request->file('file');
        $fileName = Str::slug($request->title) . '-' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('archives', $fileName, 'public');

        $ArchiveDate = $request->filled('archive_date')
            ? \Carbon\Carbon::parse($request->archive_date)
            : now();

        $archive = Archive::create([
            'title'           => $request->title,
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
        log_activity($user, 'tambah_arsip', "Arsip '{$archive->title}' (ID: {$archive->id}) berhasil diunggah ke kategori '{$category->name}'.");

        return back()->with('success', 'Arsip berhasil diunggah!');
    }

    return back()->with('error', 'Gagal mengunggah file.');
}

    /**
     * DOWNLOAD ARSIP
     */
    public function download(Archive $archive)
    {
        if (!Storage::disk('public')->exists($archive->file_path)) {
            return back()->with('error', 'File fisik tidak ditemukan di server.');
        }

        $archive->increment('download_count');

        log_activity(Auth::user(), 'download_arsip', "User mendownload arsip: {$archive->title} (ID: {$archive->id})");

        return Storage::disk('public')->download(
            $archive->file_path, 
            $archive->title . '.' . strtolower($archive->file_type)
        );
    }

    /**
     * SOFT DELETE (Pindahkan ke Sampah)
     */
    public function destroy(Request $request, Archive $archive)
    {
        $request->validate(['password' => 'required']);

        if (!Hash::check($request->password, Auth::user()->password)) {
            return back()->with('error', 'Password salah!');
        }

        if (!$this->hasArchiveAccess($archive)) {
            return back()->with('error', 'Anda tidak berhak menghapus arsip ini.');
        }

        $title = $archive->title;
        $archive->delete();

        log_activity(Auth::user(), 'soft_delete_arsip', "Arsip '{$title}' (ID: {$archive->id}) dipindahkan ke tempat sampah.");

        return back()->with('success', 'Arsip dipindahkan ke tempat sampah.');
    }

    /**
     * TEMPAT SAMPAH (Trash)
     */
    public function trash()
    {
        $user = Auth::user();
        $query = Archive::onlyTrashed()->with(['category.department', 'user']);

        if (!$user->isPureSuperAdmin()) {
            $query->whereHas('category', function($q) use ($user) {
                $q->where('department_id', $user->department_id);
            });
        }

        $archives = $query->latest()->get();
        return view('admin.archives.trash', compact('archives'));
    }

    /**
     * RESTORE
     */
    public function restore(int $id)
    {
        $archive = Archive::withTrashed()->findOrFail($id);
        
        if (!$this->hasArchiveAccess($archive)) {
            return back()->with('error', 'Akses ditolak.');
        }

        $archive->restore();
        
        log_activity(Auth::user(), 'restore_arsip', "Arsip '{$archive->title}' (ID: {$archive->id}) berhasil dipulihkan.");

        return back()->with('success', 'Arsip berhasil dipulihkan.');
    }

    /**
     * HAPUS PERMANEN
     */
    public function forceDelete(Request $request, int $id)
    {
        $request->validate(['password' => 'required']);

        if (!Hash::check($request->password, Auth::user()->password)) {
            return back()->with('error', 'Password salah!');
        }

        $archive = Archive::withTrashed()->findOrFail($id);

        if (!$this->hasArchiveAccess($archive)) {
            return back()->with('error', 'Akses ditolak.');
        }

        $title = $archive->title;
        $filePath = $archive->file_path;

        if (Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
        }

        $archive->forceDelete();
        
        log_activity(Auth::user(), 'force_delete_arsip', "Arsip '{$title}' (ID: {$id}) dihapus permanen beserta file.");

        return back()->with('success', 'Arsip telah dihapus permanen.');
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