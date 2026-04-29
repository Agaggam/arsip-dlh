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
    private function hasArchiveAccess($archive)
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
    public function index()
    {
        $user = Auth::user();
        $query = Archive::with(['category.department', 'user']);

        if (!$user->isPureSuperAdmin()) {
            $query->whereHas('category', function($q) use ($user) {
                $q->where('department_id', $user->department_id);
            });
        }

        // Paginate 10 data untuk dashboard admin
        $archives = $query->latest()->paginate(10);
        
        $categories = Category::when(!$user->isPureSuperAdmin(), function($q) use ($user) {
            return $q->where('department_id', $user->department_id);
        })->get();

        return view('admin.archives.index', compact('archives', 'categories'));
    }

    /**
     * DAFTAR ARSIP PUBLIK (Landing Page)
     */
    public function userIndex(Request $request)
    {
        // 1. Ambil semua departemen kecuali 'System'
        $departments = Department::where('name', '!=', 'System')->get();

        // 2. Ambil ID departemen dari URL jika ada
        $selectedDeptId = $request->query('dept');

        $query = Archive::with(['user', 'category.department'])->latest();

        // 3. Filter berdasarkan departemen jika dipilih
        if ($selectedDeptId) {
            $query->whereHas('category', function($q) use ($selectedDeptId) {
                $q->where('department_id', $selectedDeptId);
            });
        }

        // 4. Paginate dulu datanya (10 per halaman) 
        // appends(request()->all()) agar filter 'dept' tidak hilang saat klik page 2
        $archives = $query->paginate(10)->appends($request->all());
        
        // 5. Grouping HASIL pagination-nya saja agar tidak error links()
        $groupedArchives = $archives->getCollection()->groupBy(function($item) {
            return $item->category->name ?? 'Umum';
        });

        // 6. Nama departemen untuk judul halaman
        $currentDeptName = $selectedDeptId 
            ? $departments->firstWhere('id', $selectedDeptId)->name ?? 'Semua Bidang' 
            : 'Semua Bidang';

        return view('archive', compact('groupedArchives', 'archives', 'departments', 'currentDeptName'));
    }

    /**
     * SIMPAN ARSIP BARU (DENGAN PROTEKSI KRUSIAL)
     */
    public function store(Request $request)
    {
        $user = Auth::user();


        // 1. Cek apakah role 'user'
        if ($user->isUser()) {
            return back()->with('error', 'Akses Ditolak: Role User tidak memiliki izin.');
        }

        // 2. PROTEKSI KRUSIAL: Cek apakah dia super_admin tapi bukan dari departemen System
        // Jika benar, maka tindakannya dianggap ilegal.
        if ($user->role->name === 'super_admin' && !$user->isPureSuperAdmin()) {
            return back()->with('error', 'Akses Ilegal: Anda adalah Super Admin tetapi tidak terdaftar!');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png,docx,xlsx|max:10240',
            'description' => 'nullable|string',
        ]);

        $category = Category::findOrFail($request->category_id);

        // 3. Validasi Bidang: Pastikan bukan milik departemen lain
        if (!$user->isPureSuperAdmin()) {
            if ($category->department_id !== $user->department_id) {
                return back()->with('error', 'Akses Ditolak: Kategori ini milik bidang lain!');
            }
        }

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = Str::slug($request->title) . '-' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('archives', $fileName, 'public');

            Archive::create([
                'title' => $request->title,
                'file_path' => $path,
                'file_type' => strtoupper($file->getClientOriginalExtension()),
                'file_size' => $this->formatBytes($file->getSize()),
                'category_id' => $request->category_id,
                'user_id' => $user->id,
                'description' => $request->description,
                'download_count' => 0,
            ]);

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

        $archive->delete();
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
    public function restore($id)
    {
        $archive = Archive::withTrashed()->findOrFail($id);
        
        if (!$this->hasArchiveAccess($archive)) {
            return back()->with('error', 'Akses ditolak.');
        }

        $archive->restore();
        return back()->with('success', 'Arsip berhasil dipulihkan.');
    }

    /**
     * HAPUS PERMANEN
     */
    public function forceDelete(Request $request, $id)
    {
        $request->validate(['password' => 'required']);

        if (!Hash::check($request->password, Auth::user()->password)) {
            return back()->with('error', 'Password salah!');
        }

        $archive = Archive::withTrashed()->findOrFail($id);

        if (!$this->hasArchiveAccess($archive)) {
            return back()->with('error', 'Akses ditolak.');
        }

        if (Storage::disk('public')->exists($archive->file_path)) {
            Storage::disk('public')->delete($archive->file_path);
        }

        $archive->forceDelete();
        return back()->with('success', 'Arsip telah dihapus permanen.');
    }

    /**
     * FORMAT UKURAN FILE (Helper)
     */
    private function formatBytes($bytes, $precision = 2) {
        $units = ['B', 'KB', 'MB', 'GB'];
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}