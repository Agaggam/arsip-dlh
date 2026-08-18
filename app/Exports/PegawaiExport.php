<?php

namespace App\Exports;

use App\Models\Pegawai;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class PegawaiExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithColumnFormatting,
    ShouldAutoSize,
    WithTitle,
    WithDrawings,
    WithCustomStartCell
{
    protected array $filters;
    protected string $title;

    public function __construct(array $filters = [], string $title = 'Data Pegawai')
    {
        $this->filters = $filters;
        $this->title   = $title;
    }

    public function collection(): Collection
    {
        return Pegawai::filter($this->filters)
            ->orderBy('nama_lengkap')
            ->get();
    }

    public function title(): string
    {
        return 'Data Pegawai';
    }

    public function startCell(): string
    {
        return 'A6';
    }

    public function drawings()
    {
        $drawings = [];

        $logoBatu = new Drawing();
        $logoBatu->setName('Logo Pemkot Batu');
        $logoBatu->setDescription('Logo Pemkot Batu');
        $logoBatu->setPath(public_path('images/logo-pemkot-batu.png'));
        $logoBatu->setHeight(60);
        $logoBatu->setCoordinates('B2');
        $drawings[] = $logoBatu;

        $logoDLH = new Drawing();
        $logoDLH->setName('Logo DLH');
        $logoDLH->setDescription('Logo DLH');
        $logoDLH->setPath(public_path('images/logo-dlh.png'));
        $logoDLH->setHeight(60);
        $logoDLH->setCoordinates('M2');
        $drawings[] = $logoDLH;

        return $drawings;
    }

    public function headings(): array
    {
        return [
            'No.',
            'NIK',
            'NIP / NRK',
            'Nama Lengkap',
            'Jenis Kelamin',
            'Kategori',
            'Pangkat / Golongan',
            'Jabatan',
            'Unit Kerja',
            'TMT SK',
            'Masa Kontrak',
            'Jam Kerja/Minggu',
            'Pendidikan Terakhir',
            'No. HP',
            'Email',
            'Status',
        ];
    }

    /** @param Pegawai $row */
    public function map($row): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            // NIK & NIP dipaksa jadi string agar tidak terpotong / scientific notation
            "\t" . $row->nik,
            "\t" . ($row->nip_nrk ?? '-'),
            $row->nama_lengkap,
            $row->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan',
            $row->kategori,
            $row->pangkat_golongan ?? '-',
            $row->jabatan,
            $row->unit_kerja,
            $row->tmt_sk ? $row->tmt_sk->format('d/m/Y') : '-',
            $row->masa_kontrak ?? '-',
            $row->jam_kerja_mingguan . ' jam',
            $row->pendidikan_terakhir ?? '-',
            $row->no_hp ?? '-',
            $row->email ?? '-',
            $row->status,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_TEXT,
            'C' => NumberFormat::FORMAT_TEXT,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $lastRow = $sheet->getHighestRow();
        $lastCol = $sheet->getHighestColumn();

        // === TITLE ROW STYLE ===
        $sheet->mergeCells('A2:P2');
        $sheet->setCellValue('A2', 'SISTEM MANAJEMEN KEPEGAWAIAN TERPADU - DINAS LINGKUNGAN HIDUP');
        $sheet->getStyle('A2:P2')->applyFromArray([
            'font' => [
                'bold'  => true,
                'size'  => 14,
                'name'  => 'Calibri',
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);
        
        $sheet->mergeCells('A3:P3');
        $sheet->setCellValue('A3', 'LAPORAN DATA BASE KEPEGAWAIAN');
        $sheet->getStyle('A3:P3')->applyFromArray([
            'font' => [
                'bold'  => true,
                'size'  => 12,
                'name'  => 'Calibri',
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);
        
        // === HEADER ROW STYLE ===
        $sheet->getStyle('A6:' . $lastCol . '6')->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size'  => 10,
                'name'  => 'Calibri',
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1F4E79'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // === FREEZE HEADER ===
        $sheet->freezePane('A7');

        // === ALTERNATING ROW COLORS ===
        for ($i = 7; $i <= $lastRow; $i++) {
            $fillColor = ($i % 2 === 0) ? 'EEF4FB' : 'FFFFFF';
            $sheet->getStyle("A{$i}:{$lastCol}{$i}")->applyFromArray([
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $fillColor],
                ],
                'font' => [
                    'size' => 9,
                    'name' => 'Calibri',
                ],
            ]);
        }

        // === ALL CELLS BORDER ===
        if ($lastRow > 6) {
            $sheet->getStyle('A6:' . $lastCol . $lastRow)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['rgb' => 'D0D7E2'],
                    ],
                ],
            ]);
        }

        // === ROW HEIGHT ===
        $sheet->getRowDimension(6)->setRowHeight(20);
        for ($i = 7; $i <= $lastRow; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(15);
        }

        return [];
    }
}
