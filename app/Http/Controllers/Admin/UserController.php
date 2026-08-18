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
    private function SuperAdminAccess(): User
    {
        $user = Auth::user();
        
        if (!$user || !$user->isPureSuperAdmin()) {
            abort(403, 'Akses Ditolak: Hanya super admin asli yang diizinkan.');
        }
        
        return $user;  // ← return user
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
            return $user;  // ← return user
        }

        // Cek apakah dia admin
        if ($user->isAdmin()) {
            // Jika ada parameter department_id, cek apakah admin dari departemen tersebut
            if ($department_id !== null && !$user->isFromDepartment($department_id)) {
                abort(403, 'Akses Ditolak: Anda bukan admin dari departemen ini.');
            }
            return $user;  // ← return user
        }

        // Bukan super admin dan bukan admin
        abort(403, 'Akses Ditolak: Hanya super admin atau admin yang diizinkan.');
    }

    /**
     * Cek apakah user yang sedang login adalah user yang sama dengan target.
     */
    private function isSelf(User $user): bool
    {
        return Auth::id() === $user->id;
    }

    // ============================================================
    // CONTROLLER METHODS
    // ============================================================

    /**
     * Menampilkan daftar user sesuai hak akses (hanya super admin).
     */
    public function index(Request $request)
    {
        $authUser = $this->SuperAdminAccess();
        
        $search = $request->get('search');
        $statusFilter = $request->get('status');
        $roleFilter = $request->get('role_id');
        $verifiedFilter = $request->get('verified');
        $deptFilter = $request->get('department_id');

        $users = User::with(['role', 'department']);

        // Filter departemen (karena ini super admin, bisa lihat semua atau filter tertentu)
        if ($deptFilter) {
            $users->where('department_id', $deptFilter);
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

        // Pencarian teks
        if ($search) {
            $users->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        $users = $users->latest()->paginate(10);

        // Dropdown data
        $roles = Role::all();
        $departments = Department::all();

        // Statistik
        $statQuery = User::query();
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
        $authUser = $this->SuperAdminAccess();  // ← langsung dapat user

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

        log_activity($authUser, 'tambah_user', "Menambahkan user baru: ({$user->name}) Dengan Email: ({$user->email})");

        return back()->with('success', 'User baru berhasil ditambahkan!');
    }

    /**
     * Memperbarui role user (hanya super admin, tidak bisa ubah sendiri).
     */
    public function updateRole(Request $request, User $user)
    {
        $authUser = $this->SuperAdminAccess();  // ← langsung dapat user

        if ($this->isSelf($user)) {
            return back()->with('error', 'Anda tidak dapat mengubah role Anda sendiri.');
        }

        $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);
        
        $user->role_id = $request->role_id;
        $user->save();

        $newRole = $user->role->name;
        log_activity($authUser, 'ubah_user', "Role user ({$user->name}) diubah menjadi ({$newRole})");
        
        return back()->with('success', "Role user {$user->name} berhasil diubah menjadi {$newRole}.");
    }

    /**
     * Memperbarui departemen user (hanya super admin, tidak bisa ubah sendiri).
     */
    public function updateDepartment(Request $request, User $user)
    {
        $authUser = $this->SuperAdminAccess();  // ← langsung dapat user

        if ($this->isSelf($user)) {
            return back()->with('error', 'Anda tidak dapat mengubah departemen Anda sendiri.');
        }

        $request->validate([
            'department_id' => 'required|exists:departments,id',
        ]);
        
        $user->department_id = $request->department_id;
        $user->save();

        $newDept = $user->department->name;
        log_activity($authUser, 'ubah_user', "Departemen user ({$user->name}) diubah menjadi ({$newDept})");
        
        return back()->with('success', "Departemen user {$user->name} berhasil diubah menjadi {$newDept}.");
    }

    /**
     * Memperbarui status user (approved/rejected).
     * Hanya super admin yang diizinkan.
     */
    public function updateStatus(Request $request, User $user)
    {
        $authUser = $this->SuperAdminAccess();

        if ($this->isSelf($user)) {
            return back()->with('error', 'Anda tidak dapat mengubah status Anda sendiri.');
        }

        $request->validate([
            'status' => 'required|in:approved,rejected'
        ]);
        
        $user->status = $request->status;
        $user->save();

        // Kirim notifikasi email ke user
        try {
            $user->notify(new \App\Notifications\AccountStatusChangedNotification($request->status));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Gagal mengirim email notifikasi status akun: " . $e->getMessage());
        }
        
        log_activity($authUser, 'ubah_status_user', "Status user ({$user->name}) diubah menjadi ({$request->status})");
        
        return back()->with('success', "Status akun {$user->name} berhasil diubah menjadi {$request->status}!");
    }

    /**
     * Update data user (nama, email, password) — hanya super admin.
     */
    public function update(Request $request, User $user)
    {
        $authUser = $this->SuperAdminAccess();

        if ($this->isSelf($user)) {
            return back()->with('error', 'Edit profil Anda sendiri melalui halaman Profil.');
        }

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|min:8|confirmed',
        ]);

        $user->name  = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        log_activity($authUser, 'ubah_user', "Data user ({$user->name}) berhasil diperbarui oleh super admin.");

        return back()->with('success', "Data user {$user->name} berhasil diperbarui!");
    }

    /**
     * Menghapus user dari database (dengan konfirmasi password).
     */
    public function destroy(Request $request, User $user)
    {
        $authUser = $this->SuperAdminAccess();

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

        $userName = $user->name;
        $userEmail = $user->email;
        $user->delete();
        
        log_activity($authUser, 'hapus_user', "User ({$userName}) Dengan Email ({$userEmail}) dihapus oleh super admin.");
        return back()->with('success', 'User berhasil dihapus!');
    }
}