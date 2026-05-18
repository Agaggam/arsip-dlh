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
    private function hasManageAccess(int $target_department_id): bool
    {
        $user = Auth::user();

        if ($user->isPureSuperAdmin()) {
            return true;
        }
        if ($user->isAdmin() && $user->isFromDepartment($target_department_id)) {
            return true;
        }
        return false;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->get('search');

        $query = Category::with('department')->withCount('archives');

        if (!$user->isPureSuperAdmin()) {
            $query->where('department_id', $user->department_id);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhereHas('department', function ($dept) use ($search) {
                      $dept->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        $categories = $query->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        // Pastikan user punya akses ke departemennya sendiri (untuk admin) atau super admin otomatis lolos
        if (!$this->hasManageAccess($user->department_id)) {
            return back()->with('error', 'Akses Ditolak: Anda tidak memiliki izin untuk menambah kategori.');
        }

        $dept_id = $user->department_id;

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

        log_activity(Auth::user(), 'tambah_kategori', "Kategori '{$category->name}' berhasil ditambahkan di departemen '{$category->department->name}'.");

        return back()->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function update(Request $request, Category $category)
    {
        $user = Auth::user();

        if (!$this->hasManageAccess($category->department_id)) {
            return back()->with('error', 'Izin Ditolak: Anda tidak memiliki otoritas untuk mengubah data ini.');
        }

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

        log_activity(Auth::user(), 'edit_kategori', "Kategori '{$oldName}' diubah menjadi '{$category->name}'.");

        return back()->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Request $request, Category $category)
    {
        $user = Auth::user();

        $request->validate(['password' => 'required']);

        if (!Hash::check($request->password, $user->password)) {
            return back()->with('error', 'Konfirmasi gagal. Password yang Anda masukkan salah!');
        }

        if (!$this->hasManageAccess($category->department_id)) {
            return back()->with('error', 'Izin Ditolak: Anda tidak memiliki otoritas untuk menghapus data ini.');
        }

        $categoryName = $category->name;
        $category->delete();

        log_activity(Auth::user(), 'hapus_kategori', "Kategori '{$categoryName}' berhasil dihapus.");

        return back()->with('success', 'Kategori berhasil dihapus.');
    }
}