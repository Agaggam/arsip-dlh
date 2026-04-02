<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Admin\UserController;
use App\Models\User;

// 1. Halaman Utama
Route::get('/', function () {
    return view('welcome');
});

// 2. Group untuk Super Admin (Hanya bisa diakses role 'super_admin')
Route::middleware(['auth', 'role:super_admin'])->group(function () {
    Route::get('/admin/dashboard', function () {
        $totalUsers = User::count(); // Ambil data real jumlah user
        return view('admin.dashboard', compact('totalUsers')); 
    })->name('admin.dashboard');

    // Route Manage Users
    Route::get('/admin/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/admin/users', [UserController::class, 'store'])->name('users.store');
    // BARU DI SINI: Route untuk simpan perubahan Role
    Route::patch('/admin/users/{user}', [UserController::class, 'update'])->name('users.update');
    // Route untuk Approve/Reject Status
    Route::patch('/admin/users/{user}/status', [UserController::class, 'updateStatus'])->name('users.update_status');
    Route::delete('/admin/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    // Route Kategori
    Route::get('/admin/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/admin/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::delete('/admin/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
});

// 3. Group untuk User Biasa (Hanya bisa diakses role 'user')
Route::middleware(['auth', 'role:user', 'status'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

// 4. Group untuk Semua User yang Login (Profil, dll)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
