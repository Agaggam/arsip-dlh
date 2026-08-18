<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Archive;
use App\Models\ActivityLog;
use App\Models\Pegawai;
use App\Models\Department;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isUser()) {
            return redirect()->route('dashboard')->with('info', 'Anda tidak memiliki akses ke halaman admin.');
        }

        $isPureSuperAdmin = $user->isPureSuperAdmin();

        // Base query untuk arsip (aktif)
        $archivesQuery = Archive::query();
        // Base query untuk user
        $usersQuery = User::query();

        // Filter untuk arsip dan user berdasarkan department (jika bukan super admin)
        if (!$isPureSuperAdmin && $user->isAdmin()) {
            $userDeptId = $user->department_id;

            $archivesQuery->whereHas('category', function ($q) use ($userDeptId) {
                $q->where('department_id', $userDeptId);
            });

            $usersQuery->where('department_id', $userDeptId);
        }

        // Data statistik
        $totalUsers = $usersQuery->count();
        $totalArchives = $archivesQuery->count();
        $totalDownloads = $archivesQuery->sum('download_count');

        // Trash
        $trashedQuery = Archive::onlyTrashed();
        if (!$isPureSuperAdmin && $user->isAdmin()) {
            $trashedQuery->whereHas('category', function ($q) use ($userDeptId) {
                $q->where('department_id', $userDeptId);
            });
        }
        $totalTrashed = $trashedQuery->count();

        // Master Data Stats
        $totalPegawai = Pegawai::count();
        $totalDepartments = Department::count();
        $totalCategories = Category::count();

        // Data bulan ini
        $archivesThisMonth = (clone $archivesQuery)->whereMonth('created_at', now()->month)->count();
        $usersThisMonth = (clone $usersQuery)->whereMonth('created_at', now()->month)->count();

        // Grafik tren arsip (12 bulan)
        $months = [];
        $archiveTrend = [];
        for ($i = 11; $i >= 0; $i--) {
            $bulan = now()->subMonths($i);
            $months[] = $bulan->format('M Y');
            $count = (clone $archivesQuery)
                ->whereMonth('created_at', $bulan->month)
                ->whereYear('created_at', $bulan->year)
                ->count();
            $archiveTrend[] = $count;
        }

        // Arsip terbaru (5 data)
        $recentArchives = (clone $archivesQuery)
            ->with('category')
            ->latest()
            ->take(5)
            ->get();

        // Pengguna terbaru (5 data)
        $recentUsers = (clone $usersQuery)
            ->with('role')
            ->latest()
            ->take(5)
            ->get();

        // ========== AKTIVITAS TERKINI ==========
        $activityQuery = ActivityLog::with('user')
            ->latest()
            ->take(5); // ambil 10 aktivitas terbaru

        // Filter departemen untuk admin biasa
        if (!$isPureSuperAdmin && $user->isAdmin()) {
            $activityQuery->whereHas('user', function ($q) use ($user) {
                $q->where('department_id', $user->department_id);
            });
        }

        $recentActivities = $activityQuery->get();

        // Ambil data Usulan Survey Harga
        $surveysQuery = \App\Models\SurveyHarga::query();
        if (!$isPureSuperAdmin && $user->isAdmin()) {
            $surveysQuery->where('department_id', $user->department_id);
        }
        $totalUsulan = $surveysQuery->count();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalArchives',
            'totalDownloads',
            'totalTrashed',
            'totalPegawai',
            'totalDepartments',
            'totalCategories',
            'totalUsulan',
            'archivesThisMonth',
            'usersThisMonth',
            'months',
            'archiveTrend',
            'recentArchives',
            'recentUsers',
            'recentActivities'
        ));
    }
}