<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Department;
use App\Models\Archive;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CategoryController extends Controller
{
    /**
     * Private helper: Pastikan hanya super admin asli yang bisa mengakses.
     */
    private function SuperAdminAccess(): User
    {
        $user = Auth::user();
        
        if (!$user || !$user->isPureSuperAdmin()) {
            abort(403, 'Akses Ditolak: Hanya super admin asli yang diizinkan.');
        }
        
        return $user;
    }

    /**
     * Private helper: Pastikan hanya super admin atau admin departemen yang sesuai yang bisa mengakses.
     */
    private function AllAdminAccess(?int $department_id = null): User
    {
        $user = Auth::user();

        if (!$user) {
            abort(403, 'Akses Ditolak: Anda belum login.');
        }

        // Super admin selalu diizinkan
        if ($user->isPureSuperAdmin()) {
            return $user;
        }

        // Cek apakah dia admin
        if ($user->isAdmin()) {
            // Jika ada parameter department_id, cek apakah admin dari departemen tersebut
            if ($department_id !== null && !$user->isFromDepartment($department_id)) {
                abort(403, 'Akses Ditolak: Anda bukan admin dari departemen ini.');
            }
            return $user;
        }

        // Bukan super admin dan bukan admin
        abort(403, 'Akses Ditolak: Hanya super admin atau admin yang diizinkan.');
    }

    // ============================================================
    // CONTROLLER METHODS
    // ============================================================

    public function index(Request $request)
    {
        $authUser = $this->AllAdminAccess();
        
        $search = $request->get('search');
        $deptFilter = $request->get('department_id');
        $archiveFilter = $request->get('archive_filter');

        $query = Category::with('department')->withCount('archives');

        if (!$authUser->isPureSuperAdmin()) {
            $query->where('department_id', $authUser->department_id);
        } else {
            if ($deptFilter) {
                $query->where('department_id', $deptFilter);
            }
        }

        if ($search) {
            $query->where('name', 'LIKE', "%{$search}%");
        }

        if ($archiveFilter === 'has') {
            $query->has('archives');
        } elseif ($archiveFilter === 'empty') {
            $query->doesntHave('archives');
        }

        // Mengganti ->get() menjadi ->paginate() dan mempertahankan filter di URL query string
        $categories = $query->latest()->paginate(10)->withQueryString();

        if ($authUser->isPureSuperAdmin()) {
            $departments = Department::all();
        } else {
            $departments = Department::where('id', $authUser->department_id)->get();
        }

        return view('admin.categories.index', compact('categories', 'departments'));
    }

    public function store(Request $request)
    {
        $authUser = $this->AllAdminAccess();

        $dept_id = $authUser->department_id;

        $request->validate([
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('categories')->where(fn ($q) => $q->where('department_id', $dept_id))
            ],
            'description' => 'nullable|string'
        ]);

        $category = Category::create([
            'department_id' => $dept_id,
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
        ]);

        log_activity($authUser, 'tambah_kategori', "Kategori ({$category->name}) berhasil ditambahkan di departemen ({$category->department->name}).");

        return back()->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function update(Request $request, Category $category)
    {
        $authUser = $this->AllAdminAccess($category->department_id);

        $dept_id = $category->department_id;

        $request->validate([
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('categories')->where(fn ($q) => $q->where('department_id', $dept_id))->ignore($category->id)
            ],
            'description' => 'nullable|string'
        ]);

        $oldName = $category->name;
        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
        ]);

        log_activity($authUser, 'ubah_kategori', "Kategori ({$oldName}) diubah menjadi ({$category->name}).");

        return back()->with('success', 'Kategori berhasil diperbarui.');
    }

    /**
     * Memproses migrasi arsip antar kategori secara massal.
     * Menerima input ID via Query String / Request parameters dari Alpine dynamic action.
     */
    public function migrateArchives(Request $request)
    {
        // 1. Validasi Input Data Form dasar & kehadiran Password
        $request->validate([
            'source_category_id' => 'required|exists:categories,id',
            'target_category_id' => 'required|exists:categories,id',
            'password'           => 'required'
        ]);

        // Find data model kategori sumber berdasarkan request form
        $category = Category::findOrFail($request->source_category_id);

        // Ambil data autentikasi & validasi hak akses departemen asal
        $authUser = $this->AllAdminAccess($category->department_id);

        // 2. Verifikasi keamanan password (dari x-confirm-modal)
        if (!Hash::check($request->password, $authUser->password)) {
            return back()->with('error', 'Konfirmasi gagal. Password yang Anda masukkan salah!');
        }

        // Find data model kategori tujuan
        $targetCategory = Category::findOrFail($request->target_category_id);

        // Cek akses ke departemen kategori tujuan (jika bukan super admin)
        if (!$authUser->isPureSuperAdmin()) {
            if ($targetCategory->department_id !== $authUser->department_id) {
                return back()->with('error', 'Tidak dapat memindahkan arsip ke kategori dari departemen yang berbeda.');
            }
        }

        // Cek apakah kategori sumber dan tujuan sama
        if ($category->id === $targetCategory->id) {
            return back()->with('error', 'Kategori sumber dan tujuan tidak boleh sama.');
        }

        // Hitung jumlah arsip yang akan dipindahkan
        $archivesCount = $category->archives()->count();

        if ($archivesCount === 0) {
            return back()->with('error', 'Kategori ini tidak memiliki arsip untuk dipindahkan.');
        }

        // Pindahkan semua arsip dari kategori lama ke kategori baru menggunakan Query Builder massal
        Archive::where('category_id', $category->id)
            ->update(['category_id' => $targetCategory->id]);

        // Rekam aktivitas log sistem
        log_activity($authUser, 'pindah_kategori', "Memindahkan ({$archivesCount}) arsip dari kategori ({$category->name}) ke kategori ({$targetCategory->name}).");

        return back()->with('success', "Berhasil memindahkan {$archivesCount} arsip ke kategori '{$targetCategory->name}'.");
    }

    public function destroy(Request $request, Category $category)
    {
        $authUser = $this->AllAdminAccess($category->department_id);

        $request->validate(['password' => 'required']);

        if (!Hash::check($request->password, $authUser->password)) {
            return back()->with('error', 'Konfirmasi gagal. Password yang Anda masukkan salah!');
        }

        // Cek apakah kategori masih memiliki arsip
        $archivesCount = $category->archives()->count();

        if ($archivesCount > 0) {
            return back()->with('error', "Kategori tidak dapat dihapus karena masih memiliki {$archivesCount} arsip. Pindahkan atau hapus arsip terlebih dahulu.");
        }

        $categoryName = $category->name;
        $category->delete();

        log_activity($authUser, 'hapus_kategori', "Kategori ({$categoryName}) dari departemen ({$category->department->name}) berhasil dihapus.");

        return back()->with('success', 'Kategori berhasil dihapus.');
    }
}