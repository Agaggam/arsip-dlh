<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pengawasan Pelaku Usaha {{ $tahun }}</title>
    <style>
        @page { margin: 20px 15px; size: A4 landscape; }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 7pt;
            color: #1e293b;
            line-height: 1.3;
        }

        /* KOP SURAT */
        .kop-surat { border-bottom: 3px solid #1e3a5f; padding-bottom: 8px; margin-bottom: 10px; }
        .kop-table { width: 100%; border-collapse: collapse; border: none; }
        .kop-table td { border: none; vertical-align: middle; }
        .kop-center { text-align: center; }
        .kop-center h1 { font-size: 12pt; margin: 0 0 2px 0; color: #1e3a5f; text-transform: uppercase; letter-spacing: 0.5px; }
        .kop-center h2 { font-size: 10pt; margin: 2px 0; color: #1e3a5f; }
        .kop-center p  { font-size: 7pt; margin: 2px 0; color: #64748b; }

        /* JUDUL */
        .title-section { text-align: center; margin-bottom: 8px; }
        .title-section h3 { font-size: 10pt; color: #1e3a5f; margin: 0; text-transform: uppercase; font-weight: bold; letter-spacing: 0.5px; }
        .title-section p { font-size: 8pt; color: #475569; margin: 2px 0 0 0; }

        /* SECTION HEADER */
        .section-header {
            background: #1e3a5f;
            color: white;
            font-weight: bold;
            font-size: 8pt;
            padding: 4px 8px;
            margin-top: 10px;
            margin-bottom: 0;
            text-transform: uppercase;
        }

        /* DATA TABLE */
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        .data-table th, .data-table td {
            border: 1px solid #94a3b8;
            padding: 2px 3px;
            text-align: center;
            vertical-align: middle;
            font-size: 6.5pt;
        }
        .data-table th {
            background: #e2e8f0;
            font-weight: bold;
            color: #1e293b;
            font-size: 6pt;
            text-transform: uppercase;
        }
        .data-table .header-group {
            background: #cbd5e1;
            font-size: 7pt;
        }
        .data-table td.nama { text-align: left; white-space: nowrap; }
        .data-table td.skala { text-align: center; font-size: 6pt; }

        /* V, P, X styling */
        .val-v { color: #059669; font-weight: bold; }
        .val-p { color: #d97706; font-weight: bold; }
        .val-x { color: #dc2626; font-weight: bold; }
        .val-dash { color: #94a3b8; }

        /* Keterangan */
        .keterangan { margin-top: 8px; font-size: 7pt; }
        .keterangan table { border: none; }
        .keterangan td { border: none; padding: 1px 4px; vertical-align: top; }

        /* TTD */
        .ttd-section { margin-top: 15px; text-align: right; font-size: 7.5pt; }
        .ttd-name { font-weight: bold; margin-top: 50px; }

        /* FOOTER */
        .page-footer {
            position: fixed;
            bottom: -10px;
            left: 0; right: 0;
            text-align: right;
            font-size: 6pt;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 2px;
        }

        .page-break { page-break-before: always; }
    </style>
</head>
<body>

<div class="page-footer">
    Dicetak: {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }} &nbsp;|&nbsp; Sistem Informasi DLH
</div>

{{-- KOP SURAT --}}
<div class="kop-surat">
    @php
        $pathPemkot = public_path('images/logo-pemkot-batu.png');
        $base64Pemkot = file_exists($pathPemkot) ? 'data:image/png;base64,' . base64_encode(file_get_contents($pathPemkot)) : '';
        $pathDlh = public_path('images/logo-dlh.png');
        $base64Dlh = file_exists($pathDlh) ? 'data:image/png;base64,' . base64_encode(file_get_contents($pathDlh)) : '';
    @endphp
    <table class="kop-table">
        <tr>
            <td width="10%" style="text-align:left;">
                @if($base64Pemkot)<img src="{{ $base64Pemkot }}" style="height:50px;">@endif
            </td>
            <td width="80%" class="kop-center">
                <h1>PEMERINTAH KOTA BATU</h1>
                <h2>DINAS LINGKUNGAN HIDUP</h2>
                <p>Jl. Panglima Sudirman No.507, Pesanggrahan, Batu, Batu City, East Java 65313</p>
            </td>
            <td width="10%" style="text-align:right;">
                @if($base64Dlh)<img src="{{ $base64Dlh }}" style="height:50px;">@endif
            </td>
        </tr>
    </table>
</div>

{{-- JUDUL --}}
<div class="title-section">
    <h3>Pengawasan Pelaku Usaha dan/atau Kegiatan Tahun {{ $tahun ?? date('Y') }}</h3>
    <p>Pengawas: {{ $namaPengawas ?? 'Tim Pengawas Lingkungan Hidup' }}</p>
</div>

@php
    $hasilCols = \App\Models\Pengawasan::HASIL_COLUMNS;
    $jenisLabels = ['langsung' => 'LANGSUNG', 'tidak_langsung' => 'TIDAK LANGSUNG'];
@endphp

@foreach($grouped as $kecamatan => $jenisGroup)
    @foreach($jenisGroup as $jenis => $records)
        <div class="section-header">
            Pengawasan {{ $jenisLabels[$jenis] ?? $jenis }} di Wilayah {{ $kecamatan }}
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th rowspan="2" style="width:20px;">NO</th>
                    <th rowspan="2" style="width:100px;">NAMA USAHA</th>
                    <th rowspan="2" style="width:35px;">SKALA USAHA/DOK</th>
                    <th rowspan="2" style="width:55px;">WAKTU PENGAWASAN</th>
                    <th colspan="{{ count($hasilCols) }}" class="header-group">HASIL PENGAWASAN</th>
                    <th rowspan="2" style="width:20px;">Jml V</th>
                    <th rowspan="2" style="width:20px;">Jml P</th>
                    <th rowspan="2" style="width:20px;">Jml X</th>
                    <th rowspan="2" style="width:45px;">Keterangan</th>
                </tr>
                <tr>
                    @foreach($hasilCols as $col => $label)
                    <th style="width:30px; font-size:5pt;">{{ $label }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($records as $i => $record)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td class="nama">{{ $record->nama_usaha }}</td>
                    <td class="skala">{{ $record->skala_usaha }}</td>
                    <td>{{ $record->waktu_pengawasan->format('d/m/Y') }}</td>
                    @foreach(array_keys($hasilCols) as $col)
                    @php $val = $record->{$col}; @endphp
                    <td class="{{ $val === 'v' ? 'val-v' : ($val === 'p' ? 'val-p' : ($val === 'x' ? 'val-x' : 'val-dash')) }}">
                        {{ strtoupper($val) }}
                    </td>
                    @endforeach
                    <td><strong>{{ $record->jml_v }}</strong></td>
                    <td>{{ $record->jml_p }}</td>
                    <td>{{ $record->jml_x }}</td>
                    <td style="text-align:left; font-size:5.5pt;">{{ $record->keterangan ?? '' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="{{ 4 + count($hasilCols) + 4 }}" style="color:#94a3b8; font-style:italic; padding:8px;">
                        - Tidak ada data -
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    @endforeach
@endforeach

{{-- KETERANGAN --}}
<div class="keterangan">
    <table>
        <tr><td style="width:80px; font-weight:bold;">KETERANGAN</td><td>V</td><td>ada/baik</td></tr>
        <tr><td></td><td>P</td><td>proses</td></tr>
        <tr><td></td><td>X</td><td>tidak ada sama sekali</td></tr>
    </table>
</div>

{{-- TANDA TANGAN --}}
<div class="ttd-section">
    <p>Batu, {{ $tanggal }}</p>
    <p>PENGAWAS LINGKUNGAN HIDUP AHLI MUDA</p>
    <div class="ttd-name">
        <br><br><br>
        <u>{{ $namaPengawas }}</u>
    </div>
</div>

</body>
</html>
