<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Archive;
use App\Models\Category;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ArchiveTrashController extends Controller
{
    /**
     * HELPER PRIVAT: Cek Hak Akses Arsip Berdasarkan Bidang/Departemen
     */
    private function hasArchiveAccess(Archive $archive): bool
    {
        $user = Auth::user();
    
        if ($user->isPureSuperAdmin()) {
            return true;
        }
    
        return $archive->category && $archive->category->department_id === $user->department_id;
    }

    /**
     * TEMPAT SAMPAH (Trash dengan Filter & 4 Card Statistik)
     */
    public function trash(Request $request)
    {
        $user = Auth::user();
        $search = $request->get('search');
        $categoryId = $request->get('category_id');
        $fileType = $request->get('file_type');
        $departmentId = $request->get('department_id');
        $statusFile = $request->get('status_file'); 
        $month = $request->get('month'); 
        $year = $request->get('year');   
        $filterReason = $request->get('filter_reason'); // Ambil parameter filter baru

        // Query dasar mengambil data yang soft-deleted saja (onlyTrashed)
        $query = Archive::onlyTrashed()->with(['category.department', 'user']);

        // Filter hak akses departemen (untuk admin bidang)
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

        // Filter Bulan & Tahun
        if ($month) {
            $query->whereMonth('archive_date', $month);
        }
        if ($year) {
            $query->whereYear('archive_date', $year);
        }

        // Filter Alasan Terhapus (Query Level)
        if ($filterReason === 'dengan_alasan') {
            $query->whereNotNull('delete_reason');
        } elseif ($filterReason === 'tanpa_alasan') {
            $query->whereNull('delete_reason');
        }

        // --- AMBIL DATA EVALUASI UNTUK STATISTIK CARD ---
        $allFilteredTrash = $query->get();

        // Filter berlapis dengan hasArchiveAccess() demi keamanan data riil
        $allFilteredTrash = $allFilteredTrash->filter(function ($archive) {
            return $this->hasArchiveAccess($archive);
        });

        // 1. Total Terhapus (Card 1)
        $totalTrash = $allFilteredTrash->count();
        
        // Inisialisasi counter card lainnya
        $totalFileHilang = 0; // Card 2
        $withReason = 0;      // Card 3
        $withoutReason = 0;   // Card 4

        foreach ($allFilteredTrash as $arch) {
            // Hitung File Hilang di Storage
            $exists = $arch->file_path && Storage::disk('public')->exists($arch->file_path);
            if (!$exists) {
                $totalFileHilang++;
            }

            // Hitung berdasarkan ada/tidaknya alasan hapus (delete_reason yang bernilai null atau string)
            if ($arch->delete_reason !== null && trim($arch->delete_reason) !== '') {
                $withReason++;
            } else {
                $withoutReason++;
            }
        }

        // --- LOGIK FILTER STATUS FILE (TERSEDIA / HILANG) ---
        if ($statusFile === 'tersedia' || $statusFile === 'hilang') {
            $matchingIds = $allFilteredTrash->filter(function ($arch) use ($statusFile) {
                $exists = $arch->file_path && Storage::disk('public')->exists($arch->file_path);
                return $statusFile === 'tersedia' ? $exists : !$exists;
            })->pluck('id');

            $query->whereIn('id', $matchingIds);
        }

        // Eksekusi Pagination (Gunakan paginate agar halaman tidak overload)
        $rawPagination = $query->latest()->paginate(10)->appends($request->all());
        
        // Memastikan hasil pagination lolos pengecekan hasArchiveAccess
        $archives = $rawPagination->setCollection(
            $rawPagination->getCollection()->filter(function($archive) {
                return $this->hasArchiveAccess($archive);
            })
        );

        // --- DATA DROPDOWN FILTER UNTUK VIEW ---
        $categories = Category::with('department')->when(!$user->isPureSuperAdmin(), function($q) use ($user) {
            return $q->where('department_id', $user->department_id);
        })->get();

        $departments = Department::where('name', '!=', 'System')->get();
        $fileTypes = Archive::onlyTrashed()->distinct()->orderBy('file_type')->pluck('file_type');

        $months = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
            '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
            '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];

        $years = Archive::onlyTrashed()->whereNotNull('archive_date')
            ->selectRaw('YEAR(archive_date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('admin.archives.trash', compact(
            'archives', 
            'categories', 
            'departments', 
            'fileTypes',
            'months', 
            'years',  
            'totalTrash',
            'totalFileHilang',
            'withReason',
            'withoutReason'
        ));
    }

    /**
     * RESTORE ARSIP
     */
    public function restore(int $id)
    {
        $archive = Archive::withTrashed()->findOrFail($id);
        
        if (!$this->hasArchiveAccess($archive)) {
            return back()->with('error', 'Akses ditolak.');
        }

        $archive->restore();
        
        log_activity(Auth::user(), 'pulihkan_arsip', "Arsip ({$archive->title}) (ID: {$archive->id}) berhasil dipulihkan.");

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

        if ($filePath && Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
        }

        $archive->forceDelete();
        
        log_activity(Auth::user(), 'hapus_permanen_arsip', "Arsip ({$title}) Dengan (ID: {$id}) dihapus secara permanen.");

        return back()->with('success', 'Arsip telah dihapus permanen.');
    }
}