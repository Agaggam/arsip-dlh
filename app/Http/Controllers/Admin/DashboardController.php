<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Archive;
use App\Models\ActivityLog;
use App\Models\Pegawai;
use App\Models\Pengawasan;
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

        // Ambil data Pengawasan
        $pengawasanQuery = Pengawasan::query();
        $totalPengawasan = $pengawasanQuery->count();
        $pengawasanDisetujui = Pengawasan::where('status', 'disetujui')->count();
        $pengawasanDiajukan = Pengawasan::where('status', 'diajukan')->count();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalArchives',
            'totalDownloads',
            'totalTrashed',
            'totalPegawai',
            'totalDepartments',
            'totalCategories',
            'totalUsulan',
            'totalPengawasan',
            'pengawasanDisetujui',
            'pengawasanDiajukan',
            'archivesThisMonth',
            'usersThisMonth',
            'months',
            'archiveTrend',
            'recentArchives',
            'recentUsers',
            'recentActivities'
        ));
    }

    // ============================================================
    // STATS — Real-time JSON endpoint untuk AJAX polling dashboard
    // ============================================================
    public function stats()
    {
        $user             = Auth::user();
        $isPureSuperAdmin = $user->isPureSuperAdmin();

        $archivesQuery = Archive::query();
        $usersQuery    = User::query();

        if (!$isPureSuperAdmin && $user->isAdmin() && $user->department_id) {
            $deptId = $user->department_id;
            $archivesQuery->whereHas('category', fn($q) => $q->where('department_id', $deptId));
            $usersQuery->where('department_id', $deptId);
        }

        // KPI numbers
        $totalArchives    = $archivesQuery->count();
        $totalDownloads   = $archivesQuery->sum('download_count');
        $totalUsers       = $usersQuery->count();
        $usersThisMonth   = (clone $usersQuery)->whereMonth('created_at', now()->month)->count();
        $archivesThisMonth = (clone $archivesQuery)->whereMonth('created_at', now()->month)->count();

        $trashedQuery = Archive::onlyTrashed();
        if (!$isPureSuperAdmin && $user->isAdmin() && $user->department_id) {
            $trashedQuery->whereHas('category', fn($q) => $q->where('department_id', $user->department_id));
        }
        $totalTrashed = $trashedQuery->count();

        $surveysQuery = \App\Models\SurveyHarga::query();
        if (!$isPureSuperAdmin && $user->isAdmin() && $user->department_id) {
            $surveysQuery->where('department_id', $user->department_id);
        }

        $totalPegawai     = Pegawai::count();
        $totalUsulan      = $surveysQuery->count();
        $totalPengawasan  = Pengawasan::count();
        $totalDepartments = Department::count();
        $totalCategories  = Category::count();

        // Tren 12 bulan
        $months      = [];
        $archiveTrend = [];
        for ($i = 11; $i >= 0; $i--) {
            $bulan        = now()->subMonths($i);
            $months[]     = $bulan->format('M Y');
            $archiveTrend[] = (clone $archivesQuery)
                ->whereMonth('created_at', $bulan->month)
                ->whereYear('created_at', $bulan->year)
                ->count();
        }

        // Aktivitas terkini (rendered as HTML snippet)
        $activityQuery = ActivityLog::with('user')->latest()->take(5);
        if (!$isPureSuperAdmin && $user->isAdmin() && $user->department_id) {
            $activityQuery->whereHas('user', fn($q) => $q->where('department_id', $user->department_id));
        }
        $recentActivities = $activityQuery->get()->map(function ($a) {
            $type    = $a->activity ?? '';
            $name    = $a->causer_name ?? ($a->user->name ?? 'Sistem');
            $initial = strtoupper(substr($name, 0, 1));
            $time    = $a->created_at ? $a->created_at->diffForHumans() : 'Baru saja';
            $desc    = $a->description ?? $a->activity ?? 'Aktivitas';

            $colorMap = [
                'tambah'   => ['avatar' => 'bg-emerald-100 text-emerald-600', 'badge' => 'bg-emerald-50 text-emerald-600'],
                'edit'     => ['avatar' => 'bg-amber-100 text-amber-600',   'badge' => 'bg-amber-50 text-amber-600'],
                'hapus'    => ['avatar' => 'bg-rose-100 text-rose-600',     'badge' => 'bg-rose-50 text-rose-600'],
                'login'    => ['avatar' => 'bg-blue-100 text-blue-600',     'badge' => 'bg-blue-50 text-blue-600'],
                'download' => ['avatar' => 'bg-purple-100 text-purple-600', 'badge' => 'bg-purple-50 text-purple-600'],
                'status'   => ['avatar' => 'bg-indigo-100 text-indigo-600', 'badge' => 'bg-indigo-50 text-indigo-600'],
                'import'   => ['avatar' => 'bg-teal-100 text-teal-600',     'badge' => 'bg-teal-50 text-teal-600'],
            ];
            $colors = ['avatar' => 'bg-gray-100 text-gray-600', 'badge' => 'bg-gray-100 text-gray-600'];
            foreach ($colorMap as $keyword => $c) {
                if (str_contains($type, $keyword)) { $colors = $c; break; }
            }

            return [
                'initial'  => $initial,
                'name'     => $name,
                'type'     => str_replace('_', ' ', ucfirst($type)),
                'desc'     => $desc,
                'time'     => $time,
                'colors'   => $colors,
            ];
        });

        // Arsip terbaru
        $recentArchives = (clone $archivesQuery)->with('category')->latest()->take(5)->get()->map(fn($a) => [
            'title'    => $a->title,
            'category' => $a->category->name ?? '-',
            'date'     => $a->created_at->format('d M Y'),
        ]);

        return response()->json([
            'stats' => [
                'totalArchives'     => $totalArchives,
                'totalDownloads'    => $totalDownloads,
                'totalUsers'        => $totalUsers,
                'totalTrashed'      => $totalTrashed,
                'totalPegawai'      => $totalPegawai,
                'totalUsulan'       => $totalUsulan,
                'totalPengawasan'   => $totalPengawasan,
                'totalDepartments'  => $totalDepartments,
                'totalCategories'   => $totalCategories,
                'archivesThisMonth' => $archivesThisMonth,
                'usersThisMonth'    => $usersThisMonth,
            ],
            'chart' => [
                'months'      => $months,
                'archiveTrend' => $archiveTrend,
            ],
            'recentActivities' => $recentActivities,
            'recentArchives'   => $recentArchives,
            'updatedAt'        => now()->format('H:i:s'),
        ]);
    }
}