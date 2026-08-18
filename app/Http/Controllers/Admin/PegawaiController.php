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
     * Pastikan hanya super admin murni yang bisa akses.
     */
    private function requireAdmin(): void
    {
        $user = Auth::user();
        if (!$user || !$user->isPureSuperAdmin()) {
            abort(403, 'Akses ditolak. Hanya Super Admin yang dapat mengakses modul ini.');
        }
    }

    // ====================================================
    // INDEX — Daftar Pegawai dengan Filter & Paginasi
    // ====================================================

    public function index(Request $request)
    {
        $this->requireAdmin();

        $filters = $request->only([
            'search', 'kategori', 'unit_kerja', 'status', 'tmt_dari', 'tmt_sampai',
        ]);

        $pegawais = Pegawai::filter($filters)
            ->orderBy('nama_lengkap')
            ->paginate(15)
            ->withQueryString();

        // KPI Summary Cards
        $summary = [
            'total'           => Pegawai::count(),
            'asn_pns'         => Pegawai::where('kategori', 'ASN (PNS)')->count(),
            'p3k_penuh'       => Pegawai::where('kategori', 'P3K Penuh Waktu')->count(),
            'p3k_paruh'       => Pegawai::where('kategori', 'P3K Paruh Waktu')->count(),
            'bsn'             => Pegawai::where('kategori', 'BSN / Non-ASN')->count(),
        ];

        $statusDistribusi = [
            'Aktif'         => Pegawai::where('status', 'Aktif')->count(),
            'Cuti'          => Pegawai::where('status', 'Cuti')->count(),
            'Tugas Belajar' => Pegawai::where('status', 'Tugas Belajar')->count(),
            'Non-Aktif'     => Pegawai::where('status', 'Non-Aktif')->count(),
        ];

        // Daftar unit kerja unik untuk dropdown filter
        $unitKerjaList = \App\Models\Department::orderBy('name')->pluck('name');

        return view('admin.kepegawaian.index', compact(
            'pegawais', 'filters', 'summary', 'statusDistribusi', 'unitKerjaList'
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

        Pegawai::create($validated);
        log_activity(Auth::user(), 'tambah_pegawai', "Menambahkan pegawai baru: {$validated['nama_lengkap']} (NIK: {$validated['nik']})");

        return back()->with('success', "Pegawai {$validated['nama_lengkap']} berhasil ditambahkan!");
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
        $this->requireAdmin();

        $filters  = $request->only(['search', 'kategori', 'unit_kerja', 'status', 'tmt_dari', 'tmt_sampai']);
        $filename = 'Data_Pegawai_' . Carbon::now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new PegawaiExport($filters), $filename);
    }

    // ====================================================
    // EXPORT PDF — dengan filter aktif (A4 Landscape)
    // ====================================================

    public function exportPdf(Request $request)
    {
        $this->requireAdmin();

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
        $this->requireAdmin();

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
}
