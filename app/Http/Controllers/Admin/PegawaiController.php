<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use App\Exports\PegawaiExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class PegawaiController extends Controller
{
    /**
     * Pastikan hanya super admin murni yang bisa create, update, delete.
     */
    private function requireAdmin(): void
    {
        $user = Auth::user();
        if (!$user || (!$user->isPureSuperAdmin() && !$user->isAdmin())) {
            abort(403, 'Akses ditolak. Hanya Super Admin dan Admin yang dapat mengubah data kepegawaian.');
        }
    }

    // ====================================================
    // INDEX — Daftar Pegawai dengan Filter & Paginasi
    // ====================================================

    public function index(Request $request)
    {
        $user    = Auth::user();
        $filters = $request->only([
            'search', 'kategori', 'unit_kerja', 'status', 'tmt_dari', 'tmt_sampai',
        ]);

        // ── Department-scoped access ──────────────────────────────────────────
        // Super Admin dapat melihat & memfilter semua pegawai.
        // Admin Bidang & User hanya dapat melihat pegawai dari bidangnya sendiri.
        $scopedDepartment = null;
        if (!$user->isPureSuperAdmin() && $user->department) {
            $scopedDepartment        = $user->department;
            $filters['unit_kerja']   = $user->department->name; // paksa filter
        }
        // ─────────────────────────────────────────────────────────────────────

        $pegawais = Pegawai::filter($filters)
            ->orderBy('nama_lengkap')
            ->paginate(15)
            ->withQueryString();

        // KPI Summary Cards — scope per department jika bukan Super Admin
        $baseQuery = $scopedDepartment
            ? Pegawai::where('unit_kerja', $scopedDepartment->name)
            : Pegawai::query();

        $summary = [
            'total'     => (clone $baseQuery)->count(),
            'asn_pns'   => (clone $baseQuery)->where('kategori', 'ASN (PNS)')->count(),
            'p3k_penuh' => (clone $baseQuery)->where('kategori', 'P3K Penuh Waktu')->count(),
            'p3k_paruh' => (clone $baseQuery)->where('kategori', 'P3K Paruh Waktu')->count(),
            'bsn'       => (clone $baseQuery)->where('kategori', 'BSN / Non-ASN')->count(),
        ];

        $statusDistribusi = [
            'Aktif'         => (clone $baseQuery)->where('status', 'Aktif')->count(),
            'Cuti'          => (clone $baseQuery)->where('status', 'Cuti')->count(),
            'Tugas Belajar' => (clone $baseQuery)->where('status', 'Tugas Belajar')->count(),
            'Non-Aktif'     => (clone $baseQuery)->where('status', 'Non-Aktif')->count(),
        ];

        // Daftar unit kerja untuk dropdown filter (hanya untuk Super Admin)
        $unitKerjaList = \App\Models\Department::orderBy('name')->pluck('name');

        return view('admin.kepegawaian.index', compact(
            'pegawais', 'filters', 'summary', 'statusDistribusi', 'unitKerjaList',
            'scopedDepartment'
        ));
    }

    // ====================================================
    // STORE — Simpan Pegawai Baru
    // ====================================================

    public function store(Request $request)
    {
        $this->requireAdmin();

        $validated = $request->validate([
            'nik'                 => 'required|digits:16|unique:pegawais,nik',
            'nip_nrk'             => 'nullable|string|max:25',
            'nama_lengkap'        => 'required|string|max:255',
            'jenis_kelamin'       => 'required|in:L,P',
            'kategori'            => 'required|in:ASN (PNS),P3K Penuh Waktu,P3K Paruh Waktu,BSN / Non-ASN',
            'pangkat_golongan'    => 'nullable|string|max:100',
            'jabatan'             => 'required|string|max:255',
            'unit_kerja'          => 'required|string|max:255',
            'tmt_sk'              => 'nullable|date',
            'masa_kontrak'        => 'nullable|string|max:50',
            'jam_kerja_mingguan'  => 'required|numeric|min:1|max:60',
            'pendidikan_terakhir' => 'nullable|string|max:100',
            'no_hp'               => 'nullable|string|max:20',
            'email'               => 'nullable|email|max:255',
            'status'              => 'required|in:Aktif,Cuti,Tugas Belajar,Non-Aktif',
        ]);

        // Jika bukan Super Admin, paksa unit_kerja ke departemen user
        $user = Auth::user();
        if (!$user->isPureSuperAdmin() && $user->department) {
            $validated['unit_kerja'] = $user->department->name;
        }

        Pegawai::create($validated);
        log_activity(Auth::user(), 'tambah_pegawai', "Menambahkan pegawai baru: {$validated['nama_lengkap']} (NIK: {$validated['nik']})");

        return back()->with('success', "Pegawai {$validated['nama_lengkap']} berhasil ditambahkan!");
    }

    // ====================================================
    // STORE BULK — Simpan Banyak Pegawai Sekaligus
    // ====================================================

    public function storeBulk(Request $request)
    {
        $this->requireAdmin();

        $request->validate([
            'pegawai'                       => 'required|array|min:1',
            'pegawai.*.nik'                 => 'required|digits:16|distinct',
            'pegawai.*.nip_nrk'             => 'nullable|string|max:25',
            'pegawai.*.nama_lengkap'        => 'required|string|max:255',
            'pegawai.*.jenis_kelamin'       => 'required|in:L,P',
            'pegawai.*.kategori'            => 'required|in:ASN (PNS),P3K Penuh Waktu,P3K Paruh Waktu,BSN / Non-ASN',
            'pegawai.*.pangkat_golongan'    => 'nullable|string|max:100',
            'pegawai.*.jabatan'             => 'required|string|max:255',
            'pegawai.*.unit_kerja'          => 'required|string|max:255',
            'pegawai.*.tmt_sk'              => 'nullable|date',
            'pegawai.*.masa_kontrak'        => 'nullable|string|max:50',
            'pegawai.*.jam_kerja_mingguan'  => 'required|numeric|min:1|max:60',
            'pegawai.*.pendidikan_terakhir' => 'nullable|string|max:100',
            'pegawai.*.no_hp'               => 'nullable|string|max:20',
            'pegawai.*.email'               => 'nullable|email|max:255',
            'pegawai.*.status'              => 'required|in:Aktif,Cuti,Tugas Belajar,Non-Aktif',
        ], [
            'pegawai.*.nik.required'          => 'NIK pada baris ke-:position wajib diisi.',
            'pegawai.*.nik.digits'            => 'NIK pada baris ke-:position harus 16 digit.',
            'pegawai.*.nik.distinct'          => 'NIK pada baris ke-:position ada duplikat dalam form ini.',
            'pegawai.*.nama_lengkap.required' => 'Nama lengkap pada baris ke-:position wajib diisi.',
            'pegawai.*.jabatan.required'      => 'Jabatan pada baris ke-:position wajib diisi.',
            'pegawai.*.unit_kerja.required'   => 'Unit kerja pada baris ke-:position wajib diisi.',
        ]);

        // Cek duplikasi NIK di Database
        $inputNiks = collect($request->pegawai)->pluck('nik')->filter()->toArray();
        $existingNiks = Pegawai::whereIn('nik', $inputNiks)->pluck('nik')->toArray();

        if (!empty($existingNiks)) {
            $nikList = implode(', ', $existingNiks);
            return back()->withInput()->withErrors([
                'bulk_error' => "Gagal menyimpan! NIK berikut sudah terdaftar di sistem: {$nikList}. Silakan perbaiki NIK tersebut."
            ]);
        }

        $insertedCount = 0;
        $currentUser   = Auth::user();
        $forcedUnit    = (!$currentUser->isPureSuperAdmin() && $currentUser->department)
                         ? $currentUser->department->name
                         : null;

        \Illuminate\Support\Facades\DB::transaction(function () use ($request, &$insertedCount, $forcedUnit) {
            foreach ($request->pegawai as $item) {
                // Ensure default values if empty
                $item['jam_kerja_mingguan'] = $item['jam_kerja_mingguan'] ?? 37.5;
                $item['status'] = $item['status'] ?? 'Aktif';
                // Paksa unit_kerja ke department user jika bukan super admin
                if ($forcedUnit) {
                    $item['unit_kerja'] = $forcedUnit;
                }
                Pegawai::create($item);
                $insertedCount++;
            }
        });

        log_activity(Auth::user(), 'tambah_pegawai_bulk', "Menambahkan {$insertedCount} data pegawai secara massal");

        return redirect()->route('kepegawaian.index')->with('success', "Berhasil menambahkan {$insertedCount} data pegawai baru secara sekaligus!");
    }

    // ====================================================
    // UPDATE — Edit Data Pegawai
    // ====================================================

    public function update(Request $request, Pegawai $pegawai)
    {
        $this->requireAdmin();

        $validated = $request->validate([
            'nik'                 => 'required|digits:16|unique:pegawais,nik,' . $pegawai->id,
            'nip_nrk'             => 'nullable|string|max:25',
            'nama_lengkap'        => 'required|string|max:255',
            'jenis_kelamin'       => 'required|in:L,P',
            'kategori'            => 'required|in:ASN (PNS),P3K Penuh Waktu,P3K Paruh Waktu,BSN / Non-ASN',
            'pangkat_golongan'    => 'nullable|string|max:100',
            'jabatan'             => 'required|string|max:255',
            'unit_kerja'          => 'required|string|max:255',
            'tmt_sk'              => 'nullable|date',
            'masa_kontrak'        => 'nullable|string|max:50',
            'jam_kerja_mingguan'  => 'required|numeric|min:1|max:60',
            'pendidikan_terakhir' => 'nullable|string|max:100',
            'no_hp'               => 'nullable|string|max:20',
            'email'               => 'nullable|email|max:255',
            'status'              => 'required|in:Aktif,Cuti,Tugas Belajar,Non-Aktif',
        ]);

        $pegawai->update($validated);
        log_activity(Auth::user(), 'ubah_pegawai', "Memperbarui data pegawai: {$pegawai->nama_lengkap} (NIK: {$pegawai->nik})");

        return back()->with('success', "Data {$pegawai->nama_lengkap} berhasil diperbarui!");
    }

    // ====================================================
    // DESTROY — Hapus Pegawai
    // ====================================================

    public function destroy(Pegawai $pegawai)
    {
        $this->requireAdmin();

        $nama = $pegawai->nama_lengkap;
        $nik  = $pegawai->nik;
        $pegawai->delete();

        log_activity(Auth::user(), 'hapus_pegawai', "Menghapus data pegawai: {$nama} (NIK: {$nik})");

        return back()->with('success', "Data pegawai {$nama} berhasil dihapus.");
    }

    // ====================================================
    // EXPORT EXCEL — dengan filter aktif
    // ====================================================

    public function exportExcel(Request $request)
    {
        $filters  = $request->only(['search', 'kategori', 'unit_kerja', 'status', 'tmt_dari', 'tmt_sampai']);
        $filename = 'Data_Pegawai_' . Carbon::now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new PegawaiExport($filters), $filename);
    }

    // ====================================================
    // EXPORT PDF — dengan filter aktif (A4 Landscape)
    // ====================================================

    public function exportPdf(Request $request)
    {
        $filters  = $request->only(['search', 'kategori', 'unit_kerja', 'status', 'tmt_dari', 'tmt_sampai']);
        $pegawais = Pegawai::filter($filters)->orderBy('nama_lengkap')->get();
        $tanggal  = Carbon::now()->translatedFormat('d F Y');
        $judul    = 'Data Pegawai';
        if (!empty($filters['kategori'])) {
            $judul .= ' — ' . $filters['kategori'];
        }

        $pdf = Pdf::loadView('admin.kepegawaian.pdf', compact('pegawais', 'tanggal', 'judul', 'filters'))
            ->setPaper('a4', 'landscape')
            ->setOptions([
                'dpi'                     => 150,
                'defaultFont'             => 'helvetica',
                'isHtml5ParserEnabled'    => true,
                'isRemoteEnabled'         => false,
            ]);

        $filename = 'Data_Pegawai_' . Carbon::now()->format('Ymd_His') . '.pdf';
        return $pdf->download($filename);
    }

    // ====================================================
    // QUICK EXPORT — 1-click unduh per kategori
    // ====================================================

    public function quickExport(Request $request, string $type, string $format)
    {
        $allowedTypes = [
            'semua'      => [],
            'asn'        => ['kategori' => 'ASN (PNS)'],
            'p3k-penuh'  => ['kategori' => 'P3K Penuh Waktu'],
            'p3k-paruh'  => ['kategori' => 'P3K Paruh Waktu'],
            'bsn'        => ['kategori' => 'BSN / Non-ASN'],
            'aktif'      => ['status'   => 'Aktif'],
            'non-aktif'  => ['status'   => 'Non-Aktif'],
        ];

        if (!array_key_exists($type, $allowedTypes)) {
            abort(404, 'Tipe ekspor tidak ditemukan.');
        }

        $filters   = $allowedTypes[$type];
        $labelMap  = [
            'semua'     => 'Semua',
            'asn'       => 'ASN_PNS',
            'p3k-penuh' => 'P3K_Penuh_Waktu',
            'p3k-paruh' => 'P3K_Paruh_Waktu',
            'bsn'       => 'BSN_NonASN',
            'aktif'     => 'Status_Aktif',
            'non-aktif' => 'Status_NonAktif',
        ];
        $label     = $labelMap[$type];
        $timestamp = Carbon::now()->format('Ymd_His');

        if ($format === 'excel') {
            $filename = "Pegawai_{$label}_{$timestamp}.xlsx";
            return Excel::download(new PegawaiExport($filters, "Pegawai {$label}"), $filename);
        }

        if ($format === 'pdf') {
            $pegawais = Pegawai::filter($filters)->orderBy('nama_lengkap')->get();
            $tanggal  = Carbon::now()->translatedFormat('d F Y');
            $judul    = 'Data Pegawai — ' . str_replace('_', ' ', $label);

            $pdf = Pdf::loadView('admin.kepegawaian.pdf', compact('pegawais', 'tanggal', 'judul', 'filters'))
                ->setPaper('a4', 'landscape')
                ->setOptions([
                    'dpi'                  => 150,
                    'defaultFont'          => 'helvetica',
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled'      => false,
                ]);

            $filename = "Pegawai_{$label}_{$timestamp}.pdf";
            return $pdf->download($filename);
        }

        abort(400, 'Format tidak valid. Gunakan "excel" atau "pdf".');
    }

    // ====================================================
    // IMPORT — Impor Pegawai dari Excel
    // ====================================================

    public function import(Request $request, \App\Services\PegawaiExcelService $service)
    {
        $this->requireAdmin();

        $request->validate([
            'file_excel'          => 'nullable|file|mimes:xlsx,xls,csv|max:15360',
            'duplicate_strategy'  => 'required|in:update,skip',
            'server_file'         => 'nullable|string',
        ]);

        $duplicateStrategy = $request->input('duplicate_strategy', 'update');
        $filePath = null;

        // Cek apakah admin memilih file upload atau file server (misal PNS LENGKAP 2026.xlsx)
        if ($request->hasFile('file_excel')) {
            $uploadedFile = $request->file('file_excel');
            $filePath = $uploadedFile->getRealPath();
        } elseif ($request->filled('server_file')) {
            $potentialPath = base_path($request->input('server_file'));
            if (file_exists($potentialPath)) {
                $filePath = $potentialPath;
            } else {
                return back()->withErrors(['import_error' => 'File server tidak ditemukan: ' . $request->input('server_file')]);
            }
        } else {
            return back()->withErrors(['import_error' => 'Silakan pilih file Excel untuk diimpor.']);
        }

        try {
            $result = $service->import($filePath, $duplicateStrategy);

            $msg = "Impor data pegawai berhasil! Total {$result['imported']} pegawai baru ditambahkan";
            if ($result['updated'] > 0) {
                $msg .= ", {$result['updated']} data pegawai diperbarui";
            }
            if ($result['skipped'] > 0) {
                $msg .= ", {$result['skipped']} data dilewati (skip)";
            }
            $msg .= ".";

            log_activity(Auth::user(), 'import_pegawai', "Mengimpor {$result['total_rows']} data pegawai dari Excel");

            return redirect()->route('kepegawaian.index')->with('success', $msg);
        } catch (\Throwable $e) {
            return back()->withErrors(['import_error' => 'Terjadi kesalahan saat memproses Excel: ' . $e->getMessage()]);
        }
    }

    // ====================================================
    // DOWNLOAD TEMPLATE — Template Excel Import
    // ====================================================

    public function downloadTemplate(\App\Services\PegawaiExcelService $service)
    {
        $spreadsheet = $service->generateTemplate();
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        $filename = 'Template_Import_Pegawai_DLH.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
