<?php

namespace App\Http\Controllers;

use App\Models\Archive;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Ambil input keyword pencarian token
        $search = $request->input('search');

        // Base query kosong (tidak menampilkan apa-apa di awal)
        $query = Archive::query()->with(['category', 'user']);

        if (!empty($search)) {
            // Hanya cari yang hash_token-nya benar-benar sama persis (Exact Match)
            $query->where('hash_token', $search);
        } else {
            // Jika belum mencari / input kosong, paksa hasil agar kosong
            $query->whereRaw('1 = 0');
        }

        $archives = $query->get();

        return view('dashboard', compact('archives'));
    }
}