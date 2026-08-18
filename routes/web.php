<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\ZipArchiveController;
use App\Http\Controllers\Admin\UniversalTrashController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\DashboardController as UserDashboardController;

// 1. Halaman Utama & Statis
Route::get('/', function () {
    $totalArsip = \App\Models\Archive::count();
    $totalDepartemen = \App\Models\Department::where('name', '!=', 'System')->count();
    $totalKategori = \App\Models\Category::count();
    $totalUser = \App\Models\User::where('status', 'active')->count();
    $totalUsulan = \App\Models\SurveyHarga::count();

    return view('welcome', compact('totalArsip', 'totalDepartemen', 'totalKategori', 'totalUser', 'totalUsulan'));
})->name('home');

Route::get('/about', function () {
    return view('pages.about');
})->name('about');

Route::get('/panduan', function () {
    return view('pages.panduan');
})->name('panduan');

Route::get('/bantuan', function () {
    return view('pages.bantuan');
})->name('bantuan');

Route::get('/kebijakan-privasi', function () {
    return view('pages.kebijakan-privasi');
})->name('kebijakan-privasi');



// 2. Group untuk Super Admin dan Admin Departemen Saja
Route::middleware(['auth', 'status', 'verified'])->prefix('admin')->group(function () {
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
    Route::patch('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::patch('/users/{user}/role', [UserController::class, 'updateRole'])->name('users.update_role');
    // Route untuk Approve/Reject Status
    Route::patch('/users/{user}/status', [UserController::class, 'updateStatus'])->name('users.update_status');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    // Route Manage Categories
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::patch('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::post('/categories/migrate', [CategoryController::class, 'migrateArchives'])->name('categories.migrate');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    
    // Route Activity Logs
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
    Route::get('/activity-logs/download/{filename}', [ActivityLogController::class, 'downloadExport'])->name('activity-logs.download');

    // Route Backup & Restore Database
    Route::get('/backup', [\App\Http\Controllers\Admin\BackupController::class, 'index'])->name('admin.backup.index');
    Route::post('/backup', [\App\Http\Controllers\Admin\BackupController::class, 'create'])->name('admin.backup.create');
    Route::get('/backup/download/{filename}', [\App\Http\Controllers\Admin\BackupController::class, 'download'])->name('admin.backup.download');
    Route::delete('/backup/{filename}', [\App\Http\Controllers\Admin\BackupController::class, 'destroy'])->name('admin.backup.destroy');
    Route::post('/backup/restore', [\App\Http\Controllers\Admin\BackupController::class, 'restore'])->name('admin.backup.restore');

        // Route Manage Archives (CRUD & Soft Delete)
    Route::prefix('archives')->name('admin.archives.')->group(function () {
        
        Route::get('/', [ArchiveController::class, 'index'])->name('index');
        Route::post('/', [ArchiveController::class, 'store'])->name('store');
        Route::post('/store-zip', [ZipArchiveController::class, 'storeZip'])->name('store-zip');
        
        Route::get('/preview/{token}', [ArchiveController::class, 'Preview'])->name('preview');
        Route::put('/{archive}', [ArchiveController::class, 'update'])->name('update'); // Update Data Arsip
        Route::get('/{archive}/download', [ArchiveController::class, 'download'])->name('download'); // Download
        Route::delete('/{archive}', [ArchiveController::class, 'destroy'])->name('destroy'); // Soft Delete (Pindah ke Trash)

    });

    // Universal Trash Bin
    Route::prefix('trash')->name('admin.trash.')->group(function () {
        Route::get('/', [UniversalTrashController::class, 'index'])->name('index');
        Route::post('/{type}/{id}/restore', [UniversalTrashController::class, 'restore'])->name('restore');
        Route::delete('/{type}/{id}/force-delete', [UniversalTrashController::class, 'forceDelete'])->name('force-delete');
    });

    // Route Modul Kepegawaian
    Route::prefix('kepegawaian')->name('kepegawaian.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\PegawaiController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\Admin\PegawaiController::class, 'store'])->name('store');
        Route::put('/{pegawai}', [\App\Http\Controllers\Admin\PegawaiController::class, 'update'])->name('update');
        Route::delete('/{pegawai}', [\App\Http\Controllers\Admin\PegawaiController::class, 'destroy'])->name('destroy');
        // Export dengan filter aktif
        Route::get('/export/excel', [\App\Http\Controllers\Admin\PegawaiController::class, 'exportExcel'])->name('export.excel');
        Route::get('/export/pdf', [\App\Http\Controllers\Admin\PegawaiController::class, 'exportPdf'])->name('export.pdf');
        // Quick Export 1-click per kategori/status
        Route::get('/quick-export/{type}/{format}', [\App\Http\Controllers\Admin\PegawaiController::class, 'quickExport'])->name('quick.export');
    });

    // Route Modul Usulan SSH/SBU
    Route::prefix('survey-harga')->name('survey-harga.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\SurveyHargaController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\SurveyHargaController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\SurveyHargaController::class, 'store'])->name('store');
        Route::get('/{surveyHarga}/edit', [\App\Http\Controllers\Admin\SurveyHargaController::class, 'edit'])->name('edit');
        Route::put('/{surveyHarga}', [\App\Http\Controllers\Admin\SurveyHargaController::class, 'update'])->name('update');
        Route::delete('/{surveyHarga}', [\App\Http\Controllers\Admin\SurveyHargaController::class, 'destroy'])->name('destroy');
        Route::patch('/{surveyHarga}/status', [\App\Http\Controllers\Admin\SurveyHargaController::class, 'updateStatus'])->name('update-status');
        Route::get('/{surveyHarga}/pdf', [\App\Http\Controllers\Admin\SurveyHargaController::class, 'exportPdf'])->name('export-pdf');
    });

}); // End admin middleware group


// 3. Group untuk User Biasa (Hanya bisa diakses role 'user')
Route::middleware(['auth', 'role:user', 'status', 'verified'])->group(function () {

    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');

    // Route arsip untuk user biasa
    Route::get('/arsip', [ArchiveController::class, 'userIndex'])->name('arsip.user');
    Route::get('/arsip/preview/{token}', [ArchiveController::class, 'Preview'])->name('arsip.preview');
    Route::get('/arsip/{archive}/download', [ArchiveController::class, 'download'])->name('arsip.download');
});

// 4. Group untuk Semua User yang Login (Profil, dll)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';