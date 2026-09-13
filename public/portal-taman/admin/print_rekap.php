<?php

session_start();

require_once '../config/database.php';
require_once '../config/functions.php';

requireAdmin();

// Ambil data taman
$parks = getAll($conn, "SELECT * FROM parks ORDER BY name ASC");

// Ambil info kota
$city = getOne($conn, "SELECT * FROM city_info LIMIT 1");
$cityName = $city['city_name'] ?? 'Kota Batu';

// Hitung rekap
$totalParks = count($parks);
$totalArea = 0;
$totalEmployees = 0;
$statusCounts = ['Aktif' => 0, 'Dalam Perawatan' => 0, 'Pasif' => 0];
$categoryCounts = [];

foreach ($parks as $p) {
    $area = (float)($p['area'] ?? 0);
    $emp = (int)($p['employee_count'] ?? 0);
    $st = $p['status'] ?? 'Aktif';
    $cat = $p['category'] ?? 'Lainnya';

    $totalArea += $area;
    $totalEmployees += $emp;

    if (isset($statusCounts[$st])) {
        $statusCounts[$st]++;
    } else {
        $statusCounts[$st] = 1;
    }

    if (!isset($categoryCounts[$cat])) {
        $categoryCounts[$cat] = 0;
    }
    $categoryCounts[$cat]++;
}

// Format Tanggal Indonesia
function tglIndo($tanggal) {
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    $pecahkan = explode('-', date('Y-m-d', strtotime($tanggal)));
    return $pecahkan[2] . ' ' . $bulan[(int)$pecahkan[1]] . ' ' . $pecahkan[0];
}

