<?php

namespace App\Exports;

use App\Models\SurveyHarga;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class SurveyHargaExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    ShouldAutoSize,
    WithTitle,
    WithDrawings,
    WithCustomStartCell
{
    protected ?SurveyHarga $singleSurvey;
    protected array $filters;
    private int $rowNumber = 0;

    public function __construct(array $filters = [], ?SurveyHarga $singleSurvey = null)
    {
        $this->filters      = $filters;
        $this->singleSurvey = $singleSurvey;
    }

    public function collection(): Collection
    {
        if ($this->singleSurvey) {
            return collect([$this->singleSurvey->load('department', 'user')]);
        }

        return SurveyHarga::with(['department', 'user'])
            ->filter($this->filters)
            ->latest()
            ->get();
    }

    public function title(): string
    {
        return 'Usulan Harga';
    }

    public function startCell(): string
    {
        return 'A6';
    }

    public function drawings()
    {
        $drawings = [];
        $logoPath = file_exists(public_path('images/logo-pemkot-batu.png')) 
            ? public_path('images/logo-pemkot-batu.png') 
            : public_path('images/logopemkot.png');

        if (file_exists($logoPath)) {
            $drawing = new Drawing();
            $drawing->setName('Logo Pemkot');
            $drawing->setDescription('Logo Pemerintah Kota Batu');
            $drawing->setPath($logoPath);
            $drawing->setHeight(60);
            $drawing->setCoordinates('A1');
            $drawing->setOffsetX(10);
            $drawing->setOffsetY(5);
            $drawings[] = $drawing;
        }

        return $drawings;
    }

    public function headings(): array
    {
        return [
            'NO',
            'BIDANG / DEPARTEMEN',
            'NAMA BARANG / JASA',
            'KELOMPOK',
            'SPESIFIKASI',
            'SATUAN',
            'TOKO 1 & HARGA',
            'TOKO 2 & HARGA',
            'TOKO 3 & HARGA',
            'HARGA RATA-RATA',
            'REKOMENDASI HARGA',
            'PENGUSUL',
            'TANGGAL USULAN',
        ];
    }

    public function map($row): array
    {
        $this->rowNumber++;

        $toko1 = $row->nama_toko_1 ? "{$row->nama_toko_1} (Rp " . number_format($row->harga_toko_1, 0, ',', '.') . ")" : '-';
        $toko2 = $row->nama_toko_2 ? "{$row->nama_toko_2} (Rp " . number_format($row->harga_toko_2, 0, ',', '.') . ")" : '-';
        $toko3 = $row->nama_toko_3 ? "{$row->nama_toko_3} (Rp " . number_format($row->harga_toko_3, 0, ',', '.') . ")" : '-';

        return [
            $this->rowNumber,
            $row->department?->name ?? 'System',
            $row->judul,
            strtoupper($row->kelompok),
            $row->spesifikasi ?? '-',
            $row->satuan,
            $toko1,
            $toko2,
            $toko3,
            $row->harga_rata_rata ? 'Rp ' . number_format($row->harga_rata_rata, 0, ',', '.') : '-',
            $row->rekomendasi_harga ? 'Rp ' . number_format($row->rekomendasi_harga, 0, ',', '.') : '-',
            $row->user?->name ?? '-',
            $row->created_at ? $row->created_at->format('d/m/Y') : '-',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        // Header Text
        $sheet->mergeCells('C1:M1');
        $sheet->setCellValue('C1', 'PEMERINTAH KOTA BATU');
        $sheet->getStyle('C1')->getFont()->setSize(14)->setBold(true);

        $sheet->mergeCells('C2:M2');
        $sheet->setCellValue('C2', 'DINAS LINGKUNGAN HIDUP');
        $sheet->getStyle('C2')->getFont()->setSize(12)->setBold(true);

        $sheet->mergeCells('C3:M3');
        $sheet->setCellValue('C3', 'REKAPITULASI SURVEI USULAN HARGA (SSH / SBU / ASB)');
        $sheet->getStyle('C3')->getFont()->setSize(10)->setBold(true);

        $sheet->mergeCells('C4:M4');
        $sheet->setCellValue('C4', 'Tanggal Ekspor: ' . date('d F Y, H:i') . ' WIB');
        $sheet->getStyle('C4')->getFont()->setSize(9)->setItalic(true);

        // Heading Styles (Row 6)
        $sheet->getStyle('A6:M6')->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size'  => 10,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1E293B'], // Slate 800
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ],
        ]);

        $sheet->getRowDimension(6)->setRowHeight(28);

        // Border styling
        $highestRow = $sheet->getHighestRow();
        if ($highestRow >= 6) {
            $sheet->getStyle("A6:M{$highestRow}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['rgb' => 'CBD5E1'],
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);

            // Center NO & Dates
            $sheet->getStyle("A7:A{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("D7:D{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("F7:F{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("M7:M{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        return [];
    }
}
