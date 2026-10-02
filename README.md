# E-Arsip DLH — Sistem Informasi Manajemen Kearsipan Digital

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3.x-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg?style=for-the-badge)](https://opensource.org/licenses/MIT)

**E-Arsip DLH** adalah aplikasi berbasis web yang dirancang khusus untuk memodernisasi tata kelola kearsipan, kepegawaian, pengawasan lingkungan hidup, dan survei harga pada Dinas Lingkungan Hidup. Sistem ini memfasilitasi penyimpanan dokumen secara terstruktur, pengawasan hak akses bertingkat, serta pelaporan otomatis dalam format PDF dan Excel.

---

## 🌟 Fitur Utama

### 1. 📁 Manajemen Arsip Digital
- **Penyimpanan Terpusat:** Upload dan kategorisasi dokumen arsip berdasarkan bidang/departemen.
- **Secure Preview:** Pratinjau dokumen aman menggunakan token temporer untuk mencegah akses langsung yang tidak sah.
- **Soft Delete & Universal Trash:** Pemulihan berkas terhapus (*restore*) serta penghapusan permanen (*force delete*) melalui Universal Trash Bin.
- **Ekspor Dokumen:** Cetak rekapitulasi arsip dalam format PDF dan Excel (baik kolektif maupun per-arsip).

### 2. 👥 Modul Kepegawaian
- Pencatatan biodata aparatur dan pegawai dinas berdasarkan bidang/seksi.
- Fitur **Import Excel** dan unduh template baku format kepegawaian.
- Input data individual maupun massal (*bulk entry*).
- Cetak lembar kepegawaian resmi ber-kop surat dinas (PDF/Excel) dengan 1-click quick export.

### 3. 📋 Modul Usulan Survey Harga (SSH / SBU)
- Penginputan usulan standar harga barang/jasa lingkungan.
- Validasi spesifikasi, satuan, dan penyesuaian harga pasar.
- Cetak rekapitulasi usulan format dinas (PDF dan Excel).

### 4. 🔍 Modul Pengawasan Pelaku Usaha
- Instrumen pencatatan kepatuhan izin lingkungan pelaku usaha/industri.
- Penilaian kriteria kepatuhan (*checklist* V, P, X).
- Cetak Berita Acara dan laporan hasil pengawasan komprehensif ke PDF/Excel.

### 5. 🛡️ Keamanan, Audit & Administrasi
- **Role-Based Access Control (RBAC):**
  - **Super Admin:** Kontrol penuh atas master data, departemen, role, backup database, activity log, dan integrasi SSO.
  - **Admin Departemen:** Pengelolaan arsip dan dokumen spesifik pada lingkup bidang tugas masing-masing.
  - **User Biasa:** Akses lihat dan unduh arsip publik/departemen yang diizinkan.
- **Approval Registrasi:** Verifikasi manual status akun baru (`pending`, `approved`, `rejected`).
- **Verifikasi Email OTP:** Pengubahan email pengguna wajib divalidasi dengan kode OTP.
- **Log Aktivitas:** Pencatatan otomatis setiap aksi pengguna (*audit trail*) untuk akuntabilitas.
- **Backup & Restore Database:** Pencadangan basis data berkala yang dilindungi mekanisme *rate-limiting*.

---

## 🛠️ Tumpukan Teknologi

- **Backend:** [Laravel 12](https://laravel.com) (PHP 8.2+)
- **Frontend:** Blade Templates, [Tailwind CSS](https://tailwindcss.com), [Alpine.js](https://alpinejs.dev)
- **Database:** MySQL / SQLite
- **Autentikasi:** Laravel Breeze (Kustomisasi Multi-Role & Approval)
- **Export Engine:**
  - PDF: [laravel-dompdf](https://github.com/barryvdh/laravel-dompdf)
  - Spreadsheet: [Maatwebsite Laravel Excel](https://laravel-excel.com)
- **Asset Bundler:** [Vite](https://vitejs.dev)

---

## 🚀 Panduan Instalasi Lokal

Ikuti langkah-langkah berikut untuk menjalankan proyek di lingkungan pengembangan lokal:

### 1. Klon Repositori
```bash
git clone https://github.com/Agaggam/arsip-dlh.git
cd arsip-dlh
```

### 2. Pasang Dependensi
```bash
# Pasang dependensi PHP
composer install

# Pasang dependensi Node.js
npm install
```

### 3. Konfigurasi Lingkungan (`.env`)
Salin file konfigurasi contoh dan buat file `.env`:
```bash
cp .env.example .env
```
Sesuaikan konfigurasi basis data Anda pada `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=arsip_dlh
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Generate Kunci Aplikasi & Link Storage
```bash
php artisan key:generate
php artisan storage:link
```

### 5. Migrasi & Seed Database
Jalankan migrasi untuk membuat tabel beserta data inisial akun pengujian:
```bash
php artisan migrate --seed
```

### 6. Menjalankan Server Pengembangan
Jalankan backend server dan Vite bundler (pada dua terminal terpisah atau via composer script):

```bash
# Terminal 1 (Laravel Server)
php artisan serve

# Terminal 2 (Vite Dev Server)
npm run dev
```

Aplikasi dapat diakses melalui browser pada: `http://localhost:8000`

---

## 🔑 Akun Pengujian Default

Setelah menjalankan `php artisan db:seed`, akun berikut siap digunakan:

| Role | Email | Password | Hak Akses |
|---|---|---|---|
| **Super Admin** | `superadmin@example.com` | `superadmin123` | Akses penuh semua modul & pengaturan |
| **Admin Sekretariat** | `admin1@example.com` | `admin123` | Kelola arsip Bidang Sekretariat |
| **Admin Tata Lingkungan** | `admin2@example.com` | `admin123` | Kelola arsip Bidang Tata Lingkungan |
| **Admin Pengelolaan Sampah** | `admin3@example.com` | `admin123` | Kelola arsip Bidang Pengelolaan Sampah |
| **User Biasa** | `user@example.com` | `user123` | Lihat & unduh arsip publik |

---

## 📂 Struktur Modul Utama

```text
arsip-dlh/
├── app/
│   ├── Http/Controllers/
│   │   ├── Admin/               # Controller modul admin (Arsip, Pegawai, Pengawasan, Backup, dll.)
│   │   └── Auth/                # Controller autentikasi & registrasi
│   ├── Models/                  # Model Eloquent (Archive, Pegawai, Pengawasan, SurveyHarga, dll.)
│   └── Helpers/                 # Custom helper functions
├── database/
│   ├── migrations/              # Skema tabel database
│   └── seeders/                 # Data default role, departemen, dan pengguna
├── resources/
│   ├── views/                   # Template Blade (Antarmuka pengguna & dokumen PDF)
│   ├── css/                     # Styling Tailwind CSS
│   └── js/                      # Script frontend
└── routes/
    ├── web.php                  # Rute aplikasi utama
    └── auth.php                 # Rute autentikasi
```

---

## 📄 Lisensi

Proyek ini dikembangkan di bawah lisensi [MIT License](LICENSE).
