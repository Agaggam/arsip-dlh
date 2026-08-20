<?php

namespace App\Exports;

use App\Models\Archive;
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

class ArchiveExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    ShouldAutoSize,
    WithTitle,
    WithDrawings,
    WithCustomStartCell
{
    protected ?Archive $singleArchive;
    protected array $filters;
    private int $rowNumber = 0;

    public function __construct(array $filters = [], ?Archive $singleArchive = null)
    {
        $this->filters       = $filters;
        $this->singleArchive = $singleArchive;
    }

    public function collection(): Collection
    {
        if ($this->singleArchive) {
            return collect([$this->singleArchive->load('category.department', 'user')]);
        }

        $query = Archive::with(['category.department', 'user']);

        if (!empty($this->filters['search'])) {
            $query->where('title', 'LIKE', '%' . $this->filters['search'] . '%');
        }
        if (!empty($this->filters['category_id'])) {
            $query->where('category_id', $this->filters['category_id']);
        }
        if (!empty($this->filters['department_id'])) {
            $query->whereHas('category', fn($q) => $q->where('department_id', $this->filters['department_id']));
        }
        if (!empty($this->filters['file_type'])) {
            $query->where('file_type', $this->filters['file_type']);
        }

        return $query->latest('archive_date')->get();
    }

    public function title(): string
    {
        return 'Daftar Arsip';
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
            'JUDUL ARSIP',
            'KATEGORI',
            'BIDANG / DEPARTEMEN',
            'TIPE BERKAS',
            'UKURAN',
            'TANGGAL ARSIP',
            'PENGUNGGAH',
            'TOTAL HITS',
        ];
    }

    public function map($row): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            $row->title,
            $row->category?->name ?? 'N/A',
            $row->category?->department?->name ?? 'UMUM / SISTEM',
            strtoupper($row->file_type),
            $row->file_size ?? '-',
            $row->archive_date ? \Carbon\Carbon::parse($row->archive_date)->format('d/m/Y H:i') : '-',
            $row->user?->name ?? 'System',
            $row->download_count ?? 0,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        // Header Title
        $sheet->mergeCells('C1:I1');
        $sheet->setCellValue('C1', 'PEMERINTAH KOTA BATU');
        $sheet->getStyle('C1')->getFont()->setSize(14)->setBold(true);

        $sheet->mergeCells('C2:I2');
        $sheet->setCellValue('C2', 'DINAS LINGKUNGAN HIDUP');
        $sheet->getStyle('C2')->getFont()->setSize(12)->setBold(true);

        $sheet->mergeCells('C3:I3');
        $sheet->setCellValue('C3', 'DAFTAR REKAPITULASI ARSIP DIGITAL');
        $sheet->getStyle('C3')->getFont()->setSize(10)->setBold(true);

        $sheet->mergeCells('C4:I4');
        $sheet->setCellValue('C4', 'Tanggal Ekspor: ' . date('d F Y, H:i') . ' WIB');
        $sheet->getStyle('C4')->getFont()->setSize(9)->setItalic(true);

        // Heading Table Styles (Row 6)
        $sheet->getStyle('A6:I6')->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size'  => 10,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4338CA'], // Indigo 700
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ],
        ]);

        $sheet->getRowDimension(6)->setRowHeight(28);

        $highestRow = $sheet->getHighestRow();
        if ($highestRow >= 6) {
            $sheet->getStyle("A6:I{$highestRow}")->applyFromArray([
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

            $sheet->getStyle("A7:A{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("E7:E{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("G7:G{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("I7:I{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        return [];
    }
}
