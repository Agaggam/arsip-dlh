<?php

namespace App\Services;

use App\Models\Pegawai;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class PegawaiExcelService
{
    /**
     * Import pegawai dari file Excel.
     * Mendukung format multi-sheet (BKN / DLH Kota Batu) maupun single-sheet standar.
     *
     * @param string $filePath Path file fisik Excel
     * @param string $duplicateStrategy 'update' atau 'skip'
     * @return array
     */
    public function import(string $filePath, string $duplicateStrategy = 'update'): array
    {
        if (!file_exists($filePath)) {
            throw new \Exception("File Excel tidak ditemukan di lokasi: {$filePath}");
        }

        $reader = IOFactory::createReaderForFile($filePath);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($filePath);

        $sheetNames = $spreadsheet->getSheetNames();
        $totalSheets = count($sheetNames);

        // Cek apakah file ini adalah format Multi-Sheet BKN DLH Kota Batu
        $masterSheetIndex = $this->findMasterSheetIndex($sheetNames);

        if ($masterSheetIndex !== null) {
            return $this->importBknMultiSheet($spreadsheet, $masterSheetIndex, $duplicateStrategy);
        }

        // Format standar (Single-sheet atau seluruh sheet dibaca sekuensial)
        return $this->importGenericSheets($spreadsheet, $duplicateStrategy);
    }

    /**
     * Impor format Multi-Sheet BKN DLH (seperti PNS LENGKAP 2026.xlsx).
     */
    protected function importBknMultiSheet(Spreadsheet $spreadsheet, int $masterIndex, string $duplicateStrategy): array
    {
        // 1. Bangun peta NIP -> Unit Kerja dari sheet bidang (Sheet 1 s/d Sheet N)
        $nipToUnit = [];
        $sheetCount = $spreadsheet->getSheetCount();

        for ($i = 0; $i < $sheetCount; $i++) {
            if ($i === $masterIndex) continue;

            $sheet = $spreadsheet->getSheet($i);
            $sheetTitle = trim($sheet->getTitle());
            $unitName = $this->mapSheetTitleToUnitKerja($sheetTitle);

            $highestRow = $sheet->getHighestRow();
            for ($r = 2; $r <= $highestRow; $r++) {
                $rawNip = $sheet->getCell('C' . $r)->getValue();
                $nip = trim((string)$rawNip);
                if ($nip && !isset($nipToUnit[$nip])) {
                    $nipToUnit[$nip] = $unitName;
                }
            }
        }

        // 2. Baca Sheet Master untuk mengambil seluruh data pegawai secara unik
        $masterSheet = $spreadsheet->getSheet($masterIndex);
        $highestRow = $masterSheet->getHighestRow();

        $imported = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            for ($r = 2; $r <= $highestRow; $r++) {
                $nama = trim((string)$masterSheet->getCell('B' . $r)->getValue());
                $nipRaw = trim((string)$masterSheet->getCell('C' . $r)->getValue());

                // Jika baris kosong, lewati
                if (!$nama && !$nipRaw) {
                    continue;
                }

                $nip = $nipRaw ? preg_replace('/[^0-9]/', '', $nipRaw) : null;

                // Kategori
                $ketRaw = trim((string)$masterSheet->getCell('L' . $r)->getValue());
                $kategori = $this->normalizeKategori($ketRaw);

                // Unit Kerja dari peta sheet bidang
                $unitKerja = $nip && isset($nipToUnit[$nip])
                    ? $nipToUnit[$nip]
                    : 'Dinas Lingkungan Hidup';

                // Pangkat / Golongan
                $pangkat = trim((string)$masterSheet->getCell('F' . $r)->getValue()) ?: null;

                // Jabatan
                $jabatan = trim((string)$masterSheet->getCell('J' . $r)->getValue()) ?: 'Staf';

                // Pendidikan Terakhir
                $pendidikan = trim((string)$masterSheet->getCell('E' . $r)->getValue()) ?: null;

                // TMT SK (Konversi tanggal Excel serial)
                $tmtRaw = $masterSheet->getCell('G' . $r)->getValue();
                $tmtSk = $this->parseExcelDate($tmtRaw);

                // Jenis Kelamin otomatis dari digit ke-15 NIP (Standar BKN)
                $jenisKelamin = 'L';
                if ($nip && strlen($nip) === 18) {
                    $digit15 = substr($nip, 14, 1);
                    $jenisKelamin = ($digit15 === '2') ? 'P' : 'L';
                }

                // Jam kerja mingguan
                $jamKerja = ($kategori === 'P3K Paruh Waktu') ? 20.0 : 37.5;

                // Masa kontrak
                $masaKontrak = ($kategori === 'ASN (PNS)') ? 'Permanen' : 'Kontrak';

                $pegawaiData = [
                    'nip_nrk'             => $nip ?: null,
                    'nama_lengkap'        => $nama,
                    'jenis_kelamin'       => $jenisKelamin,
                    'kategori'            => $kategori,
                    'pangkat_golongan'    => $pangkat,
                    'jabatan'             => $jabatan,
                    'unit_kerja'          => $unitKerja,
                    'tmt_sk'              => $tmtSk,
                    'masa_kontrak'        => $masaKontrak,
                    'jam_kerja_mingguan'  => $jamKerja,
                    'pendidikan_terakhir' => $pendidikan,
                    'status'              => 'Aktif',
                ];

                // Cek apakah pegawai sudah ada di database berdasarkan NIP
                $existing = null;
                if ($nip) {
                    $existing = Pegawai::where('nip_nrk', $nip)->first();
                }

                if ($existing) {
                    if ($duplicateStrategy === 'update') {
                        $existing->update($pegawaiData);
                        $updated++;
                    } else {
                        $skipped++;
                    }
                } else {
                    Pegawai::create($pegawaiData);
                    $imported++;
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw new \Exception("Gagal memproses data pada baris {$r}: " . $e->getMessage());
        }

        return [
            'mode'      => 'multi_sheet_bkn',
            'imported'  => $imported,
            'updated'   => $updated,
            'skipped'   => $skipped,
            'total_rows'=> $imported + $updated + $skipped,
            'errors'    => $errors,
        ];
    }

    /**
     * Impor format generik / fleksibel dari seluruh sheet.
     */
    protected function importGenericSheets(Spreadsheet $spreadsheet, string $duplicateStrategy): array
    {
        $imported = 0;
        $updated = 0;
        $skipped = 0;

        DB::beginTransaction();
        try {
            foreach ($spreadsheet->getAllSheets() as $sheet) {
                $highestRow = $sheet->getHighestRow();
                $highestCol = $sheet->getHighestColumn();

                // Cari baris header
                $headerRow = 1;
                $colMap = [];

                for ($r = 1; $r <= min(10, $highestRow); $r++) {
                    $testMap = [];
                    for ($c = 'A'; $c <= $highestCol; $c++) {
                        $val = strtolower(trim((string)$sheet->getCell($c . $r)->getValue()));
                        if (str_contains($val, 'nama')) $testMap['nama_lengkap'] = $c;
                        if (str_contains($val, 'nik')) $testMap['nik'] = $c;
                        if (str_contains($val, 'nip') || str_contains($val, 'nrk')) $testMap['nip_nrk'] = $c;
                        if (str_contains($val, 'jabatan')) $testMap['jabatan'] = $c;
                        if (str_contains($val, 'unit') || str_contains($val, 'bidang') || str_contains($val, 'instansi')) $testMap['unit_kerja'] = $c;
                        if (str_contains($val, 'kategori') || str_contains($val, 'keterangan')) $testMap['kategori'] = $c;
                        if (str_contains($val, 'pangkat') || str_contains($val, 'golongan')) $testMap['pangkat_golongan'] = $c;
                        if (str_contains($val, 'kelamin') || $val === 'jk') $testMap['jenis_kelamin'] = $c;
                        if (str_contains($val, 'tmt')) $testMap['tmt_sk'] = $c;
                        if (str_contains($val, 'pendidikan')) $testMap['pendidikan_terakhir'] = $c;
                        if (str_contains($val, 'hp') || str_contains($val, 'telepon')) $testMap['no_hp'] = $c;
                        if (str_contains($val, 'email')) $testMap['email'] = $c;
                        if (str_contains($val, 'status')) $testMap['status'] = $c;
                    }

                    if (isset($testMap['nama_lengkap']) && (isset($testMap['nip_nrk']) || isset($testMap['nik']))) {
                        $headerRow = $r;
                        $colMap = $testMap;
                        break;
                    }
                }

                if (empty($colMap['nama_lengkap'])) {
                    continue; // Bukan sheet data pegawai
                }

                for ($r = $headerRow + 1; $r <= $highestRow; $r++) {
                    $nama = trim((string)$sheet->getCell($colMap['nama_lengkap'] . $r)->getValue());
                    if (!$nama) continue;

                    $nip = isset($colMap['nip_nrk']) ? trim((string)$sheet->getCell($colMap['nip_nrk'] . $r)->getValue()) : null;
                    $nik = isset($colMap['nik']) ? preg_replace('/[^0-9]/', '', (string)$sheet->getCell($colMap['nik'] . $r)->getValue()) : null;

                    $data = [
                        'nama_lengkap'        => $nama,
                        'nik'                 => $nik ?: null,
                        'nip_nrk'             => $nip ?: null,
                        'jabatan'             => isset($colMap['jabatan']) ? trim((string)$sheet->getCell($colMap['jabatan'] . $r)->getValue()) : 'Staf',
                        'unit_kerja'          => isset($colMap['unit_kerja']) ? trim((string)$sheet->getCell($colMap['unit_kerja'] . $r)->getValue()) : $sheet->getTitle(),
                        'kategori'            => isset($colMap['kategori']) ? $this->normalizeKategori((string)$sheet->getCell($colMap['kategori'] . $r)->getValue()) : 'ASN (PNS)',
                        'pangkat_golongan'    => isset($colMap['pangkat_golongan']) ? trim((string)$sheet->getCell($colMap['pangkat_golongan'] . $r)->getValue()) : null,
                        'jenis_kelamin'       => isset($colMap['jenis_kelamin']) ? (strtoupper(substr(trim((string)$sheet->getCell($colMap['jenis_kelamin'] . $r)->getValue()), 0, 1)) === 'P' ? 'P' : 'L') : 'L',
                        'pendidikan_terakhir' => isset($colMap['pendidikan_terakhir']) ? trim((string)$sheet->getCell($colMap['pendidikan_terakhir'] . $r)->getValue()) : null,
                        'tmt_sk'              => isset($colMap['tmt_sk']) ? $this->parseExcelDate($sheet->getCell($colMap['tmt_sk'] . $r)->getValue()) : null,
                        'status'              => isset($colMap['status']) ? (in_array(trim((string)$sheet->getCell($colMap['status'] . $r)->getValue()), ['Aktif', 'Cuti', 'Tugas Belajar', 'Non-Aktif']) ? trim((string)$sheet->getCell($colMap['status'] . $r)->getValue()) : 'Aktif') : 'Aktif',
                        'jam_kerja_mingguan'  => 37.5,
                        'masa_kontrak'        => 'Permanen',
                    ];

                    // Deteksi duplikat
                    $existing = null;
                    if ($data['nip_nrk']) {
                        $existing = Pegawai::where('nip_nrk', $data['nip_nrk'])->first();
                    } elseif ($data['nik']) {
                        $existing = Pegawai::where('nik', $data['nik'])->first();
                    }

                    if ($existing) {
                        if ($duplicateStrategy === 'update') {
                            $existing->update($data);
                            $updated++;
                        } else {
                            $skipped++;
                        }
                    } else {
                        Pegawai::create($data);
                        $imported++;
                    }
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw new \Exception("Gagal memproses file Excel: " . $e->getMessage());
        }

        return [
            'mode'      => 'generic',
            'imported'  => $imported,
            'updated'   => $updated,
            'skipped'   => $skipped,
            'total_rows'=> $imported + $updated + $skipped,
        ];
    }

    /**
     * Cari index sheet master yang memuat seluruh data.
     */
    protected function findMasterSheetIndex(array $sheetNames): ?int
    {
        foreach ($sheetNames as $idx => $name) {
            $n = strtolower(trim($name));
            if (str_contains($n, 'asn') || str_contains($n, 'master') || str_contains($n, 'semua') || str_contains($n, 'rekap')) {
                return $idx;
            }
        }
        return null;
    }

    /**
     * Petakan judul sheet menjadi nama Unit Kerja DLH resmi.
     */
    protected function mapSheetTitleToUnitKerja(string $sheetTitle): string
    {
        $n = strtolower(trim($sheetTitle));

        if (str_contains($n, 'bidang 1') || str_contains($n, 'tata lingkungan')) {
            return 'Bidang 1 (Tata Lingkungan)';
        }
        if (str_contains($n, 'bidang 2') || str_contains($n, 'pencemaran') || str_contains($n, 'pengawasan')) {
            return 'Bidang 2 (Pengendalian Pencemaran & Pengawasan)';
        }
        if (str_contains($n, 'uptd')) {
            return 'UPTD Pengelolaan Sampah';
        }
        if (str_contains($n, 'bidang 3') || str_contains($n, 'sampah') || str_contains($n, 'limbah')) {
            return 'Bidang 3 (Pengelolaan Sampah & Limbah)';
        }
        if (str_contains($n, 'sekretariat') || str_contains($n, 'sekreatriat')) {
            return 'Sekretariat';
        }
        if (str_contains($n, 'fungsional')) {
            return 'Jabatan Fungsional';
        }

        return $sheetTitle;
    }

    /**
     * Normalisasi string kategori menjadi enum sistem.
     */
    protected function normalizeKategori(string $raw): string
    {
        $val = strtolower(trim($raw));
        if (str_contains($val, 'paruh')) {
            return 'P3K Paruh Waktu';
        }
        if (str_contains($val, 'p3k') || str_contains($val, 'pppk')) {
            return 'P3K Penuh Waktu';
        }
        if (str_contains($val, 'pns') || str_contains($val, 'asn')) {
            return 'ASN (PNS)';
        }
        if (str_contains($val, 'bsn') || str_contains($val, 'honor') || str_contains($val, 'non')) {
            return 'BSN / Non-ASN';
        }
        return 'ASN (PNS)';
    }

    /**
     * Konversi nilai tanggal Excel ke format YYYY-MM-DD.
     */
    protected function parseExcelDate($raw): ?string
    {
        if ($raw === null || $raw === '') {
            return null;
        }

        if (is_numeric($raw) && $raw > 1000) {
            try {
                return ExcelDate::excelToDateTimeObject($raw)->format('Y-m-d');
            } catch (\Throwable $e) {
                return null;
            }
        }

        try {
            return Carbon::parse($raw)->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Generate template Excel untuk diunduh admin.
     */
    public function generateTemplate(): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Import Pegawai');

        $headers = [
            'A1' => 'NIK (16 Digit)',
            'B1' => 'NIP / NRK',
            'C1' => 'Nama Lengkap',
            'D1' => 'Jenis Kelamin (L/P)',
            'E1' => 'Kategori (ASN (PNS) / P3K Penuh Waktu / P3K Paruh Waktu / BSN / Non-ASN)',
            'F1' => 'Pangkat / Golongan',
            'G1' => 'Jabatan',
            'H1' => 'Unit Kerja',
            'I1' => 'TMT SK (YYYY-MM-DD)',
            'J1' => 'Masa Kontrak',
            'K1' => 'Pendidikan Terakhir',
            'L1' => 'No. HP',
            'M1' => 'Email',
            'N1' => 'Status (Aktif / Cuti / Tugas Belajar / Non-Aktif)',
        ];

        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }

        $sheet->getStyle('A1:N1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E293B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        // Contoh baris
        $sample = [
            'A2' => '3579011205850001',
            'B2' => '198505122010011002',
            'C2' => 'Budi Santoso, S.T.',
            'D2' => 'L',
            'E2' => 'ASN (PNS)',
            'F2' => 'III/b',
            'G2' => 'Pengendali Dampak Lingkungan Ahli Muda',
            'H2' => 'Bidang 1 (Tata Lingkungan)',
            'I2' => '2010-01-01',
            'J2' => 'Permanen',
            'K2' => 'S-1 Teknik Lingkungan',
            'L2' => '081234567890',
            'M2' => 'budi.santoso@batukota.go.id',
            'N2' => 'Aktif',
        ];

        foreach ($sample as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }

        foreach (range('A', 'N') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return $spreadsheet;
    }
}
