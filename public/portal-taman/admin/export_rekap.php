<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

session_start();

require_once '../config/database.php';
require_once '../config/functions.php';

requireAdmin();

// Load autoload composer untuk PhpSpreadsheet
$autoloadPath = dirname(dirname(dirname(__DIR__))) . '/vendor/autoload.php';
if (file_exists($autoloadPath)) {
    require_once $autoloadPath;
}

// Ambil seluruh data taman
$parks = getAll($conn, "SELECT * FROM parks ORDER BY name ASC");

// Ambil info kota
$city = getOne($conn, "SELECT * FROM city_info LIMIT 1");
$cityName = $city['city_name'] ?? 'Kota Batu';

// Hitung total dan statistik
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

$filename = 'Rekap_Taman_' . str_replace(' ', '_', $cityName) . '_' . date('Ymd_His');

// Jika PhpSpreadsheet tersedia, buat file .xlsx premium
if (class_exists(Spreadsheet::class)) {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Rekap Data Taman');

    // Tampilkan grid lines saat dibuka di Excel
    $sheet->setShowGridLines(true);

    // Header KOP Surat
    $sheet->mergeCells('A2:M2');
    $sheet->setCellValue('A2', 'PEMERINTAH KOTA BATU - DINAS LINGKUNGAN HIDUP');
    $sheet->getStyle('A2')->applyFromArray([
        'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '133827'], 'name' => 'Calibri'],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
    ]);

    $sheet->mergeCells('A3:M3');
    $sheet->setCellValue('A3', 'REKAPITULASI DATA TAMAN KOTA DAN RUANG TERBUKA HIJAU (RTH)');
    $sheet->getStyle('A3')->applyFromArray([
        'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '1A7A4E'], 'name' => 'Calibri'],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
    ]);

    $sheet->mergeCells('A4:M4');
    $sheet->setCellValue('A4', 'Tanggal Unduh: ' . date('d/m/Y H:i') . ' WIB | Total: ' . $totalParks . ' Taman | Luas Total: ' . number_format($totalArea, 2, ',', '.') . ' m²');
    $sheet->getStyle('A4')->applyFromArray([
        'font' => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '5E7368'], 'name' => 'Calibri'],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
    ]);

    // Logo Pemkot Batu & DLH jika file ada
    $logoBatuPath = dirname(dirname(dirname(__DIR__))) . '/public/images/logo-pemkot-batu.png';
    if (file_exists($logoBatuPath)) {
        $drawBatu = new Drawing();
        $drawBatu->setName('Logo Pemkot Batu');
        $drawBatu->setPath($logoBatuPath);
        $drawBatu->setHeight(55);
        $drawBatu->setCoordinates('B2');
        $drawBatu->setWorksheet($sheet);
    }

    $logoDlhPath = dirname(dirname(dirname(__DIR__))) . '/public/images/logo-dlh.png';
    if (file_exists($logoDlhPath)) {
        $drawDlh = new Drawing();
        $drawDlh->setName('Logo DLH');
        $drawDlh->setPath($logoDlhPath);
        $drawDlh->setHeight(55);
        $drawDlh->setCoordinates('L2');
        $drawDlh->setWorksheet($sheet);
    }

    // Header Tabel
    $headers = [
        'A6' => 'No.',
        'B6' => 'Nama Taman',
        'C6' => 'Kategori',
        'D6' => 'Status',
        'E6' => 'Alamat',
        'F6' => 'Jam Buka',
        'G6' => 'Luas (m²)',
        'H6' => 'Pegawai (Org)',
        'I6' => 'Jenis Tanaman',
        'J6' => 'Fasilitas',
        'K6' => 'Latitude',
        'L6' => 'Longitude',
        'M6' => 'Link Google Maps',
    ];

    foreach ($headers as $cell => $text) {
        $sheet->setCellValue($cell, $text);
    }

    // Style Header Tabel
    $sheet->getStyle('A6:M6')->applyFromArray([
        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10, 'name' => 'Calibri'],
        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '145F3C']],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '0D4229']]],
    ]);
    $sheet->getRowDimension(6)->setRowHeight(26);

    // Isi Baris Data
    $rowNum = 7;
    $no = 1;
    foreach ($parks as $p) {
        $sheet->setCellValue("A{$rowNum}", $no);
        $sheet->setCellValue("B{$rowNum}", $p['name']);
        $sheet->setCellValue("C{$rowNum}", $p['category']);
        $sheet->setCellValue("D{$rowNum}", $p['status']);
        $sheet->setCellValue("E{$rowNum}", $p['address']);
        $sheet->setCellValue("F{$rowNum}", $p['opening_hours']);
        $sheet->setCellValue("G{$rowNum}", (float)$p['area']);
        $sheet->setCellValue("H{$rowNum}", (int)$p['employee_count']);
        $sheet->setCellValue("I{$rowNum}", $p['plant_species'] ?? '-');
        $sheet->setCellValue("J{$rowNum}", $p['facilities'] ?? '-');
        $sheet->setCellValue("K{$rowNum}", $p['latitude'] ?? '-');
        $sheet->setCellValue("L{$rowNum}", $p['longitude'] ?? '-');
        $sheet->setCellValue("M{$rowNum}", $p['map_url'] ?? '-');

        // Alternating row color
        $bg = ($no % 2 === 0) ? 'F4FAF6' : 'FFFFFF';
        $sheet->getStyle("A{$rowNum}:M{$rowNum}")->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E0ECE3']]],
            'font' => ['size' => 9, 'name' => 'Calibri'],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ]);

        $sheet->getStyle("A{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("C{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("D{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("F{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("G{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("H{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("K{$rowNum}:L{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->getStyle("G{$rowNum}")->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle("H{$rowNum}")->getNumberFormat()->setFormatCode('#,##0');

        $sheet->getRowDimension($rowNum)->setRowHeight(20);
        $rowNum++;
        $no++;
    }

    $lastDataRow = $rowNum - 1;

    // BARIS TOTAL KESELURUHAN
    $sheet->mergeCells("A{$rowNum}:F{$rowNum}");
    $sheet->setCellValue("A{$rowNum}", 'TOTAL KESELURUHAN');
    $sheet->setCellValue("G{$rowNum}", "=SUM(G7:G{$lastDataRow})");
    $sheet->setCellValue("H{$rowNum}", "=SUM(H7:H{$lastDataRow})");
    $sheet->setCellValue("I{$rowNum}", "{$totalParks} Taman Terdata");
    $sheet->mergeCells("I{$rowNum}:M{$rowNum}");

    $sheet->getStyle("A{$rowNum}:M{$rowNum}")->applyFromArray([
        'font' => ['bold' => true, 'size' => 10, 'name' => 'Calibri', 'color' => ['rgb' => '064E3B']],
        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D1FAE5']],
        'borders' => [
            'top' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '10B981']],
            'bottom' => ['borderStyle' => Border::BORDER_DOUBLE, 'color' => ['rgb' => '047857']],
            'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'A7F3D0']],
        ],
        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
    ]);

    $sheet->getStyle("A{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    $sheet->getStyle("G{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    $sheet->getStyle("H{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle("G{$rowNum}")->getNumberFormat()->setFormatCode('#,##0.00');
    $sheet->getStyle("H{$rowNum}")->getNumberFormat()->setFormatCode('#,##0');
    $sheet->getRowDimension($rowNum)->setRowHeight(24);

    // KOTAK RINGKASAN REKAPITULASI DI BAWAH TABEL
    $summaryStart = $rowNum + 3;

    $sheet->setCellValue("B{$summaryStart}", "RINGKASAN REKAPITULASI TAMAN");
    $sheet->getStyle("B{$summaryStart}")->applyFromArray([
        'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '145F3C']],
    ]);

    $sRow = $summaryStart + 1;
    $sheet->setCellValue("B{$sRow}", "Total Taman");
    $sheet->setCellValue("C{$sRow}", $totalParks . " Taman");

    $sRow++;
    $sheet->setCellValue("B{$sRow}", "Total Luas Ruang Terbuka Hijau (RTH)");
    $sheet->setCellValue("C{$sRow}", number_format($totalArea, 2, ',', '.') . " m² (" . number_format($totalArea / 10000, 2, ',', '.') . " Ha)");

    $sRow++;
    $sheet->setCellValue("B{$sRow}", "Total Petugas / Tenaga Kerja");
    $sheet->setCellValue("C{$sRow}", $totalEmployees . " Orang");

    $sRow++;
    $sheet->setCellValue("B{$sRow}", "Distribusi Status");
    $statusText = [];
    foreach ($statusCounts as $st => $c) {
        $statusText[] = "{$st}: {$c}";
    }
    $sheet->setCellValue("C{$sRow}", implode(' | ', $statusText));

    $sRow++;
    $sheet->setCellValue("B{$sRow}", "Distribusi Kategori");
    $catText = [];
    foreach ($categoryCounts as $cat => $c) {
        $catText[] = "{$cat}: {$c}";
    }
    $sheet->setCellValue("C{$sRow}", implode(' | ', $catText));

    $sheet->getStyle("B" . ($summaryStart + 1) . ":C{$sRow}")->applyFromArray([
        'font' => ['size' => 9],
        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D0D7E2']]],
    ]);
    $sheet->getStyle("B" . ($summaryStart + 1) . ":B{$sRow}")->applyFromArray([
        'font' => ['bold' => true],
        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F0F4F2']],
    ]);

    // Auto-fit kolom
    foreach (range('A', 'M') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }
    $sheet->freezePane('A7');

    // Output Header Excel
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $filename . '.xlsx"');
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}

// Fallback jika PhpSpreadsheet tidak ada: Download CSV dengan UTF-8 BOM
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '.csv"');

$output = fopen('php://output', 'w');
// UTF-8 BOM agar terbaca rapi di Excel
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

fputcsv($output, ['REKAPITULASI DATA TAMAN KOTA - DINAS LINGKUNGAN HIDUP KOTA BATU']);
fputcsv($output, ['Tanggal Unduh: ' . date('d/m/Y H:i:s')]);
fputcsv($output, []);
fputcsv($output, ['No.', 'Nama Taman', 'Kategori', 'Status', 'Alamat', 'Jam Buka', 'Luas (m2)', 'Pegawai', 'Jenis Tanaman', 'Fasilitas', 'Latitude', 'Longitude', 'Google Maps']);

$no = 1;
foreach ($parks as $p) {
    fputcsv($output, [
        $no++,
        $p['name'],
        $p['category'],
        $p['status'],
        $p['address'],
        $p['opening_hours'],
        $p['area'],
        $p['employee_count'],
        $p['plant_species'],
        $p['facilities'],
        $p['latitude'],
        $p['longitude'],
        $p['map_url']
    ]);
}

fputcsv($output, []);
fputcsv($output, ['TOTAL KESELURUHAN', '', '', '', '', '', $totalArea, $totalEmployees, "{$totalParks} Taman"]);

fclose($output);
exit;
