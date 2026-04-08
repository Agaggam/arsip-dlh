<?php

namespace App\Http\Controllers;

use App\Models\Archive;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ArchiveController extends Controller
{
    /**
     * Menampilkan daftar arsip aktif
     */
    public function index()
    {
        // Mengambil arsip yang belum dihapus (Soft Delete otomatis terfilter)
        $archives = Archive::with('category')->latest()->get();
        $categories = Category::all();

        return view('admin.archives.index', compact('archives', 'categories'));
    }

public function userIndex()
{
    // Mengambil arsip dan mengelompokkannya berdasarkan nama kategori
    $groupedArchives = \App\Models\Archive::with(['user', 'category'])
        ->latest()
        ->get()
        ->groupBy(function($item) {
            return $item->category->name; // Mengelompokkan berdasarkan nama kategori
        });

    return view('archive', compact('groupedArchives'));
}

    /**
     * Menyimpan arsip baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png,docx,xlsx|max:10240', // Max 10MB
            'description' => 'nullable|string',
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            
            // Ambil info file
            $extension = strtoupper($file->getClientOriginalExtension());
            $fileSize = $this->formatBytes($file->getSize());
            
            // Nama file unik: judul-slug-timestamp.ekstensi
            $fileName = Str::slug($request->title) . '-' . time() . '.' . $file->getClientOriginalExtension();
            
            // Simpan ke storage/app/public/archives
            $path = $file->storeAs('archives', $fileName, 'public');

            Archive::create([
                'title' => $request->title,
                'file_path' => $path,
                'file_type' => $extension,
                'file_size' => $fileSize,
                'category_id' => $request->category_id,
                'user_id' => Auth::id(),
                'description' => $request->description,
                'download_count' => 0,
            ]);
            return back()->with('success', 'Arsip berhasil diunggah!');
        }

        return back()->with('error', 'Gagal mengunggah file.');
    }

    /**
     * Method untuk mengunduh file
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
     * Method untuk Soft Delete (Pindah ke Trash)
     */
    public function destroy(Archive $archive)
    {
        $archive->delete();

        return back()->with('success', 'Arsip berhasil dipindahkan ke tempat sampah.');
    }

    /**
     * Menampilkan daftar arsip di tempat sampah
     */
    public function trash()
    {
        $archives = Archive::onlyTrashed()->with('category')->latest()->get();
        return view('admin.archives.trash', compact('archives'));
    }

    /**
     * Mengembalikan arsip yang dihapus
     */
    public function restore($id)
    {
        $archive = Archive::withTrashed()->findOrFail($id);
        $archive->restore();

        return back()->with('success', 'Arsip berhasil dipulihkan!');
    }

    /**
     * Menghapus permanen arsip dan file fisiknya
     */
    public function forceDelete($id)
    {
        $archive = Archive::withTrashed()->findOrFail($id);

        // Hapus file dari storage fisik
        if (Storage::disk('public')->exists($archive->file_path)) {
            Storage::disk('public')->delete($archive->file_path);
        }

        $archive->forceDelete();

        return back()->with('success', 'Arsip telah dihapus secara permanen.');
    }

    /**
     * Helper untuk format ukuran file (MB/KB)
     */
    private function formatBytes($bytes, $precision = 2) {
        $units = ['B', 'KB', 'MB', 'GB'];
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}