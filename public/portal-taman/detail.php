<?php

require_once 'config/database.php';
require_once 'config/functions.php';

$city = getOne($conn, "SELECT * FROM city_info LIMIT 1");

$id = (int) ($_GET['id'] ?? 0);

$stmt = $conn->prepare(
    "SELECT * FROM parks WHERE id = ? LIMIT 1"
);

$stmt->bind_param("i", $id);
$stmt->execute();

$park = $stmt->get_result()->fetch_assoc();

if (!$park) {
    http_response_code(404);
    die("Taman tidak ditemukan.");
}

// Ambil foto galeri tambahan. Jika database lama belum memiliki
// tabel galeri, tetap tampilkan detail menggunakan foto utama.
$gallery = [];
$galleryStmt = $conn->prepare(
    "SELECT image FROM park_images
     WHERE park_id = ? ORDER BY sort_order ASC, id ASC"
);

if ($galleryStmt) {
    $galleryStmt->bind_param("i", $id);
    $galleryStmt->execute();
    $gallery = $galleryStmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $galleryStmt->close();
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$isAdminLoggedIn = !empty($_SESSION['admin']);

// Data slider
$slides = array_values(array_filter(array_merge([$park['image']], $gallery)));

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>
        <?= e($park['name']) ?> - Detail Taman
    </title>

    <link rel="stylesheet" href="assets/css/model.css?v=7">
</head>

<body>

    <!-- Header -->
    <header class="nav">
        <div class="container nav-in">

            <a class="logo" href="index.php" style="display: inline-flex; align-items: center; gap: 9px;">
                <img src="assets/icons/logo.png" alt="Logo" style="width: 36px; height: 36px; border-radius: 50%; object-fit: contain;">
                <span>City Parks</span>
            </a>

            <nav>
                <a href="index.php">Beranda</a>
                <a href="index.php#taman">Daftar Taman</a>
                <a href="index.php#tentang">Tentang</a>
                <?php if ($isAdminLoggedIn): ?>
                    <a href="admin/dashboard.php" class="nav-admin-btn is-logged" title="Masuk ke Panel Admin">
                        <?= icon('check', '', 14) ?> <span>Panel Admin</span>
                    </a>
                <?php else: ?>
                    <a href="admin/login.php" class="nav-admin-btn" title="Login Petugas Admin">
                        <?= icon('user', '', 14) ?> <span>Masuk Admin</span>
                    </a>
                <?php endif; ?>
            </nav>

        </div>
    </header>

    <!-- Detail Taman -->
    <main class="detail">

        <div class="container">

            <!-- Slider Foto Taman -->
            <div class="slider" id="parkSlider">

                <div class="slider-track">

                    <?php foreach ($slides as $src): ?>

                        <div class="slide">
                            <img src="<?= e($src) ?>" alt="<?= e($park['name']) ?>">
                        </div>

                    <?php endforeach; ?>

                </div>

                <?php if (count($slides) > 1): ?>

                    <button class="slider-arrow prev" type="button" aria-label="Sebelumnya">‹</button>
                    <button class="slider-arrow next" type="button" aria-label="Berikutnya">›</button>

                    <div class="slider-dots">

                        <?php foreach ($slides as $i => $src): ?>

                            <button
                                class="dot <?= $i === 0 ? 'active' : '' ?>"
                                type="button"
                                data-index="<?= $i ?>"
                            ></button>

                        <?php endforeach; ?>

                    </div>

                <?php endif; ?>

            </div>

            <!-- Informasi Taman -->
            <div class="detail-card">

                <p class="label">
                    <?= e($park['category']) ?>
                </p>

                <h1>
                    <?= e($park['name']) ?>
                </h1>

                <p style="color: var(--text-muted); font-size: 0.95rem; display: flex; align-items: center; gap: 8px; margin: 6px 0;">
                    <?= icon('map-pin', '', 16) ?> <span><?= e($park['address']) ?></span>
                </p>

                <p style="color: var(--text-muted); font-size: 0.95rem; display: flex; align-items: center; gap: 8px; margin: 6px 0 20px;">
                    <?= icon('clock', '', 16) ?> <span><?= e($park['opening_hours']) ?> WIB</span>
                </p>

                <!-- Deskripsi -->
                <h3>Tentang Taman</h3>

                <p style="line-height: 1.7; color: var(--text);">
                    <?= nl2br(e($park['description'])) ?>
                </p>


                <div class="uraian">    

                    <!-- Pegawai -->
                    <div class="info-item">
                        <span class="info-label" style="display: flex; align-items: center; gap: 6px;">
                            <?= icon('user', '', 16) ?> Pegawai
                        </span>
                        <span class="info-value">
                            <?= nl2br(e($park['employee_count'])) ?>
                        </span>
                    </div>

                    <!-- Luas -->
                    <div class="info-item">
                        <span class="info-label" style="display: flex; align-items: center; gap: 6px;">
                            <?= icon('area', '', 16) ?> Luas
                        </span>
                        <span class="info-value">
                            <?= nl2br(e($park['area'])) ?> m²
                        </span>
                    </div>

                    <!-- Status -->
                    <div class="info-item">
                        <span class="info-label" style="display: flex; align-items: center; gap: 6px;">
                            <?= icon('check', '', 16) ?> Status
                        </span>
                        <span class="info-value status-<?= strtolower(str_replace(' ', '-', $park['status'])) ?>">
                            <?= nl2br(e($park['status'])) ?>
                        </span>
                    </div>

                </div>


                <!-- Jenis Tanaman -->
                <h3 class="judul">Jenis Tanaman</h3>

                <div class="jenis">

                    <?php foreach (explode(',', $park['plant_species']) as $species): ?>

                        <span class="jenis">
                            <?= e(trim($species)) ?>
                        </span>

                    <?php endforeach; ?>

                </div>

                <!-- Fasilitas -->
                <h3>Fasilitas</h3>

                <div class="facility-list">

                    <?php foreach (explode(',', $park['facilities']) as $facility): ?>

                        <span>
                            ✓ <?= e(trim($facility)) ?>
                        </span>

                    <?php endforeach; ?>

                </div>

                <!-- Google Maps -->
                <?php
                $detailMapUrl = !empty($park['map_url'])
                    ? $park['map_url']
                    : ((!empty($park['latitude']) && !empty($park['longitude'])) ? "https://www.google.com/maps?q={$park['latitude']},{$park['longitude']}" : '');
                ?>
                <?php if (!empty($detailMapUrl)): ?>
                    <a
                        class="map"
                        target="_blank"
                        rel="noopener noreferrer"
                        href="<?= e($detailMapUrl) ?>"
                        style="display: inline-flex; align-items: center; gap: 8px;"
                    >
                        <?= icon('globe', '', 18) ?> <span>Buka Google Maps</span>
                    </a>
                <?php endif; ?>

            </div>

        </div>

    </main>

    <!-- Footer -->
    <footer id="kontak">
        <div class="container foot" style="display: flex; flex-direction: column; gap: 10px;">
            <div>
                <strong style="display: inline-flex; align-items: center; gap: 8px; font-size: 1.1rem; color: #ffffff;">
                    <img src="assets/icons/logo.png" alt="Logo" style="width: 30px; height: 30px; border-radius: 50%; vertical-align: middle;">
                    <span>Portal Taman <?= e($city['city_name'] ?? 'Kota Batu') ?></span>
                </strong>

                <p style="margin: 8px 0 0; color: #a9cfb8; display: flex; align-items: flex-start; gap: 8px; font-size: 0.92rem; line-height: 1.6;">
                    <?= icon('map-pin', '', 16) ?>
                    <span><?= e($city['contact'] ?? 'Dinas Lingkungan Hidup Kota Batu') ?></span>
                </p>
            </div>

            <div style="border-top: 1px solid rgba(255, 255, 255, 0.12); padding-top: 14px; margin-top: 4px; font-size: 0.82rem; color: #7ea38d;">
                &copy; <?= date('Y') ?> Dinas Lingkungan Hidup (DLH) <?= e($city['city_name'] ?? 'Kota Batu') ?>. Seluruh hak cipta dilindungi.
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="assets/js/script.js?v=2"></script>

</body>

</html>
