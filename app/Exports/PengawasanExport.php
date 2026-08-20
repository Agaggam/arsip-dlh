<?php

namespace App\Exports;

use App\Models\Pengawasan;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class PengawasanExport implements FromCollection, WithStyles, WithTitle, WithCustomStartCell
{
    protected ?Pengawasan $singlePengawasan;
    protected array $filters;

    public function __construct(array $filters = [], ?Pengawasan $singlePengawasan = null)
    {
        $this->filters          = $filters;
        $this->singlePengawasan = $singlePengawasan;
    }

    public function collection(): Collection
    {
        // We will build the entire sheet custom in styles()
        return collect([]);
    }

    public function startCell(): string
    {
        return 'A1';
    }

    public function title(): string
    {
        return 'Data Pengawasan';
    }

    public function styles(Worksheet $sheet): array
    {
        // Enable gridlines
        $sheet->setShowGridlines(true);

        // Fetch records
        if ($this->singlePengawasan) {
            $records = $this->singlePengawasan->batch_id
                ? Pengawasan::where('batch_id', $this->singlePengawasan->batch_id)->orderBy('waktu_pengawasan')->get()
                : collect([$this->singlePengawasan]);
            $tahun = $this->singlePengawasan->tahun;
            $namaPengawas = $this->singlePengawasan->nama_pengawas;
        } else {
            $tahun = $this->filters['tahun'] ?? date('Y');
            $records = Pengawasan::filter($this->filters)
                ->orderBy('kecamatan')
                ->orderBy('jenis_pengawasan')
                ->orderBy('waktu_pengawasan')
                ->get();
            $namaPengawas = $records->first()?->nama_pengawas ?? 'NAWANG WIRAWAN, S.Pt';
        }

        // Column width setup
        $sheet->getColumnDimension('A')->setWidth(5.5);
        $sheet->getColumnDimension('B')->setWidth(26);
        $sheet->getColumnDimension('C')->setWidth(13);
        $sheet->getColumnDimension('D')->setWidth(14);
        
        // 16 columns for hasil pengawasan (E to T)
        foreach (range('E', 'T') as $col) {
            $sheet->getColumnDimension($col)->setWidth(7.5);
        }
        
        // Summary & Keterangan (U to X)
        $sheet->getColumnDimension('U')->setWidth(6.5);
        $sheet->getColumnDimension('V')->setWidth(6.5);
        $sheet->getColumnDimension('W')->setWidth(6.5);
        $sheet->getColumnDimension('X')->setWidth(16);

        // 1. JUDUL UTAMA (Row 1 & 2)
        $sheet->mergeCells('A1:X1');
        $sheet->setCellValue('A1', 'PENGAWASAN PELAKU USAHA DAN/ATAU KEGIATAN TAHUN ' . $tahun);
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'name' => 'Calibri'],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(24);

        $sheet->mergeCells('A2:X2');
        $sheet->setCellValue('A2', 'PENGAWAS : ' . strtoupper($namaPengawas));
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 11, 'name' => 'Calibri'],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(20);

        // 2. KELOMPOKKAN DATA PER KECAMATAN & JENIS
        // Grouping: Kecamatan -> Jenis Pengawasan
        $grouped = $records->groupBy('kecamatan')->map(fn($items) => $items->groupBy('jenis_pengawasan'));

        $currentRow = 4;
        $allBorderRanges = [];

        $subHeadings = [
            'E' => "IZIN\nLINGK",
            'F' => "Wajib\nubah\ndok",
            'G' => "OSS\nrba",
            'H' => "LAP",
            'I' => "grea\nsetr\nap",
            'J' => "IPAL",
            'K' => "Kett\nTeknis",
            'L' => "Pantau\nIPAL",
            'M' => "IPLC/\nperte\nk",
            'N' => "Panta\nu\nudara",
            'O' => "INV LB3",
            'P' => "TPSL\nB3",
            'Q' => "Kett\nTek\nnis",
            'R' => "Izin/Rin\ntek",
            'S' => "Sures/\nBiopori",
            'T' => "Pilah\nsampah",
        ];

        $hasilKeys = array_keys(Pengawasan::HASIL_COLUMNS);

        foreach ($grouped as $kecamatan => $jenisGroup) {
            foreach ($jenisGroup as $jenis => $items) {
                if ($items->isEmpty()) {
                    continue; // "tabel paling bawah ga di pake" -> skip empty sections
                }

                $jenisLabel = ($jenis === 'langsung') ? 'LANGSUNG' : 'TIDAK LANGSUNG';
                $sectionTitle = "PENGAWASAN {$jenisLabel} DI WILAYAH " . strtoupper($kecamatan);

                // Section Title Row
                $sheet->setCellValue("A{$currentRow}", $sectionTitle);
                $sheet->getStyle("A{$currentRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 10, 'name' => 'Calibri'],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension($currentRow)->setRowHeight(20);
                $currentRow++;

                // Table Header Row 1 ($hRow1) and Row 2 ($hRow2)
                $hRow1 = $currentRow;
                $hRow2 = $currentRow + 1;

                // Merge & Values for Main Headers
                $sheet->mergeCells("A{$hRow1}:A{$hRow2}");
                $sheet->setCellValue("A{$hRow1}", "NO");

                $sheet->mergeCells("B{$hRow1}:B{$hRow2}");
                $sheet->setCellValue("B{$hRow1}", "NAMA USAHA");

                $sheet->mergeCells("C{$hRow1}:C{$hRow2}");
                $sheet->setCellValue("C{$hRow1}", "SKALA\nUSAHA /\nDOK");

                $sheet->mergeCells("D{$hRow1}:D{$hRow2}");
                $sheet->setCellValue("D{$hRow1}", "WAKTU\nPENGAWAS\nAN");

                // HASIL PENGAWASAN (E to T)
                $sheet->mergeCells("E{$hRow1}:T{$hRow1}");
                $sheet->setCellValue("E{$hRow1}", "HASIL PENGAWASAN");

                // Sub-headers in Row 2
                foreach ($subHeadings as $col => $lbl) {
                    $sheet->setCellValue("{$col}{$hRow2}", $lbl);
                }

                // Summary headers (U to X)
                $sheet->mergeCells("U{$hRow1}:U{$hRow2}");
                $sheet->setCellValue("U{$hRow1}", "Jml v");

                $sheet->mergeCells("V{$hRow1}:V{$hRow2}");
                $sheet->setCellValue("V{$hRow1}", "Jml p");

                $sheet->mergeCells("W{$hRow1}:W{$hRow2}");
                $sheet->setCellValue("W{$hRow1}", "Jml x");

                $sheet->mergeCells("X{$hRow1}:X{$hRow2}");
                $sheet->setCellValue("X{$hRow1}", "Keter\nanga\nn");

                // Style Headers
                $sheet->getStyle("A{$hRow1}:X{$hRow2}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 8.5, 'name' => 'Calibri'],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E7E6E6']],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                        'wrapText'   => true,
                    ],
                ]);

                $sheet->getRowDimension($hRow1)->setRowHeight(18);
                $sheet->getRowDimension($hRow2)->setRowHeight(32);

                $dataStartRow = $hRow2 + 1;
                $rowIdx = $dataStartRow;
                $no = 1;

                // Data Rows
                foreach ($items as $item) {
                    $sheet->setCellValue("A{$rowIdx}", $no++);
                    $sheet->setCellValue("B{$rowIdx}", $item->nama_usaha);
                    $sheet->setCellValue("C{$rowIdx}", $item->skala_usaha);
                    $sheet->setCellValue("D{$rowIdx}", $item->waktu_pengawasan ? $item->waktu_pengawasan->format('n/j/Y') : '-');

                    // 16 hasil cols
                    foreach ($hasilKeys as $kIdx => $colKey) {
                        $excelCol = chr(ord('E') + $kIdx); // E, F, G, ... T
                        $val = strtoupper($item->{$colKey} ?? '-');
                        $sheet->setCellValue("{$excelCol}{$rowIdx}", $val);
                    }

                    $sheet->setCellValue("U{$rowIdx}", $item->jml_v);
                    $sheet->setCellValue("V{$rowIdx}", $item->jml_p);
                    $sheet->setCellValue("W{$rowIdx}", $item->jml_x);
                    $sheet->setCellValue("X{$rowIdx}", $item->keterangan ?? '');

                    // Style data row
                    $sheet->getStyle("A{$rowIdx}:X{$rowIdx}")->applyFromArray([
                        'font' => ['size' => 9, 'name' => 'Calibri'],
                        'alignment' => [
                            'vertical' => Alignment::VERTICAL_CENTER,
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ],
                    ]);
                    $sheet->getStyle("B{$rowIdx}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                    $sheet->getRowDimension($rowIdx)->setRowHeight(20);
                    $rowIdx++;
                }

                $dataEndRow = $rowIdx - 1;
                $allBorderRanges[] = "A{$hRow1}:X{$dataEndRow}";

                $currentRow = $rowIdx + 2; // Spacing after section
            }
        }

        // Apply borders for all tables
        foreach ($allBorderRanges as $range) {
            $sheet->getStyle($range)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['rgb' => '000000'],
                    ],
                ],
            ]);
        }

        // 3. KETERANGAN BOX (Left) & SIGNATURE (Right)
        $signRowStart = $currentRow;

        // Keterangan Legend Box (Left)
        $sheet->setCellValue("B{$signRowStart}", "KETERANGAN");
        $sheet->setCellValue("D{$signRowStart}", "v");
        $sheet->setCellValue("E{$signRowStart}", "ada/baik");

        $r2 = $signRowStart + 1;
        $sheet->setCellValue("D{$r2}", "p");
        $sheet->setCellValue("E{$r2}", "proses");

        $r3 = $signRowStart + 2;
        $sheet->setCellValue("D{$r3}", "x");
        $sheet->setCellValue("E{$r3}", "tidak ada sama sekali");

        $sheet->getStyle("B{$signRowStart}:E{$r3}")->applyFromArray([
            'font' => ['size' => 9, 'name' => 'Calibri'],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getStyle("B{$signRowStart}")->getFont()->setBold(true);
        $sheet->getStyle("D{$signRowStart}:D{$r3}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Signature on Right (Columns S to W)
        $bulanTahun = Carbon::now()->translatedFormat('F Y');
        $sheet->mergeCells("S{$signRowStart}:W{$signRowStart}");
        $sheet->setCellValue("S{$signRowStart}", "Batu,   " . $bulanTahun);
        $sheet->getStyle("S{$signRowStart}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $rSignTitle = $signRowStart + 1;
        $sheet->mergeCells("R{$rSignTitle}:X{$rSignTitle}");
        $sheet->setCellValue("R{$rSignTitle}", "PENGAWAS LINGKUNGAN HIDUP AHLI MUDA");
        $sheet->getStyle("R{$rSignTitle}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 9, 'name' => 'Calibri'],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $rSignName = $signRowStart + 5;
        $sheet->mergeCells("R{$rSignName}:X{$rSignName}");
        $sheet->setCellValue("R{$rSignName}", strtoupper($namaPengawas));
        $sheet->getStyle("R{$rSignName}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 10, 'name' => 'Calibri', 'underline' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        return [];
    }
}
