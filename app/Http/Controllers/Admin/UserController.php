<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Menampilkan daftar user dan role.
     */
    public function index()
    {
        // Ambil semua user beserta relasi rolenya
        $users = User::with('role')->get();
        $roles = Role::all();
        
        return view('admin.users.index', compact('users', 'roles'));
    }

    /**
     * Menyimpan user baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|min:8',
            'role_id' => 'required|exists:roles,id',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
        ]);

        return back()->with('success', 'User baru berhasil ditambahkan!');
    }

    /**
     * Memperbarui role user.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);

        // Cara ini lebih 'keras' dan pasti tersimpan
        $user->role_id = $request->role_id;
        $user->save();

        // Ambil nama role baru untuk pesan sukses yang lebih jelas
        $newRole = Role::find($request->role_id)->name;

        return back()->with('success', "Role {$user->name} berhasil diubah menjadi {$newRole}!");
    }

    /**
     * Memperbarui status user.
     */
    public function updateStatus(Request $request, User $user)
    {
        $request->validate(['status' => 'required|in:approved,rejected']);
        $user->update(['status' => $request->status]);
        return back()->with('success', "Status akun {$user->name} berhasil diubah!");
    }

    /**
     * Menghapus user dari database.
     */
    public function destroy(User $user)
    {
        // Menggunakan Auth::id() untuk mengambil ID user yang sedang login
        // Ini cara paling aman agar VS Code tidak error 'Undefined method'
        if (Auth::id() == $user->id) {
            return back()->with('error', 'Anda tidak bisa menghapus akun sendiri!');
        }

        $user->delete();
        
        return back()->with('success', 'User berhasil dihapus!');
    }
}