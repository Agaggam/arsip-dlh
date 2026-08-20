<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekapitulasi Arsip Digital - Dinas Lingkungan Hidup Kota Batu</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 1.2cm 1cm 1.5cm 1cm;
        }
        * {
            box-sizing: border-box;
            font-family: Arial, Helvetica, sans-serif;
        }
        body {
            font-size: 8.5pt;
            color: #1a1a1a;
            line-height: 1.25;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 2.5px solid #000;
            padding-bottom: 6px;
            margin-bottom: 12px;
        }
        .header-table td {
            vertical-align: middle;
            border: none;
            padding: 0;
        }
        .logo-img {
            width: 60px;
            height: auto;
        }
        .header-text {
            text-align: center;
            padding-right: 60px;
        }
        .header-text h3 {
            margin: 0;
            font-size: 11pt;
            font-weight: normal;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .header-text h2 {
            margin: 2px 0;
            font-size: 13pt;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .header-text p {
            margin: 0;
            font-size: 7.5pt;
            color: #333;
        }
        .doc-title {
            text-align: center;
            margin: 8px 0 14px 0;
        }
        .doc-title h4 {
            margin: 0;
            font-size: 10.5pt;
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .doc-title p {
            margin: 2px 0 0 0;
            font-size: 8pt;
            color: #555;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .data-table th, .data-table td {
            border: 1px solid #666;
            padding: 5px 6px;
            font-size: 8pt;
        }
        .data-table th {
            background-color: #f0f3f8;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            font-size: 7.5pt;
        }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .footer-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            border: none;
        }
        .footer-table td {
            border: none;
            vertical-align: top;
            font-size: 8.5pt;
        }
    </style>
</head>
<body>

    {{-- KOP SURAT --}}
    <table class="header-table">
        <tr>
            <td style="width: 75px; text-align: center;">
                @if(file_exists(public_path('images/logopemkot.png')))
                <img src="{{ public_path('images/logopemkot.png') }}" class="logo-img" alt="Logo">
                @endif
            </td>
            <td class="header-text">
                <h3>PEMERINTAH KOTA BATU</h3>
                <h2>DINAS LINGKUNGAN HIDUP</h2>
                <p>Balaikota Among Tani Gedung B Lantai 2, Jl. Panglima Sudirman No. 507 Kota Batu 65313</p>
                <p>Telepon: (0341) 591034 | Faksimile: (0341) 591034 | Pos-el: dlh@batukota.go.id</p>
            </td>
        </tr>
    </table>

    {{-- JUDUL LAPORAN --}}
    <div class="doc-title">
        <h4>DAFTAR REKAPITULASI ARSIP DIGITAL</h4>
        <p>Dicetak pada: {{ $tanggal ?? date('d F Y') }}</p>
    </div>

    {{-- TABEL DATA ARSIP --}}
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 30px;">NO</th>
                <th style="text-align: left;">JUDUL ARSIP</th>
                <th style="width: 120px;">KATEGORI</th>
                <th style="width: 140px;">BIDANG / DEPARTEMEN</th>
                <th style="width: 60px;">TIPE</th>
                <th style="width: 70px;">UKURAN</th>
                <th style="width: 110px;">TANGGAL UPLOAD</th>
                <th style="width: 100px;">PENGUNGGAH</th>
                <th style="width: 50px;">HITS</th>
            </tr>
        </thead>
        <tbody>
            @forelse($archives as $idx => $arc)
            <tr>
                <td class="text-center">{{ $idx + 1 }}</td>
                <td class="text-left" style="font-weight: bold;">{{ $arc->title }}</td>
                <td class="text-center">{{ $arc->category?->name ?? '-' }}</td>
                <td class="text-left">{{ $arc->category?->department?->name ?? 'UMUM / SISTEM' }}</td>
                <td class="text-center">{{ strtoupper($arc->file_type) }}</td>
                <td class="text-center">{{ $arc->file_size ?? '-' }}</td>
                <td class="text-center">{{ $arc->archive_date ? \Carbon\Carbon::parse($arc->archive_date)->format('d/m/Y H:i') : '-' }}</td>
                <td class="text-center">{{ $arc->user?->name ?? 'System' }}</td>
                <td class="text-center">{{ $arc->download_count ?? 0 }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center" style="padding: 15px; color: #999;">Tidak ada data arsip yang tersedia.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- TANDA TANGAN / FOOTER --}}
    <table class="footer-table">
        <tr>
            <td style="width: 60%;"></td>
            <td style="width: 40%; text-align: center;">
                <p style="margin-bottom: 2px;">Batu, {{ $tanggal ?? date('d F Y') }}</p>
                <p style="font-weight: bold; margin-top: 0; margin-bottom: 50px;">Kepala Dinas Lingkungan Hidup<br>Kota Batu</p>
                <p style="font-weight: bold; text-decoration: underline; margin-bottom: 1px;">DIAN FACHRONGDY, S.STP., M.M.</p>
                <p style="font-size: 8pt; color: #444; margin-top: 0;">Pembina Tk. I (IV/b)<br>NIP. 19780514 199702 1 002</p>
            </td>
        </tr>
    </table>

</body>
</html>
