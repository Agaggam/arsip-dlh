<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Archive;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use ZipArchive;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

class ZipArchiveController extends Controller
{
    /**
     * PROSES UNTUK BULK UPLOAD FILE ZIP (Hirarki: archives/{dept_slug}/{YYYY}/{MM})
     */
    public function storeZip(Request $request)
    {
        // 0. BYPASS TIMEOUT & MEMORY LIMIT
        ini_set('max_execution_time', 300); // 5 Menit
        ini_set('memory_limit', '512M');    // Naikkan batas RAM PHP sementara

        $user = Auth::user();

        // 1. Validasi Akses
        if ($user->isUser()) {
            return back()->with('error', 'Akses Ditolak: Role User tidak memiliki izin.');
        }

        if ($user->role->name === 'super_admin' && !$user->isPureSuperAdmin()) {
            return back()->with('error', 'Akses Ilegal: Anda adalah Super Admin tetapi tidak terdaftar!');
        }

        // 2. Validasi File ZIP & Kategori
        $request->validate([
            'zip_file'    => 'required|file|mimes:zip|max:20480',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        // 3. Tentukan Kategori Penampung dengan Memuat Relasi Departemen (Eager Loading)
        if ($request->filled('category_id')) {
            $category = Category::with('department')->findOrFail($request->category_id);
        } else {
            $targetDeptId = $user->isPureSuperAdmin() ? 1 : $user->department_id;
            
            $category = Category::firstOrCreate(
                [
                    'name'          => 'Belum Dikategorikan',
                    'department_id' => $targetDeptId
                ],
                [
                    'slug'        => Str::slug('Belum Dikategorikan'),
                    'description' => 'Kategori default untuk arsip yang di-upload melalui fitur bulk ZIP tanpa kategori spesifik.',
                ]
            );
            // Muat relasi jika kategori baru saja dibuat
            $category->load('department');
        }

        $file = $request->file('zip_file');
        $zip = new ZipArchive;

        // Buat nama folder temporary unik
        $tempFolderName = 'temp_' . time() . '_' . Str::random(5);
        $tempPath = storage_path('app/' . $tempFolderName);

        // 4. Mulai Ekstraksi ZIP
        if ($zip->open($file->getRealPath()) === TRUE) {
            $zip->extractTo($tempPath);
            $zip->close();

            $uploadedCount = 0;
            $duplicateCount = 0; 

            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($tempPath, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::LEAVES_ONLY
            );

            // Ambil slug departemen secara dinamis dari kategori target
            $deptSlug = $category->department->slug ?? 'default-dept';

            foreach ($files as $fileInfo) {
                if ($fileInfo->isFile()) {
                    $fullPath = $fileInfo->getRealPath();
                    $fileNameWithExt = $fileInfo->getFilename();
                    
                    if (str_starts_with($fileNameWithExt, '.')) {
                        continue;
                    }

                    $extension = strtolower(pathinfo($fileNameWithExt, PATHINFO_EXTENSION));

                    // 5. Filter Ekstensi Valid
                    if (in_array($extension, ['pdf', 'jpg', 'jpeg', 'png', 'docx', 'xlsx'])) {
                        
                        $titleWithoutExt = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
                        
                        // Proteksi Duplikasi Judul
                        $duplicate = Archive::where('title', $titleWithoutExt)
                            ->where('category_id', $category->id)
                            ->withTrashed()
                            ->exists();

                        if ($duplicate) {
                            $duplicateCount++; 
                            continue;
                        }

                        // === IMPLEMENTASI HIRARKI FOLDER BARU (DEPARTEMEN/TAHUN/BULAN) ===
                        // Buat nama file unik
                        $newFileName = Str::slug($titleWithoutExt) . '-' . time() . '.' . $extension;
                        
                        // Rangkai folder dinamis berbasis slug departemen + waktu saat ini
                        $folderPath = "archives/{$deptSlug}/" . now()->format('Y/m');
                        
                        // Kombinasikan menjadi path lengkap database
                        $storagePath = $folderPath . '/' . $newFileName;

                        // Ambil konten file temp dan simpan ke disk public dengan path hirarki baru
                        $fileContent = file_get_contents($fullPath);
                        Storage::disk('public')->put($storagePath, $fileContent);
                        // ==============================================================

                        // 6. Tulis data ke tabel archives
                        Archive::create([
                            'hash_token'     => Str::random(16), 
                            'title'          => $titleWithoutExt,
                            'file_path'      => $storagePath, 
                            'file_type'      => strtoupper($extension),
                            'file_size'      => $this->formatBytes($fileInfo->getSize()),
                            'category_id'    => $category->id, 
                            'user_id'        => $user->id,
                            'description'    => 'Hasil ekstrak otomatis bulk-upload ZIP.',
                            'download_count' => 0,
                            'archive_date'   => now(),
                        ]);

                        $uploadedCount++;
                    }
                }
            }

            // 7. Bersihkan sisa folder temporary lokal beserta isinya
            $this->deleteDir($tempPath);

            // Blok Pengkondisian Notifikasi Response
            if ($uploadedCount > 0) {
                log_activity($user, 'tambah_arsip_via_zip', "Berhasil mengekstrak ({$uploadedCount}) berkas baru dari file ZIP.");
                
                $msg = "Berhasil mengekstrak dan menyimpan ({$uploadedCount}) arsip baru.";
                if ($duplicateCount > 0) {
                    $msg .= " ({$duplicateCount} berkas dilewati karena duplikat).";
                }
                return back()->with('success', $msg);
            }

            if ($duplicateCount > 0) {
                return back()->with('error', "Gagal unggah: Berkas di dalam ZIP sudah pernah di-upload sebelumnya (Terdeteksi {$duplicateCount} duplikat).");
            }

            return back()->with('error', 'Tidak ditemukan berkas dokumen yang valid (.pdf, .xlsx, .docx, .png, .jpg) di dalam ZIP.');
        }

        return back()->with('error', 'Gagal membuka atau mengekstrak file ZIP.');
    }

    /**
     * HELPER: Format Ukuran Berkas
     */
    private function formatBytes(int|float $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * HELPER: Hapus Folder Beserta Seluruh Isinya secara Rekursif
     */
    private function deleteDir($dirPath) {
        if (!is_dir($dirPath)) {
            return;
        }
        $files = array_diff(scandir($dirPath), ['.','..']);
        foreach ($files as $file) {
            (is_dir("$dirPath/$file")) ? $this->deleteDir("$dirPath/$file") : unlink("$dirPath/$file");
        }
        return rmdir($dirPath);
    }
}