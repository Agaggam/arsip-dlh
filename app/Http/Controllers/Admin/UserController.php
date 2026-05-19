<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Private helper: Pastikan hanya super admin asli yang bisa mengakses.
     */
    private function secureAccess()
    {
        if (!Auth::user()->isPureSuperAdmin()) {
            abort(403, 'Akses Ditolak: Hanya super admin asli yang diizinkan.');
        }
    }

    /**
     * Cek apakah user yang sedang login adalah user yang sama dengan target.
     */
    private function isSelf(User $user): bool
    {
        return Auth::id() === $user->id;
    }

    /**
     * Menampilkan daftar user sesuai hak akses.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->get('search');
        $statusFilter = $request->get('status');
        $roleFilter = $request->get('role_id');
        $verifiedFilter = $request->get('verified');
        $deptFilter = $request->get('department_id'); // filter departemen untuk super admin

        $users = User::with(['role', 'department']);

        // Filter dasar berdasarkan role user yang login
        if (!$user->isPureSuperAdmin()) {
            // Admin: hanya melihat user di departemennya sendiri
            $users->where('department_id', $user->department_id);
        } else {
            // Super admin: jika ada filter departemen, terapkan
            if ($deptFilter) {
                $users->where('department_id', $deptFilter);
            }
        }

        // Filter status
        if ($statusFilter && in_array($statusFilter, ['pending', 'approved', 'rejected'])) {
            $users->where('status', $statusFilter);
        }

        // Filter role
        if ($roleFilter) {
            $users->where('role_id', $roleFilter);
        }

        // Filter verifikasi email
        if ($verifiedFilter === 'verified') {
            $users->whereNotNull('email_verified_at');
        } elseif ($verifiedFilter === 'unverified') {
            $users->whereNull('email_verified_at');
        }

        // Pencarian teks (hanya name & email)
        if ($search) {
            $users->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        $users = $users->latest()->paginate(10);

        // Role dropdown: super admin bisa lihat semua, admin hanya lihat selain super_admin
        if ($user->isPureSuperAdmin()) {
            $roles = Role::all();
        } else {
            $roles = Role::where('name', '!=', 'super_admin')->get();
        }

        // Departemen untuk dropdown filter (super admin bisa memilih semua, admin hanya melihat departemen sendiri)
        if ($user->isPureSuperAdmin()) {
            $departments = Department::all();
        } else {
            $departments = Department::where('id', $user->department_id)->get();
        }

        // Statistik (difilter sesuai hak akses)
        $statQuery = User::query();
        if (!$user->isPureSuperAdmin()) {
            $statQuery->where('department_id', $user->department_id);
        }
        $pendingCount   = (clone $statQuery)->where('status', 'pending')->count();
        $approvedCount  = (clone $statQuery)->where('status', 'approved')->count();
        $rejectedCount  = (clone $statQuery)->where('status', 'rejected')->count();
        $verifiedCount  = (clone $statQuery)->where('status', 'approved')->whereNotNull('email_verified_at')->count();

        return view('admin.users.index', compact('users', 'roles', 'departments', 'pendingCount', 'approvedCount', 'rejectedCount', 'verifiedCount'));
    }

    /**
     * Menyimpan user baru ke database (hanya super admin).
     */
    public function store(Request $request)
    {
        $this->secureAccess();

        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|string|email|max:255|unique:users',
            'password'      => 'required|min:8',
            'role_id'       => 'required|exists:roles,id',
            'department_id' => 'nullable|exists:departments,id',
        ]);

        $user = User::create([
            'name'          => $request->name,
            'email'         => $request->email,
            'password'      => Hash::make($request->password),
            'role_id'       => $request->role_id,
            'department_id' => $request->department_id,
            'status'        => 'pending',
        ]);

        log_activity(Auth::user(), 'tambah_user', "Menambahkan user baru: {$user->name} ({$user->email})");

        return back()->with('success', 'User baru berhasil ditambahkan!');
    }

    /**
     * Memperbarui role user (hanya super admin, tidak bisa ubah sendiri).
     */
    public function updateRole(Request $request, User $user)
    {
        $this->secureAccess();

        if ($this->isSelf($user)) {
            return back()->with('error', 'Anda tidak dapat mengubah role Anda sendiri.');
        }

        $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);
        
        $user->role_id = $request->role_id;
        $user->save();

        $newRole = $user->role->name;
        log_activity(Auth::user(), 'edit_user', "Role user {$user->name} diubah menjadi {$newRole}");
        
        return back()->with('success', "Role user {$user->name} berhasil diubah menjadi {$newRole}.");
    }

    /**
     * Memperbarui departemen user (hanya super admin, tidak bisa ubah sendiri).
     */
    public function updateDepartment(Request $request, User $user)
    {
        $this->secureAccess();

        if ($this->isSelf($user)) {
            return back()->with('error', 'Anda tidak dapat mengubah departemen Anda sendiri.');
        }

        $request->validate([
            'department_id' => 'required|exists:departments,id',
        ]);
        
        $user->department_id = $request->department_id;
        $user->save();

        $newDept = $user->department->name;
        log_activity(Auth::user(), 'edit_user', "Departemen user {$user->name} diubah menjadi {$newDept}");
        
        return back()->with('success', "Departemen user {$user->name} berhasil diubah menjadi {$newDept}.");
    }

    /**
     * Memperbarui status user (approved/rejected).
     * Super admin bisa semua, admin hanya untuk user di departemennya sendiri.
     */
    public function updateStatus(Request $request, User $user)
    {
        $authUser = Auth::user();

        if (!$authUser->isPureSuperAdmin()) {
            if ($user->department_id !== $authUser->department_id) {
                return back()->with('error', 'Anda tidak memiliki izin untuk mengubah status user dari departemen lain.');
            }
        }

        if ($this->isSelf($user)) {
            return back()->with('error', 'Anda tidak dapat mengubah status Anda sendiri.');
        }
        
        $request->validate([
            'status' => 'required|in:approved,rejected'
        ]);
        
        $user->status = $request->status;
        $user->save();
        
        log_activity($authUser, 'update_status', "Status user {$user->name} diubah menjadi {$request->status}");
        
        return back()->with('success', "Status akun {$user->name} berhasil diubah menjadi {$request->status}!");
    }

    /**
     * Menghapus user dari database (dengan konfirmasi password).
     */
    public function destroy(Request $request, User $user)
    {
        $authUser = Auth::user();

        if ($this->isSelf($user)) {
            return back()->with('error', 'Anda tidak bisa menghapus akun sendiri!');
        }

        // Validasi password konfirmasi
        $request->validate([
            'password' => 'required'
        ]);

        if (!Hash::check($request->password, $authUser->password)) {
            return back()->with('error', 'Konfirmasi gagal. Password salah!');
        }

        if ($authUser->isPureSuperAdmin()) {
            $userName = $user->name;
            $userEmail = $user->email;
            $user->delete();
            log_activity($authUser, 'hapus_user', "User {$userName} ({$userEmail}) dihapus oleh super admin.");
            return back()->with('success', 'User berhasil dihapus!');
        }
        
        if ($authUser->isAdmin()) {
            if ($user->role->name !== 'user') {
                return back()->with('error', 'Anda hanya dapat menghapus user dengan role "user".');
            }
            if ($user->department_id !== $authUser->department_id) {
                return back()->with('error', 'Anda hanya dapat menghapus user dari departemen Anda sendiri.');
            }
            $userName = $user->name;
            $userEmail = $user->email;
            $user->delete();
            log_activity($authUser, 'hapus_user', "User {$userName} ({$userEmail}) dihapus oleh admin departemen.");
            return back()->with('success', 'User berhasil dihapus!');
        }
        
        return back()->with('error', 'Anda tidak memiliki izin untuk menghapus user.');
    }
}