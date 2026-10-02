<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Kepegawaian - Export PDF</title>
    <style>
        @page { margin: 40px 30px; }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 8pt;
            color: #333;
            line-height: 1.3;
        }
        /* Kop Surat/Header */
        .kop-surat {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #1F4E79;
            padding-bottom: 10px;
        }
        .kop-surat h1 {
            font-size: 14pt;
            margin: 0;
            color: #1F4E79;
            text-transform: uppercase;
        }
        .kop-surat h2 {
            font-size: 12pt;
            margin: 3px 0;
        }
        .kop-surat p {
            font-size: 9pt;
            margin: 0;
            color: #555;
        }
        /* Metadata/Info Laporan */
        .info-laporan {
            margin-bottom: 15px;
            font-size: 9pt;
        }
        .info-laporan table { width: 100%; border: none; }
        .info-laporan td { padding: 2px 0; vertical-align: top; }
        .info-label { width: 100px; font-weight: bold; }
        /* Tabel Utama */
        .table-data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table-data th, .table-data td {
            border: 1px solid #ddd;
            padding: 5px 4px;
            word-wrap: break-word;
        }
        .table-data th {
            background-color: #1F4E79;
            color: white;
            font-weight: bold;
            text-align: center;
            font-size: 8pt;
        }
        .table-data tbody tr:nth-child(even) {
            background-color: #EEF4FB;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        /* Footer/Signatures */
        .footer {
            margin-top: 30px;
            width: 100%;
        }
        .signature {
            float: right;
            width: 250px;
            text-align: center;
        }
        .signature p { margin: 2px 0; }
        .signature-space { height: 60px; }
        /* Page Numbering */
        .page-number:before {
            content: "Halaman " counter(page);
        }
        .page-footer {
            position: fixed;
            bottom: -20px;
            left: 0;
            right: 0;
            text-align: right;
            font-size: 7pt;
            color: #888;
        }
    </style>
</head>
<body>

    <div class="page-footer text-right">
        <em>Dicetak pada: {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i:s') }} - <span class="page-number"></span></em>
    </div>

    <div class="kop-surat">
        @php
            $pathPemkot = public_path('images/logo-pemkot-batu.png');
            $base64Pemkot = 'data:image/png;base64,' . base64_encode(file_get_contents($pathPemkot));
            
            $pathDlh = public_path('images/logo-dlh.png');
            $base64Dlh = 'data:image/png;base64,' . base64_encode(file_get_contents($pathDlh));
        @endphp
        <table width="100%" style="border: none; border-collapse: collapse;">
            <tr>
                <td width="15%" style="text-align: left; border: none; vertical-align: middle;">
                    <img src="{{ $base64Pemkot }}" style="width: 70px;">
                </td>
                <td width="70%" style="text-align: center; border: none; vertical-align: middle;">
                    <h1>SISTEM MANAJEMEN KEPEGAWAIAN TERPADU</h1>
                    <h2>DINAS LINGKUNGAN HIDUP</h2>
                    <p>Laporan Data Base Kepegawaian</p>
                </td>
                <td width="15%" style="text-align: right; border: none; vertical-align: middle;">
                    <img src="{{ $base64Dlh }}" style="width: 80px;">
                </td>
            </tr>
        </table>
    </div>

    <div class="info-laporan">
        <table border="0">
            <tr>
                <td class="info-label">Jenis Laporan</td>
                <td>: {{ $judul }}</td>
                <td class="text-right font-bold" style="font-size: 10pt;">
                    Total Data: {{ count($pegawais) }} Pegawai
                </td>
            </tr>
            <tr>
                <td class="info-label">Tanggal Cetak</td>
                <td>: {{ $tanggal }}</td>
                <td class="text-right">
                    @if(!empty($filters['unit_kerja']))
                        Unit Kerja: {{ $filters['unit_kerja'] }}
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <table class="table-data">
        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="12%">NIK / NIP (NRK)</th>
                <th width="14%">Nama Lengkap</th>
                <th width="3%">L/P</th>
                <th width="8%">Kategori</th>
                <th width="12%">Jabatan</th>
                <th width="12%">Unit Kerja</th>
                <th width="8%">Pangkat/Gol</th>
                <th width="8%">TMT SK</th>
                <th width="8%">Kontrak</th>
                <th width="4%">Jam/Mgg</th>
                <th width="8%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pegawais as $index => $p)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        {{ $p->nik }}<br>
                        <span style="color: #666; font-size: 7pt;">{{ $p->nip_nrk ?? '-' }}</span>
                    </td>
                    <td class="font-bold">{{ $p->nama_lengkap }}</td>
                    <td class="text-center">{{ $p->jenis_kelamin }}</td>
                    <td class="text-center">{{ $p->kategori }}</td>
                    <td>{{ $p->jabatan }}</td>
                    <td>{{ $p->unit_kerja }}</td>
                    <td class="text-center">{{ $p->pangkat_golongan ?? '-' }}</td>
                    <td class="text-center">{{ $p->tmt_sk ? $p->tmt_sk->format('d/m/Y') : '-' }}</td>
                    <td class="text-center">{{ $p->masa_kontrak ?? '-' }}</td>
                    <td class="text-center">{{ $p->jam_kerja_mingguan }}</td>
                    <td class="text-center">{{ $p->status }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" class="text-center" style="padding: 20px;">
                        <em>Tidak ada data pegawai yang sesuai dengan kriteria.</em>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if(count($pegawais) > 0)
    <div class="footer">
        <div class="signature">
            <p>Mengetahui,</p>
            <p class="font-bold">Kepala Sub Bagian Umum & Kepegawaian</p>
            <div class="signature-space"></div>
            <p class="font-bold">____________________________</p>
            <p>NIP. ....................................</p>
        </div>
    </div>
    @endif

</body>
</html>
