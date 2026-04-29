<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CategoryController extends Controller
{
    /**
     * FUNGSI AKSES: Cek otoritas pengelolaan
     * Menggunakan konsep Pure Super Admin (Role Super Admin + Dept System)
     */
    private function hasManageAccess($target_department_id)
    {
        $user = Auth::user();

        // 1. Pure Super Admin: Akses mutlak ke semua departemen
        if ($user->isPureSuperAdmin()) {
            return true;
        }

        // 2. Admin Biasa: Hanya boleh jika departemennya cocok
        if ($user->isAdmin() && $user->isFromDepartment($target_department_id)) {
            return true;
        }

        // 3. Super Admin "Palsu" (Bukan Dept System) atau User Biasa: Ditolak
        return false;
    }

    /**
     * TAMPILKAN DAFTAR KATEGORI
     */
    public function index()
    {
        $user = Auth::user();
        $query = Category::with('department')->withCount('archives');

        // Proteksi: Jika dia super_admin tapi bukan dari System, 
        // perlakukan seperti admin biasa (hanya lihat departemennya sendiri)
        if (!$user->isPureSuperAdmin()) {
            $query->where('department_id', $user->department_id);
        }

        $categories = $query->get();
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * SIMPAN KATEGORI
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
            return back()->with('error', 'Akses Ilegal: Anda adalah Super Admin tetapi tidak terdaftar di departemen System!');
        }

        $dept_id = $user->department_id;

        $request->validate([
            'name' => [
                'required', 'string', 'max:255',
                // Validasi unik berdasarkan nama di departemen yang sama
                Rule::unique('categories')->where(fn ($q) => $q->where('department_id', $dept_id))
            ],
            'description' => 'nullable|string'
        ]);
        
        Category::create([
            'department_id' => $dept_id, 
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
        ]);

        return back()->with('success', 'Kategori berhasil ditambahkan.');
    }

    /**
     * HAPUS KATEGORI
     */
    public function destroy(Request $request, Category $category)
    {
        $user = Auth::user();

        // 1. Konfirmasi Password
        $request->validate(['password' => 'required']);

        if (!Hash::check($request->password, $user->password)) {
            return back()->with('error', 'Konfirmasi gagal. Password yang Anda masukkan salah!');
        }

        // 2. Cek Hak Akses (Pure Super Admin vs Admin Departemen)
        // Fungsi hasManageAccess sudah memproteksi super_admin di luar System.
        if (!$this->hasManageAccess($category->department_id)) {
            return back()->with('error', 'Izin Ditolak: Anda tidak memiliki otoritas untuk menghapus data ini.');
        }

        $category->delete();
        return back()->with('success', 'Kategori berhasil dihapus.');
    }
}