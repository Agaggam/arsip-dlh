<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Archive;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Mengambil data asli dari database
        $totalUsers = User::count();
        $totalArchives = Archive::count();
        
        // Kita hitung total download dari semua arsip sebagai bonus info
        $totalDownloads = Archive::sum('download_count');

        return view('admin.dashboard', [
            'totalUsers' => $totalUsers,
            'totalArchives' => $totalArchives,
            'pendingArchives' => $totalDownloads, // Sementara kita pakai untuk total download ya
        ]);
    }
}
