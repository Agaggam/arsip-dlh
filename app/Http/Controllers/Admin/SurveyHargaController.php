<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SurveyHarga;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class SurveyHargaController extends Controller
{
    private function requireAdminOrSuperAdmin(): void
    {
        $user = Auth::user();
        if (!$user || (!$user->isAdmin() && !$user->isPureSuperAdmin())) {
            abort(403, 'Akses ditolak.');
        }
    }

    // ===========================
    // INDEX
    // ===========================

    public function index(Request $request)
    {
        $this->requireAdminOrSuperAdmin();

        $user    = Auth::user();
        $filters = $request->only(['search', 'kelompok', 'status', 'department_id']);

        $query = SurveyHarga::with(['user', 'department'])->filter($filters);

        // Admin biasa hanya bisa lihat departemen sendiri
        if (!$user->isPureSuperAdmin()) {
            $query->where('department_id', $user->department_id);
        }

        $surveys = $query->latest()->paginate(15)->withQueryString();

        // KPI Summary
        $baseQuery = SurveyHarga::query();
        if (!$user->isPureSuperAdmin()) {
            $baseQuery->where('department_id', $user->department_id);
        }

        $summary = [
            'total'      => (clone $baseQuery)->count(),
            'diajukan'   => (clone $baseQuery)->where('status', 'diajukan')->count(),
            'disetujui'  => (clone $baseQuery)->where('status', 'disetujui')->count(),
            'ditolak'    => (clone $baseQuery)->where('status', 'ditolak')->count(),
        ];

        $departments = $user->isPureSuperAdmin()
            ? Department::where('name', '!=', 'System')->orderBy('name')->get()
            : collect();

        return view('admin.survey-harga.index', compact('surveys', 'filters', 'summary', 'departments'));
    }

    // ===========================
    // CREATE
    // ===========================

    public function create()
    {
        $this->requireAdminOrSuperAdmin();
        return view('admin.survey-harga.create');
    }

    // ===========================
    // STORE
    // ===========================

    public function store(Request $request)
    {
        $this->requireAdminOrSuperAdmin();

        $validated = $request->validate([
            'kelompok'            => 'required|in:SSH,SBU,HSPK,ASB',
            'judul'               => 'required|string|max:255',
            'kode_komponen'       => 'nullable|string|max:100',
            'kode_rekening'       => 'nullable|string|max:100',
            'spesifikasi_singkat' => 'nullable|string|max:255',
            'spesifikasi_detail'  => 'nullable|string',
            'satuan'              => 'required|string|max:50',
            'harga_usulan'        => 'required|numeric|min:0',
            // Toko 1
            'nama_toko_1'         => 'nullable|string|max:255',
            'harga_toko_1'        => 'nullable|numeric|min:0',
            'gambar_toko_1'       => 'nullable|image|max:3072',
            'link_belanja_1'      => 'nullable|url|max:500',
            // Toko 2
            'nama_toko_2'         => 'nullable|string|max:255',
            'harga_toko_2'        => 'nullable|numeric|min:0',
            'gambar_toko_2'       => 'nullable|image|max:3072',
            'link_belanja_2'      => 'nullable|url|max:500',
            // Toko 3
            'nama_toko_3'         => 'nullable|string|max:255',
            'harga_toko_3'        => 'nullable|numeric|min:0',
            'gambar_toko_3'       => 'nullable|image|max:3072',
            'link_belanja_3'      => 'nullable|url|max:500',
        ]);

        // Upload gambar
        foreach ([1, 2, 3] as $i) {
            $key = "gambar_toko_{$i}";
            if ($request->hasFile($key)) {
                $validated[$key] = $request->file($key)->store("survey-gambar", 'public');
            }
        }

        $validated['user_id']       = Auth::id();
        $validated['department_id'] = Auth::user()->department_id;
        $validated['harga_usulan']  = (int) str_replace(['.', ','], ['', ''], $validated['harga_usulan']);
        foreach ([1, 2, 3] as $i) {
            $key = "harga_toko_{$i}";
            if (isset($validated[$key])) {
                $validated[$key] = (int) str_replace(['.', ','], ['', ''], $validated[$key]);
            }
        }

        SurveyHarga::create($validated);
        log_activity(Auth::user(), 'tambah_survey_harga', "Menambahkan usulan {$validated['kelompok']}: {$validated['judul']}");

        return redirect()->route('survey-harga.index')->with('success', "Usulan \"{$validated['judul']}\" berhasil ditambahkan!");
    }

    // ===========================
    // EDIT
    // ===========================

    public function edit(SurveyHarga $surveyHarga)
    {
        $this->requireAdminOrSuperAdmin();
        $user = Auth::user();

        if (!$user->isPureSuperAdmin() && $surveyHarga->department_id !== $user->department_id) {
            abort(403);
        }

        return view('admin.survey-harga.edit', ['survey' => $surveyHarga]);
    }

    // ===========================
    // UPDATE
    // ===========================

    public function update(Request $request, SurveyHarga $surveyHarga)
    {
        $this->requireAdminOrSuperAdmin();
        $user = Auth::user();

        if (!$user->isPureSuperAdmin() && $surveyHarga->department_id !== $user->department_id) {
            abort(403);
        }

        $validated = $request->validate([
            'kelompok'            => 'required|in:SSH,SBU,HSPK,ASB',
            'judul'               => 'required|string|max:255',
            'kode_komponen'       => 'nullable|string|max:100',
            'kode_rekening'       => 'nullable|string|max:100',
            'spesifikasi_singkat' => 'nullable|string|max:255',
            'spesifikasi_detail'  => 'nullable|string',
            'satuan'              => 'required|string|max:50',
            'harga_usulan'        => 'required|numeric|min:0',
            'nama_toko_1'         => 'nullable|string|max:255',
            'harga_toko_1'        => 'nullable|numeric|min:0',
            'gambar_toko_1'       => 'nullable|image|max:3072',
            'link_belanja_1'      => 'nullable|url|max:500',
            'nama_toko_2'         => 'nullable|string|max:255',
            'harga_toko_2'        => 'nullable|numeric|min:0',
            'gambar_toko_2'       => 'nullable|image|max:3072',
            'link_belanja_2'      => 'nullable|url|max:500',
            'nama_toko_3'         => 'nullable|string|max:255',
            'harga_toko_3'        => 'nullable|numeric|min:0',
            'gambar_toko_3'       => 'nullable|image|max:3072',
            'link_belanja_3'      => 'nullable|url|max:500',
        ]);

        foreach ([1, 2, 3] as $i) {
            $key = "gambar_toko_{$i}";
            if ($request->hasFile($key)) {
                // Hapus gambar lama
                if ($surveyHarga->$key) {
                    Storage::disk('public')->delete($surveyHarga->$key);
                }
                $validated[$key] = $request->file($key)->store("survey-gambar", 'public');
            } else {
                // Pertahankan gambar lama jika tidak di-upload ulang
                unset($validated[$key]);
            }
        }

        $validated['harga_usulan'] = (int) str_replace(['.', ','], ['', ''], $validated['harga_usulan']);
        foreach ([1, 2, 3] as $i) {
            $key = "harga_toko_{$i}";
            if (isset($validated[$key])) {
                $validated[$key] = (int) str_replace(['.', ','], ['', ''], $validated[$key]);
            }
        }

        $surveyHarga->update($validated);
        log_activity(Auth::user(), 'ubah_survey_harga', "Memperbarui usulan: {$surveyHarga->judul}");

        return redirect()->route('survey-harga.index')->with('success', "Usulan \"{$surveyHarga->judul}\" berhasil diperbarui!");
    }

    // ===========================
    // DESTROY
    // ===========================

    public function destroy(SurveyHarga $surveyHarga)
    {
        $this->requireAdminOrSuperAdmin();
        $user = Auth::user();

        if (!$user->isPureSuperAdmin() && $surveyHarga->department_id !== $user->department_id) {
            abort(403);
        }

        // Hapus gambar terkait
        foreach ([1, 2, 3] as $i) {
            $key = "gambar_toko_{$i}";
            if ($surveyHarga->$key) {
                Storage::disk('public')->delete($surveyHarga->$key);
            }
        }

        $judul = $surveyHarga->judul;
        $surveyHarga->delete();
        log_activity(Auth::user(), 'hapus_survey_harga', "Menghapus usulan: {$judul}");

        return back()->with('success', "Usulan \"{$judul}\" berhasil dihapus.");
    }

    // ===========================
    // UPDATE STATUS (Super Admin)
    // ===========================

    public function updateStatus(Request $request, SurveyHarga $surveyHarga)
    {
        if (!Auth::user()->isPureSuperAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'status'         => 'required|in:diajukan,disetujui,ditolak',
            'catatan_admin'  => 'nullable|string|max:500',
        ]);

        $surveyHarga->update($validated);
        log_activity(Auth::user(), 'ubah_status_survey_harga', "Mengubah status usulan \"{$surveyHarga->judul}\" menjadi {$validated['status']}");

        return back()->with('success', "Status usulan berhasil diubah menjadi \"{$validated['status']}\".");
    }

    // ===========================
    // EXPORT PDF
    // ===========================

    public function exportPdf(SurveyHarga $surveyHarga)
    {
        $this->requireAdminOrSuperAdmin();
        $user = Auth::user();

        if (!$user->isPureSuperAdmin() && $surveyHarga->department_id !== $user->department_id) {
            abort(403);
        }

        $tanggal = Carbon::now()->translatedFormat('d F Y');

        $pdf = Pdf::loadView('admin.survey-harga.pdf', compact('surveyHarga', 'tanggal'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'dpi'                  => 150,
                'defaultFont'          => 'helvetica',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => false,
            ]);

        $filename = 'Survei_Harga_' . str_replace(' ', '_', $surveyHarga->judul) . '_' . Carbon::now()->format('Ymd') . '.pdf';
        return $pdf->download($filename);
    }
}
