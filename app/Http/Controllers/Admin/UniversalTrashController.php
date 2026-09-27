<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Archive;
use App\Models\Category;
use App\Models\Department;
use App\Models\User;
use App\Models\SurveyHarga;
use App\Models\Pengawasan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UniversalTrashController extends Controller
{
    private function hasArchiveAccess(Archive $archive): bool
    {
        $user = Auth::user();
        if ($user->isPureSuperAdmin()) return true;
        return $archive->category && $archive->category->department_id === $user->department_id;
    }

    public function index(Request $request)
    {
        $type = $request->get('type', 'archive');
        $user = Auth::user();

        // Variabel default yang akan di-compact
        $archives = null;
        $surveys = null;
        $pengawasans = null;
        $users = null;

        if ($type === 'archive') {
            $search = $request->get('search');
            $categoryId = $request->get('category_id');
            $fileType = $request->get('file_type');
            
            $query = Archive::onlyTrashed()->with(['category.department', 'user']);

            if (!$user->isPureSuperAdmin()) {
                $query->whereHas('category', function($q) use ($user) {
                    $q->where('department_id', $user->department_id);
                });
            }

            if ($search) $query->where('title', 'LIKE', "%{$search}%");
            if ($categoryId) $query->where('category_id', $categoryId);
            if ($fileType) $query->where('file_type', strtoupper($fileType));

            $rawPagination = $query->latest()->paginate(10)->appends($request->all());
            
            $archives = $rawPagination->setCollection(
                $rawPagination->getCollection()->filter(function($archive) {
                    return $this->hasArchiveAccess($archive);
                })
            );

        } elseif ($type === 'survey') {
            $query = SurveyHarga::onlyTrashed()->with(['user', 'department']);
            if (!$user->isPureSuperAdmin()) {
                $query->where('department_id', $user->department_id);
            }
            if ($request->search) {
                $query->where('judul', 'LIKE', "%{$request->search}%");
            }
            $surveys = $query->latest()->paginate(10)->appends($request->all());

        } elseif ($type === 'pengawasan') {
            $query = Pengawasan::onlyTrashed()->with('user');
            if (!$user->isPureSuperAdmin() && $user->isAdmin() && $user->department_id) {
                $query->where('department_id', $user->department_id);
            }
            if ($request->search) {
                $query->where('nama_usaha', 'LIKE', "%{$request->search}%");
            }
            $pengawasans = $query->latest()->paginate(10)->appends($request->all());


        } elseif ($type === 'user') {
            if (!$user->isPureSuperAdmin()) abort(403);
            $query = User::onlyTrashed()->with(['department', 'role']);
            if ($request->search) {
                $query->where('name', 'LIKE', "%{$request->search}%");
            }
            $users = $query->latest()->paginate(10)->appends($request->all());
        }

        $categories = Category::with('department')->when(!$user->isPureSuperAdmin(), function($q) use ($user) {
            return $q->where('department_id', $user->department_id);
        })->get();
        $departments = Department::where('name', '!=', 'System')->get();
        $fileTypes = Archive::onlyTrashed()->distinct()->orderBy('file_type')->pluck('file_type');

        return view('admin.trash.index', compact(
            'type', 'archives', 'surveys', 'pengawasans', 'users', 'categories', 'departments', 'fileTypes'
        ));
    }

    public function restore($type, $id)
    {
        if ($type === 'archive') {
            $model = Archive::withTrashed()->findOrFail($id);
            if (!$this->hasArchiveAccess($model)) return back()->with('error', 'Akses ditolak.');
            $model->restore();
            log_activity(Auth::user(), 'pulihkan_arsip', "Arsip ({$model->title}) berhasil dipulihkan.");
        } elseif ($type === 'survey') {
            $model = SurveyHarga::withTrashed()->findOrFail($id);
            if (!Auth::user()->isPureSuperAdmin() && $model->department_id !== Auth::user()->department_id) return back()->with('error', 'Akses ditolak.');
            $model->restore();
            log_activity(Auth::user(), 'pulihkan_usulan', "Usulan Harga ({$model->judul}) berhasil dipulihkan.");
        } elseif ($type === 'pengawasan') {
            $model = Pengawasan::withTrashed()->findOrFail($id);
            $user  = Auth::user();
            if (!$user->isPureSuperAdmin() && $user->isAdmin() && isset($model->department_id) && $model->department_id !== $user->department_id) {
                return back()->with('error', 'Akses ditolak.');
            }
            $model->restore();
            log_activity(Auth::user(), 'pulihkan_pengawasan', "Data Pengawasan ({$model->nama_usaha}) berhasil dipulihkan.");
        } elseif ($type === 'user') {
            if (!Auth::user()->isPureSuperAdmin()) return back()->with('error', 'Akses ditolak.');
            $model = User::withTrashed()->findOrFail($id);
            $model->restore();
            log_activity(Auth::user(), 'pulihkan_user', "Pengguna ({$model->name}) berhasil dipulihkan.");
        }
        
        return back()->with('success', 'Data berhasil dipulihkan.');
    }

    public function forceDelete(Request $request, $type, $id)
    {
        $request->validate(['password' => 'required']);

        if (!Hash::check($request->password, Auth::user()->password)) {
            return back()->with('error', 'Password salah!');
        }

        if ($type === 'archive') {
            $model = Archive::withTrashed()->findOrFail($id);
            if (!$this->hasArchiveAccess($model)) return back()->with('error', 'Akses ditolak.');
            // [CRITICAL-01] Hapus file fisik dari private storage
            if ($model->file_path && Storage::disk('local')->exists($model->file_path)) {
                Storage::disk('local')->delete($model->file_path);
            }
            $title = $model->title;
            $model->forceDelete();
            log_activity(Auth::user(), 'hapus_permanen_arsip', "Arsip ({$title}) dihapus secara permanen.");
        } elseif ($type === 'survey') {
            $model = SurveyHarga::withTrashed()->findOrFail($id);
            if (!Auth::user()->isPureSuperAdmin() && $model->department_id !== Auth::user()->department_id) return back()->with('error', 'Akses ditolak.');
            $title = $model->judul;
            $model->forceDelete();
            log_activity(Auth::user(), 'hapus_permanen_usulan', "Usulan Harga ({$title}) dihapus permanen.");
        } elseif ($type === 'pengawasan') {
            $model = Pengawasan::withTrashed()->findOrFail($id);
            $authUser = Auth::user();
            if (!$authUser->isPureSuperAdmin() && $authUser->isAdmin() && isset($model->department_id) && $model->department_id !== $authUser->department_id) {
                return back()->with('error', 'Akses ditolak.');
            }
            $title = $model->nama_usaha;
            $model->forceDelete();
            log_activity(Auth::user(), 'hapus_permanen_pengawasan', "Pengawasan ({$title}) dihapus permanen.");
        } elseif ($type === 'user') {
            if (!Auth::user()->isPureSuperAdmin()) return back()->with('error', 'Akses ditolak.');
            $model = User::withTrashed()->findOrFail($id);
            $title = $model->name;
            $model->forceDelete();
            log_activity(Auth::user(), 'hapus_permanen_user', "Pengguna ({$title}) dihapus permanen.");
        }

        return back()->with('success', 'Data telah dihapus permanen.');
    }
}
