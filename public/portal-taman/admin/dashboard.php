<?php

session_start();

require_once '../config/database.php';
require_once '../config/functions.php';

requireAdmin();

$city = getOne($conn, "SELECT * FROM city_info LIMIT 1");

// Statistik taman
$statsTaman = getOne($conn, "SELECT COUNT(*) as c, COALESCE(SUM(area), 0) as total_area, COALESCE(SUM(employee_count), 0) as total_emp FROM parks");
$totalParks = (int) ($statsTaman['c'] ?? 0);
$totalArea = (float) ($statsTaman['total_area'] ?? 0);
$totalEmployees = (int) ($statsTaman['total_emp'] ?? 0);

$activeParks = (int) getOne($conn, "SELECT COUNT(*) c FROM parks WHERE status='Aktif'")['c'];
$maintenanceParks = (int) getOne($conn, "SELECT COUNT(*) c FROM parks WHERE status='Dalam Perawatan'")['c'];
$passiveParks = (int) getOne($conn, "SELECT COUNT(*) c FROM parks WHERE status='Pasif'")['c'];
$totalAdmins = (int) getOne($conn, "SELECT COUNT(*) c FROM admins")['c'];

?>

<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard - Portal Taman</title>
    <link rel="stylesheet" href="admin.css?v=6">
</head>

<body>

<!-- SIDEBAR -->
<aside>
    <a href="dashboard.php" class="brand">
        <img src="../assets/icons/logo.png" alt="Logo" class="brand-img">
        <div class="brand-info">
            <span class="brand-text">Portal Taman</span>
            <span class="brand-sub">Panel Admin</span>
        </div>
    </a>

    <div class="nav-label">MENU</div>

    <a class="active" href="dashboard.php">
        <img src="../assets/icons/dashboard.png" class="nav-icon-img" alt="Dashboard">
        <span class="nav-text">Dashboard</span>
    </a>

    <a href="parks.php">
        <img src="../assets/icons/parks.png" class="nav-icon-img" alt="Kelola Taman">
        <span class="nav-text">Kelola Taman</span>
    </a>

    <a href="city.php">
        <img src="../assets/icons/city.png" class="nav-icon-img" alt="Informasi Kota">
        <span class="nav-text">Informasi Kota</span>
    </a>

    <a href="admins.php">
        <img src="../assets/icons/admins.png" class="nav-icon-img" alt="Kelola Admin">
        <span class="nav-text">Kelola Admin</span>
    </a>

    <div class="nav-spacer"></div>

    <a class="nav-logout" href="logout.php">
        <?= icon('logout', '', 18) ?>
        <span class="nav-text">Keluar</span>
    </a>
</aside>

<!-- MAIN -->
<main>

    <h1>Dashboard</h1>
    <p class="page-subtitle">Selamat datang, <?= e($_SESSION['admin_username'] ?? 'Admin') ?>! Berikut ringkasan portal taman.</p>

    <!-- STAT CARDS -->
    <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">

        <div class="stat stat-blue">
            <div class="stat-icon-wrap">
                <?= icon('tree', '', 26) ?>
            </div>
            <div class="stat-info">
                <span class="stat-label">Total Taman</span>
                <span class="stat-value"><?= $totalParks ?></span>
            </div>
        </div>

        <div class="stat stat-green">
            <div class="stat-icon-wrap">
                <svg style="width:26px;height:26px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
            </div>
            <div class="stat-info">
                <span class="stat-label">Luas RTH</span>
                <span class="stat-value" style="font-size:1.35rem;"><?= number_format($totalArea, 0, ',', '.') ?> m²</span>
            </div>
        </div>

        <div class="stat stat-blue">
            <div class="stat-icon-wrap">
                <?= icon('user', '', 24) ?>
            </div>
            <div class="stat-info">
                <span class="stat-label">Tenaga Kerja</span>
                <span class="stat-value"><?= $totalEmployees ?> <span style="font-size:0.85rem; font-weight:normal;">Org</span></span>
            </div>
        </div>

        <div class="stat stat-green">
            <div class="stat-icon-wrap">
                <?= icon('check', '', 26) ?>
            </div>
            <div class="stat-info">
                <span class="stat-label">Status Aktif</span>
                <span class="stat-value"><?= $activeParks ?></span>
            </div>
        </div>

        <div class="stat stat-yellow">
            <div class="stat-icon-wrap">
                <?= icon('wrench', '', 24) ?>
            </div>
            <div class="stat-info">
                <span class="stat-label">Dalam Perawatan</span>
                <span class="stat-value"><?= $maintenanceParks ?></span>
            </div>
        </div>

        <div class="stat stat-red">
            <div class="stat-icon-wrap">
                <?= icon('pause', '', 24) ?>
            </div>
            <div class="stat-info">
                <span class="stat-label">Status Pasif</span>
                <span class="stat-value"><?= $passiveParks ?></span>
            </div>
        </div>

    </div>

    <!-- REKAPITULASI DOKUMEN & AKSI CEPAT -->
    <section>
        <h2>Aksi Rekapitulasi & Manajemen</h2>

        <div class="quick-actions">
            <a href="export_rekap.php" class="quick-action-link" style="background:#ecfdf5; border-color:#a7f3d0; color:#065f46;">
                <svg style="width:18px;height:18px; color:#059669;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <strong>Unduh Rekap Excel (.xlsx)</strong>
            </a>
            <a href="print_rekap.php" target="_blank" rel="noopener noreferrer" class="quick-action-link" style="background:#fef2f2; border-color:#fecaca; color:#991b1b;">
                <svg style="width:18px;height:18px; color:#dc2626;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <strong>Cetak / Simpan PDF Rekap</strong>
            </a>
            <a href="parks.php" class="quick-action-link">
                <?= icon('plus', '', 18) ?> Tambah / Kelola Taman
            </a>
            <a href="city.php" class="quick-action-link">
                <?= icon('edit', '', 18) ?> Edit Info Kota
            </a>
            <a href="../index.php" target="_blank" class="quick-action-link">
                <?= icon('globe', '', 18) ?> Buka Portal Publik
            </a>
        </div>
    </section>

    <!-- INFO KOTA -->
    <section>
        <h2>Informasi Kota</h2>

        <p style="margin-bottom: 8px;">
            <strong><?= e($city['city_name'] ?? '-') ?></strong>
        </p>
        <p style="color: var(--text-muted); line-height: 1.65;">
            <?= e($city['description'] ?? '-') ?>
        </p>
    </section>

</main>

</body>

</html>