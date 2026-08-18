<?php

use App\Models\SurveyHarga;
use App\Models\User;

$user = User::find(2); // Admin Sekretariat

if ($user) {
    $survey = new SurveyHarga();
    $survey->kelompok = 'SSH';
    $survey->judul = 'Laptop Core i5 14 inch (Dummy Data)';
    $survey->kode_komponen = '5.2.02.01';
    $survey->satuan = 'Unit';
    $survey->harga_usulan = 12500000;
    $survey->spesifikasi_singkat = 'Intel Core i5, RAM 8GB, SSD 512GB';
    
    // Toko 1
    $survey->nama_toko_1 = 'Toko Komputer Makmur';
    $survey->harga_toko_1 = 12000000;
    $survey->link_belanja_1 = 'https://tokopedia.com/dummy1';
    
    // Toko 2
    $survey->nama_toko_2 = 'Elektronik Jaya';
    $survey->harga_toko_2 = 12300000;
    $survey->link_belanja_2 = 'https://shopee.co.id/dummy2';

    $survey->status = 'diajukan';
    $survey->user_id = $user->id;
    $survey->department_id = $user->department_id;
    $survey->save();

    echo "Berhasil membuat data dummy dengan ID: " . $survey->id . "\n";
} else {
    echo "User ID 2 tidak ditemukan.\n";
}
