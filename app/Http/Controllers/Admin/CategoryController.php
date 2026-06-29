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
        // 1. Hak Akses ditaruh paling atas
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
        // 1. Hak Akses ditaruh paling atas
        $authUser = $this->AllAdminAccess();

        $dept_id = ($authUser->isPureSuperAdmin() && $request->filled('department_id')) 
            ? $request->department_id 
            : $authUser->department_id;

        $request->validate([
            'department_id' => $authUser->isPureSuperAdmin() ? 'nullable|exists:departments,id' : 'nullable',
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('categories')->where(fn ($q) => $q->where('department_id', $dept_id))
            ],
            'description' => 'nullable|string'
        ]);

        $category = Category::create([
            'department_id' => $dept_id,
            'name'          => $request->name,
            'slug'          => Str::slug($request->name),
            'description'   => $request->description,
        ]);

        $category->load('department');

        log_activity($authUser, 'tambah_kategori', "Kategori ({$category->name}) berhasil ditambahkan di departemen ({$category->department->name}).");

        return back()->with('success', 'Kategori berhasil ditambahkan.');
    }

    // Mengubah parameter dari Category $category menjadi $id untuk mengamankan kebocoran informasi data
    public function update(Request $request, $id)
    {
        // 1. Ambil data mentah dulu demi mengekstrak departemen asal
        $category = Category::findOrFail($id);

        // 2. Hak Akses ditaruh di atas sebelum manipulasi data dijalankan
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
            'name'        => $request->name,
            'slug'        => Str::slug($request->name),
            'description' => $request->description,
        ]);

        log_activity($authUser, 'ubah_kategori', "Kategori ({$oldName}) diubah menjadi ({$category->name}).");

        return back()->with('success', 'Kategori berhasil diperbarui.');
    }

    public function migrateArchives(Request $request)
    {
        // 1. Hak Akses awal ditaruh paling atas (Akses Admin Umum)
        $authUser = $this->AllAdminAccess();

        // 2. Baru lakukan validasi muatan form request
        $request->validate([
            'source_category_id' => 'required|exists:categories,id',
            'target_category_id' => 'required|exists:categories,id',
            'password'           => 'required'
        ]);

        $category = Category::findOrFail($request->source_category_id);

        // 3. Hak Akses tingkat lanjut: Cek apakah dia berhak mengelola departemen asal kategori tersebut
        if (!$authUser->isPureSuperAdmin() && !$authUser->isFromDepartment($category->department_id)) {
            abort(403, 'Akses Ditolak: Anda bukan admin dari departemen ini.');
        }

        // 4. Verifikasi keamanan password
        if (!Hash::check($request->password, $authUser->password)) {
            return back()->with('error', 'Konfirmasi gagal. Password yang Anda masukkan salah!');
        }

        $targetCategory = Category::findOrFail($request->target_category_id);

        // Cek akses ke departemen kategori tujuan (jika bukan super admin)
        if (!$authUser->isPureSuperAdmin()) {
            if ($targetCategory->department_id !== $authUser->department_id) {
                return back()->with('error', 'Tidak dapat memindahkan arsip ke kategori dari departemen yang berbeda.');
            }
        }

        if ($category->id === $targetCategory->id) {
            return back()->with('error', 'Kategori sumber dan tujuan tidak boleh sama.');
        }

        $archivesCount = $category->archives()->count();

        if ($archivesCount === 0) {
            return back()->with('error', 'Kategori ini tidak memiliki arsip untuk dipindahkan.');
        }

        Archive::where('category_id', $category->id)
            ->update(['category_id' => $targetCategory->id]);

        log_activity($authUser, 'pindah_kategori', "Memindahkan ({$archivesCount}) arsip dari kategori ({$category->name}) ke kategori ({$targetCategory->name}).");

        return back()->with('success', "Berhasil memindahkan {$archivesCount} arsip ke kategori '{$targetCategory->name}'.");
    }

    // Mengubah parameter dari Category $category menjadi $id untuk mengamankan kebocoran informasi data
    public function destroy(Request $request, $id)
    {
        // 1. Ambil data mentah dulu demi mengekstrak departemen asal
        $category = Category::findOrFail($id);

        // 2. Hak Akses ditaruh di atas sebelum manipulasi data dilakukan
        $authUser = $this->AllAdminAccess($category->department_id);

        $request->validate(['password' => 'required']);

        if (!Hash::check($request->password, $authUser->password)) {
            return back()->with('error', 'Konfirmasi gagal. Password yang Anda masukkan salah!');
        }

        $archivesCount = $category->archives()->count();

        if ($archivesCount > 0) {
            return back()->with('error', "Kategori tidak dapat dihapus karena masih memiliki {$archivesCount} arsip. Pindahkan atau hapus arsip terlebih dahulu.");
        }

        $categoryName = $category->name;
        $deptName = $category->department->name ?? 'Tidak Ada Departemen';
        
        $category->delete();

        log_activity($authUser, 'hapus_kategori', "Kategori ({$categoryName}) dari departemen ({$deptName}) berhasil dihapus.");

        return back()->with('success', 'Kategori berhasil dihapus.');
    }
}