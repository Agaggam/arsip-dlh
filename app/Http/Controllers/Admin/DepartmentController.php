<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class DepartmentController extends Controller
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

    // ============================================================
    // CONTROLLER METHODS
    // ============================================================

    /**
     * Menampilkan daftar departemen.
     */
    public function index()
    {
        $authUser = $this->SuperAdminAccess();
        
        $departments = Department::withCount(['users', 'categories', 'archives'])->get();
        
        return view('admin.departments.index', compact('departments'));
    }

    /**
     * Menyimpan departemen baru.
     */
    public function store(Request $request)
    {
        $authUser = $this->SuperAdminAccess();

        $request->validate([
            'name'        => 'required|string|max:255|unique:departments,name',
            'description' => 'nullable|string',
        ]);

        $department = Department::create([
            'name'        => $request->name,
            'slug'        => Str::slug($request->name),
            'description' => $request->description,
        ]);

        log_activity($authUser, 'tambah_departemen', "Departemen ({$department->name}) berhasil ditambahkan.");

        return redirect()->route('admin.departments.index')
                        ->with('success', 'Departemen baru berhasil ditambahkan.');
    }

    /**
     * Memperbarui data departemen (via modal).
     */
    public function update(Request $request, Department $department)
    {
        $authUser = $this->SuperAdminAccess();

        // Departemen System tidak boleh diubah
        if ($department->name === 'System') {
            return back()->with('error', 'Departemen System tidak boleh diubah.');
        }

        $request->validate([
            'name'        => 'required|string|max:255|unique:departments,name,' . $department->id,
            'description' => 'nullable|string',
        ]);

        $oldName = $department->name;
        $department->update([
            'name'        => $request->name,
            'slug'        => Str::slug($request->name),
            'description' => $request->description,
        ]);

        log_activity($authUser, 'ubah_departemen', "Departemen ({$oldName}) diubah menjadi ({$department->name}).");

        return redirect()->route('admin.departments.index')
                        ->with('success', 'Data departemen berhasil diperbarui.');
    }

    /**
     * Menghapus departemen jika tidak memiliki data terkait.
     */
    public function destroy(Request $request, Department $department)
    {
        $authUser = $this->SuperAdminAccess();

        // Departemen System tidak boleh dihapus
        if ($department->name === 'System') {
            return back()->with('error', 'Departemen System tidak bisa dihapus.');
        }

        // Cek apakah masih ada data terkait
        $totalCategories = $department->categories()->count();
        $totalUsers      = $department->users()->count();
        $totalArchives   = $department->categories()->withCount('archives')->get()->sum('archives_count');

        if ($totalCategories > 0 || $totalUsers > 0 || $totalArchives > 0) {
            return back()->with('error', 'Departemen tidak dapat dihapus karena masih memiliki data terkait (kategori, user, atau arsip).');
        }

        // Validasi password konfirmasi
        $request->validate([
            'password' => 'required'
        ]);

        if (!Hash::check($request->password, $authUser->password)) {
            return back()->with('error', 'Konfirmasi gagal. Password salah.');
        }

        $deptName = $department->name;
        $department->delete();

        log_activity($authUser, 'hapus_departemen', "Departemen ({$deptName}) berhasil dihapus.");

        return redirect()->route('admin.departments.index')
                        ->with('success', 'Departemen ' . $deptName . ' berhasil dihapus.');
    }
}