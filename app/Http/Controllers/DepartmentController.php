<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class DepartmentController extends Controller
{
    /**
     * PRIVATE HELPER: Proteksi Puncak
     * Memastikan User adalah super_admin DAN berasal dari departemen System.
     */
    private function secureAccess()
    {
        // Menggunakan method baru di Model User
        if (!Auth::user()->isPureSuperAdmin()) {
            abort(403, 'Akses Ditolak: Hanya super admin yang asli yang diizinkan.');
        }
    }

    /**
     * TAMPILKAN DAFTAR DEPARTEMEN
     */
    public function index()
    {
        $this->secureAccess();

        $departments = Department::withCount(['users', 'archives'])->get();
        return view('admin.departments.index', compact('departments'));
    }

    /**
     * SIMPAN DEPARTEMEN BARU
     */
    public function store(Request $request)
    {
        $this->secureAccess();

        $request->validate([
            'name' => 'required|string|max:255|unique:departments,name',
            'description' => 'nullable|string'
        ]);

        Department::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
        ]);

        return redirect()->route('admin.departments.index')->with('success', 'Departemen baru berhasil ditambahkan.');
    }

    /**
     * FORM EDIT DEPARTEMEN
     */
    public function edit(Department $department)
    {
        $this->secureAccess();

        // Proteksi: Departemen System tidak boleh diedit namanya
        if ($department->name === 'System') {
            return redirect()->route('admin.departments.index')->with('error', 'Departemen System adalah proteksi inti dan tidak boleh diubah!');
        }

        return view('admin.departments.edit', compact('department'));
    }

    /**
     * UPDATE DATA DEPARTEMEN
     */
    public function update(Request $request, Department $department)
    {
        $this->secureAccess();

        if ($department->name === 'System') {
            return back()->with('error', 'Departemen System tidak boleh diubah!');
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:departments,name,' . $department->id,
            'description' => 'nullable|string'
        ]);

        $department->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
        ]);

        return redirect()->route('admin.departments.index')->with('success', 'Data departemen diperbarui.');
    }

    /**
     * HAPUS DEPARTEMEN
     */
    public function destroy(Request $request, Department $department)
    {
        $this->secureAccess();

        // 1. Larangan mutlak menghapus departemen System
        if ($department->name === 'System') {
            return back()->with('error', 'Departemen utama sistem tidak bisa dihapus!');
        }

        // 2. Validasi Password Konfirmasi
        $request->validate([
            'password' => 'required',
        ]);

        // Verifikasi password user login
        if (!Hash::check($request->password, Auth::user()->password)) {
            return back()->with('error', 'Konfirmasi gagal. Password salah!');
        }

        $department->delete();

        return redirect()->route('admin.departments.index')->with('success', 'Departemen ' . $department->name . ' berhasil dihapus.');
    }
}