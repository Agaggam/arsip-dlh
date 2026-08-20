<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengawasan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PengawasanExport;
use Carbon\Carbon;

class PengawasanController extends Controller
{
    private function requireAdminOrSuperAdmin(): void
    {
        $user = Auth::user();
        if (!$user || (!$user->isAdmin() && !$user->isPureSuperAdmin())) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengubah data pengawasan.');
        }
    }

    // ===========================
    // INDEX (UNIVERSAL SESI PENGAWASAN)
    // ===========================

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'kecamatan', 'jenis_pengawasan', 'tahun']);

        // Query unik batch_id untuk paginasi sesi pengawasan
        $batchQuery = Pengawasan::filter($filters)
            ->whereNotNull('batch_id')
            ->select('batch_id', DB::raw('MAX(waktu_pengawasan) as latest_waktu'), DB::raw('MAX(created_at) as latest_created'))
            ->groupBy('batch_id')
            ->orderByDesc('latest_created');

        $paginatedBatches = $batchQuery->paginate(15)->withQueryString();

        $batchIds = $paginatedBatches->pluck('batch_id');
        $allRecords = Pengawasan::with('user')
            ->whereIn('batch_id', $batchIds)
            ->orderBy('waktu_pengawasan')
            ->get()
            ->groupBy('batch_id');

        // Rakit data sesi pengawasan universal
        $sessions = $paginatedBatches->map(function ($batch) use ($allRecords) {
            $items = $allRecords->get($batch->batch_id, collect());
            if ($items->isEmpty()) {
                return null;
            }
            $first = $items->first();

            return (object) [
                'batch_id'         => $batch->batch_id,
                'first_id'         => $first->id,
                'first_item'       => $first,
                'tahun'            => $first->tahun,
                'nama_pengawas'    => $first->nama_pengawas,
                'kecamatan'        => $first->kecamatan,
                'jenis_pengawasan' => $first->jenis_pengawasan,
                'jenis_label'      => $first->jenis_label,
                'total_usaha'      => $items->count(),
                'nama_usaha_list'  => $items->pluck('nama_usaha')->filter()->values(),
                'waktu_pengawasan' => $items->max('waktu_pengawasan'),
                'jml_v'            => $items->sum('jml_v'),
                'jml_p'            => $items->sum('jml_p'),
                'jml_x'            => $items->sum('jml_x'),
                'items'            => $items,
            ];
        })->filter();

        // KPI Summary (Berdasarkan Total Usaha & Sesi)
        $summary = [
            'total_usaha'   => Pengawasan::count(),
            'total_sesi'    => Pengawasan::distinct('batch_id')->count('batch_id'),
            'batu'          => Pengawasan::where('kecamatan', 'Kecamatan Batu')->count(),
            'bumiaji'       => Pengawasan::where('kecamatan', 'Kecamatan Bumiaji')->count(),
            'junrejo'       => Pengawasan::where('kecamatan', 'Kecamatan Junrejo')->count(),
        ];

        // Tahun unik untuk filter
        $tahunList = Pengawasan::select('tahun')->distinct()->orderByDesc('tahun')->pluck('tahun');

        return view('admin.pengawasan.index', [
            'sessions'         => $sessions,
            'paginatedBatches' => $paginatedBatches,
            'filters'          => $filters,
            'summary'          => $summary,
            'tahunList'        => $tahunList,
        ]);
    }

    // ===========================
    // CREATE
    // ===========================

    public function create()
    {
        $this->requireAdminOrSuperAdmin();
        return view('admin.pengawasan.create');
    }

    // ===========================
    // STORE
    // ===========================

    public function store(Request $request)
    {
        $this->requireAdminOrSuperAdmin();

        $header = $request->validate([
            'tahun'            => 'required|integer|min:2020|max:' . (date('Y') + 1),
            'nama_pengawas'    => 'required|string|max:255',
            'kecamatan'        => 'required|in:Kecamatan Batu,Kecamatan Bumiaji,Kecamatan Junrejo',
            'jenis_pengawasan' => 'required|in:langsung,tidak_langsung',
        ]);

        $batchId = (string) Str::uuid();

        // Batch Multi-Usaha
        if ($request->has('items') && is_array($request->items)) {
            $filteredItems = array_values(array_filter($request->items, function ($item) {
                return !empty($item['nama_usaha']) && trim($item['nama_usaha']) !== '';
            }));

            if (empty($filteredItems)) {
                return back()->withInput()->withErrors(['items' => 'Harap isi setidaknya satu baris Nama Usaha.']);
            }

            $request->merge(['items' => $filteredItems]);

            $request->validate([
                'items'                      => 'required|array|min:1',
                'items.*.nama_usaha'         => 'required|string|max:255',
                'items.*.skala_usaha'        => 'required|string|max:100',
                'items.*.waktu_pengawasan'   => 'required|date',
                'items.*.keterangan'         => 'nullable|string|max:1000',
                'items.*.izin_lingkungan'    => 'required|in:v,p,x,-',
                'items.*.wajib_ubah_dok'     => 'required|in:v,p,x,-',
                'items.*.oss_rba'            => 'required|in:v,p,x,-',
                'items.*.lap'                => 'required|in:v,p,x,-',
                'items.*.grease_trap'        => 'required|in:v,p,x,-',
                'items.*.ipal'               => 'required|in:v,p,x,-',
                'items.*.kett_teknis_ipal'   => 'required|in:v,p,x,-',
                'items.*.pantau_ipal'        => 'required|in:v,p,x,-',
                'items.*.iplc_pertek'        => 'required|in:v,p,x,-',
                'items.*.pantau_udara'       => 'required|in:v,p,x,-',
                'items.*.inv_lb3'            => 'required|in:v,p,x,-',
                'items.*.tps_b3'             => 'required|in:v,p,x,-',
                'items.*.kett_teknis_b3'     => 'required|in:v,p,x,-',
                'items.*.izin_rintek'        => 'required|in:v,p,x,-',
                'items.*.sures_biopori'      => 'required|in:v,p,x,-',
                'items.*.pilah_sampah'       => 'required|in:v,p,x,-',
            ]);

            DB::transaction(function () use ($filteredItems, $header, $batchId) {
                foreach ($filteredItems as $item) {
                    $itemData = array_merge($header, $item, [
                        'batch_id' => $batchId,
                        'user_id'  => Auth::id(),
                        'status'   => 'disetujui',
                    ]);
                    Pengawasan::create($itemData);
                }
            });

            $count = count($filteredItems);
            log_activity(Auth::user(), 'tambah_pengawasan', "Menambahkan laporan pengawasan {$header['kecamatan']} berisi {$count} pelaku usaha");

            return redirect()->route('pengawasan.index')->with('success', "Laporan pengawasan berhasil disimpan ({$count} usaha di {$header['kecamatan']})!");
        }

        // Fallback single item
        $validated = $this->validatePengawasan($request);
        $validated['batch_id'] = $batchId;
        $validated['user_id']  = Auth::id();
        $validated['status']   = 'disetujui';

        Pengawasan::create($validated);
        log_activity(Auth::user(), 'tambah_pengawasan', "Menambahkan data pengawasan: {$validated['nama_usaha']} ({$validated['kecamatan']})");

        return redirect()->route('pengawasan.index')->with('success', "Data pengawasan berhasil ditambahkan!");
    }

    // ===========================
    // EDIT (UNIVERSAL SESI PENGAWASAN)
    // ===========================

    public function edit(Pengawasan $pengawasan)
    {
        $this->requireAdminOrSuperAdmin();

        $batchId = $pengawasan->batch_id;
        $items = $batchId 
            ? Pengawasan::where('batch_id', $batchId)->orderBy('waktu_pengawasan')->get()
            : collect([$pengawasan]);

        return view('admin.pengawasan.edit', [
            'pengawasan' => $pengawasan,
            'items'      => $items,
        ]);
    }

    // ===========================
    // UPDATE (UNIVERSAL SESI PENGAWASAN)
    // ===========================

    public function update(Request $request, Pengawasan $pengawasan)
    {
        $this->requireAdminOrSuperAdmin();

        $batchId = $pengawasan->batch_id ?? (string) Str::uuid();

        // Update Batch Items
        if ($request->has('items') && is_array($request->items)) {
            $header = $request->validate([
                'tahun'            => 'required|integer|min:2020|max:' . (date('Y') + 1),
                'nama_pengawas'    => 'required|string|max:255',
                'kecamatan'        => 'required|in:Kecamatan Batu,Kecamatan Bumiaji,Kecamatan Junrejo',
                'jenis_pengawasan' => 'required|in:langsung,tidak_langsung',
            ]);

            $submittedItems = array_values(array_filter($request->input('items', []), function ($item) {
                return !empty($item['nama_usaha']) && trim($item['nama_usaha']) !== '';
            }));

            if (empty($submittedItems)) {
                return back()->withInput()->withErrors(['items' => 'Harap isi setidaknya satu baris Nama Usaha.']);
            }

            DB::transaction(function () use ($submittedItems, $header, $batchId) {
                $keptIds = [];

                foreach ($submittedItems as $item) {
                    $itemData = array_merge($header, $item, [
                        'batch_id' => $batchId,
                        'user_id'  => Auth::id(),
                        'status'   => 'disetujui',
                    ]);

                    if (!empty($item['id'])) {
                        $target = Pengawasan::find($item['id']);
                        if ($target) {
                            $target->update($itemData);
                            $keptIds[] = $target->id;
                            continue;
                        }
                    }

                    // Baris baru yang ditambahkan di form edit
                    unset($itemData['id']);
                    $newCreated = Pengawasan::create($itemData);
                    $keptIds[] = $newCreated->id;
                }

                // Hapus baris lama yang dibuang user di tabel spreadsheet
                Pengawasan::where('batch_id', $batchId)
                    ->whereNotIn('id', $keptIds)
                    ->delete();
            });

            log_activity(Auth::user(), 'ubah_pengawasan', "Memperbarui laporan pengawasan di {$header['kecamatan']}");

            return redirect()->route('pengawasan.index')->with('success', "Laporan pengawasan {$header['kecamatan']} berhasil diperbarui!");
        }

        // Fallback single item update
        $validated = $this->validatePengawasan($request);
        $validated['status'] = 'disetujui';

        $pengawasan->update($validated);
        log_activity(Auth::user(), 'ubah_pengawasan', "Memperbarui data pengawasan: {$pengawasan->nama_usaha}");

        return redirect()->route('pengawasan.index')->with('success', "Data pengawasan \"{$pengawasan->nama_usaha}\" berhasil diperbarui!");
    }

    // ===========================
    // DESTROY (UNIVERSAL SESI PENGAWASAN)
    // ===========================

    public function destroy(Pengawasan $pengawasan)
    {
        $this->requireAdminOrSuperAdmin();

        $batchId = $pengawasan->batch_id;
        if ($batchId) {
            $count = Pengawasan::where('batch_id', $batchId)->count();
            Pengawasan::where('batch_id', $batchId)->delete();
            log_activity(Auth::user(), 'hapus_pengawasan', "Menghapus laporan pengawasan {$pengawasan->kecamatan} ({$count} usaha)");
            return back()->with('success', "Laporan pengawasan ({$count} usaha) berhasil dihapus.");
        }

        $nama = $pengawasan->nama_usaha;
        $pengawasan->delete();
        log_activity(Auth::user(), 'hapus_pengawasan', "Menghapus data pengawasan: {$nama}");

        return back()->with('success', "Data pengawasan \"{$nama}\" berhasil dihapus.");
    }

    // ===========================
    // EXPORT EXCEL
    // ===========================

    public function exportExcel(Request $request, ?Pengawasan $pengawasan = null)
    {
        $filters = $request->only(['search', 'kecamatan', 'jenis_pengawasan', 'tahun']);
        
        $filename = $pengawasan 
            ? 'Laporan_Pengawasan_' . str_replace(' ', '_', $pengawasan->kecamatan) . '_' . Carbon::now()->format('Ymd') . '.xlsx'
            : 'Rekap_Pengawasan_LH_' . Carbon::now()->format('Ymd') . '.xlsx';

        return Excel::download(new PengawasanExport($filters, $pengawasan), $filename);
    }

    // ===========================
    // EXPORT PDF
    // ===========================

    public function exportPdf(Request $request, ?Pengawasan $pengawasan = null)
    {
        if ($pengawasan) {
            $records = $pengawasan->batch_id
                ? Pengawasan::where('batch_id', $pengawasan->batch_id)->orderBy('waktu_pengawasan')->get()
                : collect([$pengawasan]);
            $tahun = $pengawasan->tahun;
            $namaPengawas = $pengawasan->nama_pengawas;
        } else {
            $filters = $request->only(['search', 'kecamatan', 'jenis_pengawasan', 'tahun']);
            $tahun = $filters['tahun'] ?? date('Y');
            $records = Pengawasan::filter($filters)->orderBy('kecamatan')->orderBy('jenis_pengawasan')->orderBy('waktu_pengawasan')->get();
            $namaPengawas = $records->first()?->nama_pengawas ?? 'Tim Pengawas Lingkungan Hidup';
        }

        // Kelompokkan per kecamatan → jenis
        $grouped = $records->groupBy('kecamatan')->map(fn($items) => $items->groupBy('jenis_pengawasan'));

        $tanggal = Carbon::now()->translatedFormat('d F Y');

        $pdf = Pdf::loadView('admin.pengawasan.pdf', compact('grouped', 'tahun', 'namaPengawas', 'tanggal'))
            ->setPaper('a4', 'landscape')
            ->setOptions([
                'dpi'                  => 150,
                'defaultFont'          => 'helvetica',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => false,
            ]);

        $filename = $pengawasan 
            ? 'Laporan_Pengawasan_' . str_replace(' ', '_', $pengawasan->kecamatan) . '_' . Carbon::now()->format('Ymd') . '.pdf'
            : 'Rekap_Pengawasan_LH_' . $tahun . '_' . Carbon::now()->format('Ymd') . '.pdf';

        return $pdf->download($filename);
    }

    // ===========================
    // VALIDASI SINGLE ITEM
    // ===========================

    private function validatePengawasan(Request $request): array
    {
        return $request->validate([
            'tahun'            => 'required|integer|min:2020|max:' . (date('Y') + 1),
            'nama_pengawas'    => 'required|string|max:255',
            'kecamatan'        => 'required|in:Kecamatan Batu,Kecamatan Bumiaji,Kecamatan Junrejo',
            'jenis_pengawasan' => 'required|in:langsung,tidak_langsung',
            'nama_usaha'       => 'required|string|max:255',
            'skala_usaha'      => 'required|string|max:100',
            'waktu_pengawasan' => 'required|date',
            'keterangan'       => 'nullable|string|max:1000',
            'izin_lingkungan'  => 'required|in:v,p,x,-',
            'wajib_ubah_dok'   => 'required|in:v,p,x,-',
            'oss_rba'          => 'required|in:v,p,x,-',
            'lap'              => 'required|in:v,p,x,-',
            'grease_trap'      => 'required|in:v,p,x,-',
            'ipal'             => 'required|in:v,p,x,-',
            'kett_teknis_ipal' => 'required|in:v,p,x,-',
            'pantau_ipal'      => 'required|in:v,p,x,-',
            'iplc_pertek'      => 'required|in:v,p,x,-',
            'pantau_udara'     => 'required|in:v,p,x,-',
            'inv_lb3'          => 'required|in:v,p,x,-',
            'tps_b3'           => 'required|in:v,p,x,-',
            'kett_teknis_b3'   => 'required|in:v,p,x,-',
            'izin_rintek'      => 'required|in:v,p,x,-',
            'sures_biopori'    => 'required|in:v,p,x,-',
            'pilah_sampah'     => 'required|in:v,p,x,-',
        ]);
    }
}