$tanggalHariIni = tglIndo(date('Y-m-d'));

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekapitulasi Data Taman Kota - DLH <?= e($cityName) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #145f3c;
            --primary-light: #e8f5ee;
            --text: #1a202c;
            --text-muted: #4a5568;
            --border: #cbd5e1;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: var(--text);
            background: #f1f5f9;
            padding: 24px;
            font-size: 11pt;
            line-height: 1.4;
        }

        /* TOOLBAR AKSI (Hanya tampil di layar browser) */
        .print-toolbar {
            max-width: 1100px;
            margin: 0 auto 20px auto;
            background: #ffffff;
            padding: 14px 20px;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.06);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            border: 1px solid #e2e8f0;
        }

        .toolbar-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .toolbar-title {
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
        }

        .toolbar-desc {
            font-size: 12px;
            color: #64748b;
        }

        .toolbar-buttons {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 600;
            border-radius: 8px;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
        }

        .btn-print {
            background: #059669;
            color: #ffffff;
        }
        .btn-print:hover {
            background: #047857;
        }

        .btn-excel {
            background: #1e293b;
            color: #ffffff;
        }
        .btn-excel:hover {
            background: #0f172a;
        }

        .btn-back {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #cbd5e1;
        }
        .btn-back:hover {
            background: #e2e8f0;
        }

        /* LEMBAR DOKUMEN CETAK (A4 Landscape) */
        .sheet {
            max-width: 1100px;
            margin: 0 auto;
            background: #ffffff;
            padding: 32px 40px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            border: 1px solid #e2e8f0;
        }

        /* KOP SURAT RESMI */
        .kop-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-bottom: 14px;
            border-bottom: 3px double #0f172a;
            margin-bottom: 20px;
        }

        .kop-logo {
            width: 75px;
            height: 75px;
            object-fit: contain;
        }

        .kop-text {
            text-align: center;
            flex: 1;
            padding: 0 16px;
        }

        .kop-instansi-atas {
            font-size: 13pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #1e293b;
        }

        .kop-instansi-utama {
            font-size: 16pt;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #145f3c;
            margin: 2px 0;
        }

        .kop-alamat {
            font-size: 8.5pt;
            color: #475569;
            line-height: 1.35;
        }

        /* JUDUL LAPORAN */
        .report-title-box {
            text-align: center;
            margin-bottom: 20px;
        }

        .report-title {
            font-size: 13pt;
            font-weight: 800;
            text-transform: uppercase;
            color: #0f172a;
            letter-spacing: 0.5px;
        }

        .report-subtitle {
            font-size: 9.5pt;
            color: #64748b;
            margin-top: 4px;
        }

        /* MINI REKAP STATISTIK */
        .rekap-stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 20px;
        }

        .rekap-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 10px 14px;
            text-align: center;
        }

        .rekap-card-label {
            font-size: 8pt;
            font-weight: 700;
            text-transform: uppercase;
            color: #64748b;
            letter-spacing: 0.5px;
        }

        .rekap-card-val {
            font-size: 14pt;
            font-weight: 800;
            color: #145f3c;
            margin-top: 2px;
        }

        .rekap-card-sub {
            font-size: 7.5pt;
            color: #94a3b8;
        }

        /* TABEL DATA */
        table.rekap-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
            margin-bottom: 24px;
        }

        table.rekap-table th,
        table.rekap-table td {
            border: 1px solid #cbd5e1;
            padding: 7px 9px;
            vertical-align: middle;
        }

        table.rekap-table th {
            background-color: #145f3c;
            color: #ffffff;
            font-weight: 700;
            text-align: center;
            font-size: 8.5pt;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        table.rekap-table tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }

        table.rekap-table tfoot tr {
            background-color: #ecfdf5;
            font-weight: 700;
            color: #064e3b;
        }

        table.rekap-table tfoot td {
            border-top: 2px solid #059669;
            border-bottom: 2px solid #059669;
        }

        .badge-status {
            display: inline-block;
            padding: 2px 7px;
            border-radius: 4px;
            font-size: 7.5pt;
            font-weight: 700;
            text-align: center;
        }

        .badge-aktif {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-perawatan {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-pasif {
            background: #fee2e2;
            color: #991b1b;
        }

        /* SECTION DISTRIBUSI & TANDA TANGAN */
        .bottom-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-top: 15px;
            gap: 30px;
            page-break-inside: avoid;
        }

        .breakdown-box {
            flex: 1.2;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px 16px;
        }

        .breakdown-title {
            font-size: 9pt;
            font-weight: 700;
            color: #145f3c;
            margin-bottom: 8px;
            text-transform: uppercase;
        }

        .breakdown-list {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 6px 14px;
            font-size: 8.5pt;
        }

        .breakdown-item {
            display: flex;
            justify-content: space-between;
            border-bottom: 1px dashed #e2e8f0;
            padding-bottom: 3px;
        }

        .ttd-box {
            width: 280px;
            text-align: center;
            font-size: 9.5pt;
        }

        .ttd-date {
            margin-bottom: 6px;
        }

        .ttd-role {
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 65px;
        }

        .ttd-name {
            font-weight: 700;
            text-decoration: underline;
            color: #0f172a;
        }

        .ttd-nip {
            font-size: 8.5pt;
            color: #64748b;
        }

        /* PRINT STYLES */
        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }

            .print-toolbar {
                display: none !important;
            }

            .sheet {
                max-width: 100%;
                border: none;
                box-shadow: none;
                padding: 0;
                border-radius: 0;
            }

            @page {
                size: A4 landscape;
                margin: 10mm 12mm 10mm 12mm;
            }

            table.rekap-table th {
                background-color: #145f3c !important;
                color: #ffffff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            table.rekap-table tfoot tr {
                background-color: #ecfdf5 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .badge-status {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .rekap-card {
                background: #f8fafc !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>

    <!-- TOOLBAR INTERAKTIF -->
    <div class="print-toolbar">
        <div class="toolbar-info">
            <svg style="width:24px;height:24px;color:#059669;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            <div>
                <div class="toolbar-title">Pratinjau Cetak Rekapitulasi Taman</div>
                <div class="toolbar-desc">Siap dicetak atau disimpan sebagai PDF (Ukuran standar: A4 Landscape)</div>
            </div>
        </div>
        <div class="toolbar-buttons">
            <button onclick="window.print()" class="btn btn-print">
                <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                <span>Cetak / Simpan PDF</span>
            </button>
            <a href="export_rekap.php" class="btn btn-excel">
                <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <span>Unduh Excel</span>
            </a>
            <a href="parks.php" class="btn btn-back">
                <span>← Kembali ke Kelola Taman</span>
            </a>
        </div>
    </div>

    <!-- LEMBAR DOKUMEN CETAK -->
    <div class="sheet">

        <!-- KOP SURAT RESMI -->
        <div class="kop-header">
            <img src="../../images/logo-pemkot-batu.png" alt="Logo Pemkot Batu" class="kop-logo" onerror="this.style.display='none'">
            <div class="kop-text">
                <div class="kop-instansi-atas">Pemerintah Kota Batu</div>
                <div class="kop-instansi-utama">Dinas Lingkungan Hidup</div>
                <div class="kop-alamat">
                    Balai Kota Among Tani Gedung B Lantai 2, Jl. Panglima Sudirman No. 507, Kec. Batu, Kota Batu, Jawa Timur 65313<br>
                    Pos-el: dlh@batukota.go.id | Laman Resmi: dlh.batukota.go.id
                </div>
            </div>
            <img src="../../images/logo-dlh.png" alt="Logo DLH" class="kop-logo" onerror="this.style.display='none'">
        </div>

        <!-- JUDUL LAPORAN -->
        <div class="report-title-box">
            <h1 class="report-title">Laporan Rekapitulasi Data Taman Kota & Ruang Terbuka Hijau (RTH)</h1>
            <p class="report-subtitle">Dinas Lingkungan Hidup Kota Batu — Periode Tahun <?= date('Y') ?></p>
        </div>

        <!-- KARTU MINI REKAP STATISTIK -->
        <div class="rekap-stats-grid">
            <div class="rekap-card">
                <div class="rekap-card-label">Total Taman</div>
                <div class="rekap-card-val"><?= $totalParks ?></div>
                <div class="rekap-card-sub">Titik Ruang Publik</div>
            </div>
            <div class="rekap-card">
                <div class="rekap-card-label">Total Luas RTH</div>
                <div class="rekap-card-val"><?= number_format($totalArea, 0, ',', '.') ?> <span style="font-size:9pt;">m²</span></div>
                <div class="rekap-card-sub">± <?= number_format($totalArea / 10000, 2, ',', '.') ?> Hektar</div>
            </div>
            <div class="rekap-card">
                <div class="rekap-card-label">Tenaga Kerja</div>
                <div class="rekap-card-val"><?= $totalEmployees ?> <span style="font-size:9pt;">Orang</span></div>
                <div class="rekap-card-sub">Petugas Pemeliharaan</div>
            </div>
            <div class="rekap-card">
                <div class="rekap-card-label">Status Operasional</div>
                <div class="rekap-card-val" style="font-size:11pt; margin-top:5px; color:#0f172a;">
                    <?= $statusCounts['Aktif'] ?? 0 ?> Aktif • <?= $statusCounts['Dalam Perawatan'] ?? 0 ?> Rawat
                </div>
                <div class="rekap-card-sub"><?= $statusCounts['Pasif'] ?? 0 ?> Pasif</div>
            </div>
        </div>

        <!-- TABEL UTAMA REKAPITULASI -->
        <table class="rekap-table">
            <thead>
                <tr>
                    <th style="width: 35px;">No.</th>
                    <th>Nama Taman</th>
                    <th style="width: 120px;">Kategori</th>
                    <th>Alamat / Lokasi</th>
                    <th style="width: 90px;">Jam Buka</th>
                    <th style="width: 85px;">Luas (m²)</th>
                    <th style="width: 70px;">Tenaga Kerja</th>
                    <th style="width: 95px;">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($parks)): ?>
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 20px; color: #64748b;">
                            Belum ada data taman yang tersimpan.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php $no = 1; foreach ($parks as $p): ?>
                        @php
                            $statusCls = match($p['status'] ?? '') {
                                'Aktif'           => 'badge-aktif',
                                'Dalam Perawatan' => 'badge-perawatan',
                                default           => 'badge-pasif',
                            };
                        @endphp
                        <tr>
                            <td style="text-align: center;"><?= $no++ ?></td>
                            <td><strong><?= e($p['name']) ?></strong></td>
                            <td style="text-align: center;"><?= e($p['category']) ?></td>
                            <td><?= e($p['address']) ?></td>
                            <td style="text-align: center; font-size: 8pt;"><?= e($p['opening_hours']) ?></td>
                            <td style="text-align: right; font-weight: 600;"><?= number_format((float)($p['area'] ?? 0), 2, ',', '.') ?></td>
                            <td style="text-align: center;"><?= (int)($p['employee_count'] ?? 0) ?> orang</td>
                            <td style="text-align: center;">
                                <?php
                                    $st = $p['status'] ?? 'Aktif';
                                    $badgeCls = ($st === 'Aktif') ? 'badge-aktif' : (($st === 'Dalam Perawatan') ? 'badge-perawatan' : 'badge-pasif');
                                ?>
                                <span class="badge-status <?= $badgeCls ?>"><?= e($st) ?></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" style="text-align: right; padding-right: 12px; font-weight: 800;">TOTAL KESELURUHAN</td>
                    <td style="text-align: right; font-weight: 800; font-size: 9.5pt;"><?= number_format($totalArea, 2, ',', '.') ?></td>
                    <td style="text-align: center; font-weight: 800;"><?= $totalEmployees ?> orang</td>
                    <td style="text-align: center; font-size: 8pt;"><?= $totalParks ?> Taman Terdata</td>
                </tr>
            </tfoot>
        </table>

        <!-- BAGIAN BAWAH: RINGKASAN & TANDA TANGAN -->
        <div class="bottom-section">
            <div class="breakdown-box">
                <div class="breakdown-title">Rincian Klasifikasi & Distribusi</div>
                <div class="breakdown-list">
                    <div>
                        <strong style="color:#64748b; font-size:8pt; text-transform:uppercase;">Berdasarkan Kategori:</strong>
                        <?php foreach ($categoryCounts as $cat => $jml): ?>
                            <div class="breakdown-item" style="margin-top:3px;">
                                <span><?= e($cat) ?></span>
                                <strong><?= $jml ?> taman</strong>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div>
                        <strong style="color:#64748b; font-size:8pt; text-transform:uppercase;">Berdasarkan Status:</strong>
                        <?php foreach ($statusCounts as $st => $jml): ?>
                            <div class="breakdown-item" style="margin-top:3px;">
                                <span><?= e($st) ?></span>
                                <strong><?= $jml ?> taman</strong>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- TANDA TANGAN RESMI PEJABAT -->
            <div class="ttd-box">
                <div class="ttd-date">Batu, <?= $tanggalHariIni ?></div>
                <div class="ttd-role">
                    Kepala Bidang Tata Lingkungan<br>
                    Dinas Lingkungan Hidup Kota Batu
                </div>
                <div class="ttd-name">Ir. H. SUGENG PRAMONO, M.Si.</div>
                <div class="ttd-nip">NIP. 19710815 199703 1 005</div>
            </div>
        </div>

    </div>

</body>
</html>
