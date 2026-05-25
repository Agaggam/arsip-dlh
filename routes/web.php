<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ArchiveController;

// 1. Halaman Utama
Route::get('/', function () {
    return view('welcome');
});

// 2. Group untuk Super Admin dan Admin Departemen Saja
Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Route Manage departements
    Route::get('/departments', [DepartmentController::class, 'index'])->name('admin.departments.index');
    Route::post('/departments', [DepartmentController::class, 'store'])->name('admin.departments.store');
    Route::patch('/departments/{department}', [DepartmentController::class, 'update'])->name('admin.departments.update');
    Route::delete('/departments/{department}', [DepartmentController::class, 'destroy'])->name('admin.departments.destroy');

    // Route Manage Users
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    // Route untuk simpan perubahan Departemen
    Route::patch('/users/{user}/department', [UserController::class, 'updateDepartment'])->name('users.update_department');
    // Route untuk simpan perubahan Role
    Route::patch('/users/{user}/role', [UserController::class, 'updateRole'])->name('users.update_role');
    // Route untuk Approve/Reject Status
    Route::patch('/users/{user}/status', [UserController::class, 'updateStatus'])->name('users.update_status');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    // Route Manage Categories
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::patch('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    
    // Route Activity Logs
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
    Route::get('/activity-logs/download/{filename}', [ActivityLogController::class, 'downloadExport'])->name('activity-logs.download');

    // Route Manage Archives (CRUD & Soft Delete)
    Route::prefix('archives')->name('admin.archives.')->group(function () {
        Route::get('/trash', [ArchiveController::class, 'trash'])->name('trash'); // Lihat Tong Sampah
        
        Route::get('/', [ArchiveController::class, 'index'])->name('index');
        Route::post('/', [ArchiveController::class, 'store'])->name('store');
        Route::get('/{archive}/download', [ArchiveController::class, 'download'])->name('download'); // Download
        Route::delete('/{archive}', [ArchiveController::class, 'destroy'])->name('destroy'); // Soft Delete (Pindah ke Trash)

        // Fitur Pemulihan (Soft Delete Logic)
        Route::post('/{id}/restore', [ArchiveController::class, 'restore'])->name('restore'); // Pulihkan data
        Route::delete('/{id}/force-delete', [ArchiveController::class, 'forceDelete'])->name('force_delete'); // Hapus Permanen
    });
});

// 3. Group untuk User Biasa (Hanya bisa diakses role 'user')
Route::middleware(['auth', 'role:user', 'status', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Route arsip untuk user biasa
    Route::get('/arsip', [ArchiveController::class, 'userIndex'])->name('arsip.user');
    Route::get('/arsip/{archive}/download', [ArchiveController::class, 'download'])->name('arsip.download');
});

// 4. Group untuk Semua User yang Login (Profil, dll)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
