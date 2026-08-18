<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Formulir Survei Harga - {{ $surveyHarga->judul }}</title>
    <style>
        @page { margin: 30px 25px; }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 9pt;
            color: #1e293b;
            line-height: 1.4;
        }

        /* KOP SURAT */
        .kop-surat {
            border-bottom: 3px solid #1e3a5f;
            padding-bottom: 10px;
            margin-bottom: 14px;
        }
        .kop-table { width: 100%; border-collapse: collapse; border: none; }
        .kop-table td { border: none; vertical-align: middle; }
        .kop-center { text-align: center; }
        .kop-center h1 { font-size: 13pt; margin: 0 0 2px 0; color: #1e3a5f; text-transform: uppercase; letter-spacing: 0.5px; }
        .kop-center h2 { font-size: 11pt; margin: 2px 0; color: #1e3a5f; }
        .kop-center p  { font-size: 8pt; margin: 2px 0; color: #64748b; }

        /* BADGE KELOMPOK */
        .badge-kelompok {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 4px;
            font-size: 10pt;
            font-weight: bold;
            background: #e0e7ff;
            color: #3730a3;
        }

        /* META INFO */
        .meta-table { width: 100%; border: none; margin-bottom: 14px; }
        .meta-table td { border: none; padding: 2px 4px; font-size: 8.5pt; }
        .meta-label { width: 120px; color: #64748b; font-weight: bold; }
        .meta-colon { width: 10px; color: #94a3b8; }

        /* TABLE UTAMA */
        .section-title {
            background: #1e3a5f;
            color: white;
            font-weight: bold;
            font-size: 9pt;
            padding: 5px 10px;
            letter-spacing: 0.5px;
        }
        .detail-table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        .detail-table td, .detail-table th {
            border: 1px solid #cbd5e1;
            padding: 5px 8px;
            vertical-align: top;
        }
        .detail-table .col-label { width: 28%; background: #f8fafc; font-weight: bold; color: #475569; font-size: 8.5pt; }
        .detail-table .col-colon { width: 3%; text-align: center; color: #94a3b8; }

        /* SURVEI TOKO */
        .toko-grid { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        .toko-grid td { border: 1px solid #cbd5e1; padding: 0; vertical-align: top; width: 33.33%; }
        .toko-inner { padding: 8px; }
        .toko-num {
            display: inline-block;
            width: 18px; height: 18px;
            background: #1e3a5f;
            color: white;
            border-radius: 50%;
            text-align: center;
            line-height: 18px;
            font-size: 8pt;
            font-weight: bold;
            margin-bottom: 6px;
        }
        .toko-label { font-size: 7.5pt; color: #64748b; margin-bottom: 1px; }
        .toko-value { font-size: 9pt; font-weight: bold; color: #1e293b; margin-bottom: 5px; }
        .toko-img { width: 100%; max-height: 90px; object-fit: contain; border: 1px solid #e2e8f0; border-radius: 4px; margin-top: 4px; }
        .toko-link { font-size: 7pt; color: #3b82f6; word-break: break-all; margin-top: 3px; }
        .no-data { color: #94a3b8; font-style: italic; font-size: 8pt; }

        /* HARGA HIGHLIGHT */
        .harga-box {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 4px;
            padding: 8px 12px;
            margin-bottom: 16px;
        }
        .harga-box .label { font-size: 8pt; color: #64748b; }
        .harga-box .value { font-size: 16pt; font-weight: bold; color: #1d4ed8; }
        .harga-box table { width: 100%; border: none; }
        .harga-box td { border: none; vertical-align: middle; }

        /* STATUS BADGE */
        .status-diajukan  { background: #dbeafe; color: #1e40af; }
        .status-disetujui { background: #dcfce7; color: #166534; }
        .status-ditolak   { background: #fee2e2; color: #991b1b; }
        .status-badge {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 20px;
            font-size: 8pt;
            font-weight: bold;
        }

        /* TTD */
        .ttd-table { width: 100%; border: none; margin-top: 24px; }
        .ttd-table td { border: none; text-align: center; vertical-align: top; width: 33.33%; }
        .ttd-space { height: 55px; }
        .ttd-name { font-weight: bold; border-top: 1px solid #475569; display: inline-block; width: 80%; padding-top: 4px; margin-top: 4px; }

        /* FOOTER */
        .page-footer {
            position: fixed;
            bottom: -15px;
            left: 0; right: 0;
            text-align: right;
            font-size: 7pt;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 3px;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
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
            <td width="12%" style="text-align:left;">
                @if($base64Pemkot)<img src="{{ $base64Pemkot }}" style="height:65px;">@endif
            </td>
            <td width="76%" class="kop-center">
                <h1>PEMERINTAH KOTA BATU</h1>
                <h2>DINAS LINGKUNGAN HIDUP</h2>
                <p>Jl. Panglima Sudirman No.507, Pesanggrahan, Batu, Batu City, East Java 65313</p>
            </td>
            <td width="12%" style="text-align:right;">
                @if($base64Dlh)<img src="{{ $base64Dlh }}" style="height:65px;">@endif
            </td>
        </tr>
    </table>
</div>

{{-- JUDUL FORMULIR --}}
<div class="text-center" style="margin-bottom:12px;">
    <span class="badge-kelompok">{{ ['SSH' => 'Standar Satuan Harga', 'SBU' => 'Standar Biaya Umum', 'HSPK' => 'Harga Satuan Pokok Kegiatan', 'ASB' => 'Analisis Standar Belanja'][$surveyHarga->kelompok] ?? $surveyHarga->kelompok }}</span>
    <div style="font-size:12pt; font-weight:bold; color:#1e3a5f; margin-top:6px; text-transform:uppercase; letter-spacing:0.5px;">
        FORMULIR SURVEI HARGA PASAR
    </div>
    <div style="font-size:9pt; color:#64748b; margin-top:2px;">
        Usulan {{ $surveyHarga->kelompok }}
    </div>
</div>

{{-- META INFO --}}
<table class="meta-table">
    <tr>
        <td class="meta-label">Departemen Pengusul</td>
        <td class="meta-colon">:</td>
        <td>{{ $surveyHarga->department->name ?? '-' }}</td>
        <td class="text-right" style="color:#64748b; font-size:8pt;">Tanggal Cetak: {{ $tanggal }}</td>
    </tr>
    <tr>
        <td class="meta-label">Pengisi Formulir</td>
        <td class="meta-colon">:</td>
        <td>{{ $surveyHarga->user->name ?? '-' }}</td>
        <td class="text-right">
            <span class="status-badge status-{{ $surveyHarga->status }}">{{ ucfirst($surveyHarga->status) }}</span>
        </td>
    </tr>
</table>

{{-- SECTION A: DATA BARANG --}}
<div class="section-title">A. DATA BARANG / JASA</div>
<table class="detail-table">
    <tr>
        <td class="col-label">Judul</td>
        <td class="col-colon">:</td>
        <td style="font-weight:bold; font-size:10pt;">{{ $surveyHarga->judul }}</td>
    </tr>
    <tr>
        <td class="col-label">Kode Komponen</td>
        <td class="col-colon">:</td>
        <td>{{ $surveyHarga->kode_komponen ?? '-' }}</td>
    </tr>
    <tr>
        <td class="col-label">Kode Rekening</td>
        <td class="col-colon">:</td>
        <td>{{ $surveyHarga->kode_rekening ?? '-' }}</td>
    </tr>
    <tr>
        <td class="col-label">Satuan</td>
        <td class="col-colon">:</td>
        <td>{{ $surveyHarga->satuan }}</td>
    </tr>
    @if($surveyHarga->spesifikasi_singkat)
    <tr>
        <td class="col-label">Spesifikasi Singkat</td>
        <td class="col-colon">:</td>
        <td>{{ $surveyHarga->spesifikasi_singkat }}</td>
    </tr>
    @endif
    @if($surveyHarga->spesifikasi_detail)
    <tr>
        <td class="col-label">Spesifikasi Detail</td>
        <td class="col-colon">:</td>
        <td style="white-space: pre-line;">{{ $surveyHarga->spesifikasi_detail }}</td>
    </tr>
    @endif
</table>

{{-- HARGA USULAN --}}
<div class="harga-box">
    <table>
        <tr>
            <td>
                <div class="label">Harga Rata-rata yang Diusulkan</div>
                <div class="value">Rp {{ number_format($surveyHarga->harga_usulan, 0, ',', '.') }}</div>
            </td>
            <td class="text-right">
                <div class="label">Per Satuan</div>
                <div style="font-size:10pt; font-weight:bold; color:#475569;">{{ $surveyHarga->satuan }}</div>
            </td>
        </tr>
    </table>
</div>

{{-- SECTION B: DATA SURVEI TOKO --}}
<div class="section-title">B. DATA SURVEI TOKO PEMBANDING</div>
<table class="toko-grid">
    <tr>
        @foreach([1, 2, 3] as $i)
        @php
            $namaToko  = $surveyHarga->{"nama_toko_{$i}"};
            $hargaToko = $surveyHarga->{"harga_toko_{$i}"};
            $gambar    = $surveyHarga->{"gambar_toko_{$i}"};
            $link      = $surveyHarga->{"link_belanja_{$i}"};
            $imgPath   = $gambar ? public_path("storage/{$gambar}") : null;
            $imgBase64 = ($imgPath && file_exists($imgPath))
                ? 'data:image/' . pathinfo($imgPath, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($imgPath))
                : null;
        @endphp
        <td>
            <div class="toko-inner">
                <div><span class="toko-num">{{ $i }}</span></div>
                @if($namaToko)
                    <div class="toko-label">Nama Toko</div>
                    <div class="toko-value">{{ $namaToko }}</div>
                    <div class="toko-label">Harga</div>
                    <div class="toko-value" style="color:#059669;">Rp {{ number_format($hargaToko ?? 0, 0, ',', '.') }}</div>
                    @if($imgBase64)
                    <img src="{{ $imgBase64 }}" alt="Gambar Toko {{ $i }}" class="toko-img">
                    @endif
                    @if($link)
                    <div class="toko-label" style="margin-top:4px;">Link Belanja</div>
                    <div class="toko-link">{{ $link }}</div>
                    @endif
                @else
                    <div class="no-data">— Tidak diisi —</div>
                @endif
            </div>
        </td>
        @endforeach
    </tr>
</table>

{{-- CATATAN ADMIN --}}
@if($surveyHarga->catatan_admin)
<div style="background:#fef9c3; border:1px solid #fde047; border-radius:4px; padding:8px 12px; margin-bottom:16px; font-size:8.5pt;">
    <strong>Catatan dari Verifikator:</strong> {{ $surveyHarga->catatan_admin }}
</div>
@endif

{{-- TANDA TANGAN --}}
<table class="ttd-table">
    <tr>
        <td>
            <div style="font-size:8.5pt; color:#64748b;">Dibuat oleh,</div>
            <div class="ttd-space"></div>
            <div class="ttd-name">{{ $surveyHarga->user->name ?? '____________________' }}</div>
            <div style="font-size:7.5pt; color:#64748b;">{{ $surveyHarga->department->name ?? '' }}</div>
        </td>
        <td></td>
        <td>
            <div style="font-size:8.5pt; color:#64748b;">Mengetahui,</div>
            <div style="font-size:7.5pt; color:#64748b; margin-bottom:2px;">Kepala Sub Bagian Umum & Kepegawaian</div>
            <div class="ttd-space"></div>
            <div class="ttd-name">____________________________</div>
            <div style="font-size:7.5pt; color:#64748b;">NIP. ................................</div>
        </td>
    </tr>
</table>

</body>
</html>
