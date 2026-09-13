PORTAL TAMAN KOTA - PHP + MYSQL

Konsep:
Website direktori untuk menampilkan banyak taman yang berada dalam satu kota.

Fitur:
- Daftar seluruh taman
- Pencarian taman
- Filter berdasarkan kategori
- Halaman detail setiap taman
- Alamat, jam buka, fasilitas, foto dan Google Maps
- Informasi kota
- Admin login
- Admin CRUD tambah/hapus taman
- Admin edit informasi kota
- Responsive

INSTALASI:
1. Extract folder portal-taman-kota ke C:\xampp\htdocs\
2. Jalankan Apache dan MySQL.
3. Buka http://localhost/phpmyadmin
4. Import database/portal_taman_kota.sql
5. Buka http://localhost/portal-taman-kota/
6. Admin: http://localhost/portal-taman-kota/admin/login.php
   username: admin
   password: password

Nama kota, data taman, foto, alamat, fasilitas, dan Google Maps dapat diganti melalui database/admin.


==================================================
FITUR PETA LOKASI TAMAN
==================================================

Versi ini menambahkan:
- Field Latitude dan Longitude pada admin > Kelola Taman.
- Peta interaktif Leaflet + OpenStreetMap di halaman utama.
- Marker taman dengan warna berdasarkan status.
- Tombol panah pada setiap kartu taman untuk langsung scroll ke peta,
  zoom ke koordinat taman, dan membuka informasi lokasi.
- Tombol panah otomatis nonaktif jika koordinat taman belum diisi.

PENTING UNTUK DATABASE YANG SUDAH ADA:
Jalankan file:
database/update_park_fields.sql

Cara mengisi koordinat:
1. Buka Google Maps.
2. Klik kanan tepat pada lokasi taman.
3. Salin angka Latitude dan Longitude.
4. Masukkan ke form Kelola Taman.
5. Simpan.

Contoh format:
Latitude  : -7.795580
Longitude : 110.369490

Peta menggunakan Leaflet dan OpenStreetMap, sehingga tidak membutuhkan
Google Maps API key.
