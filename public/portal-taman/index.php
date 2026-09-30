<?php

require_once 'config/database.php';
require_once 'config/functions.php';

// Ambil informasi kota
$city = getOne(
    $conn,
    "SELECT * FROM city_info LIMIT 1"
);

// Ambil parameter pencarian
$q = trim($_GET['q'] ?? '');
$category = trim($_GET['category'] ?? '');

// Pengaturan Pagination (6 taman per halaman)
$perPage = 6;

// Hitung total taman sesuai filter
$countSql = "SELECT COUNT(*) as total FROM parks WHERE 1";
$countParams = [];
$countTypes = "";

if ($category !== '') {
    $countSql .= " AND category = ?";
    $countParams[] = $category;
    $countTypes .= "s";
}

$countStmt = $conn->prepare($countSql);
if ($countParams) {
    $countStmt->bind_param($countTypes, ...$countParams);
}
$countStmt->execute();
$totalParks = (int)($countStmt->get_result()->fetch_assoc()['total'] ?? 0);
$totalPages = max(1, (int)ceil($totalParks / $perPage));

$page = (int)($_GET['page'] ?? 1);
if ($page < 1) $page = 1;
if ($page > $totalPages) $page = $totalPages;

$offset = ($page - 1) * $perPage;

// Query daftar taman untuk halaman saat ini (6 data)
$sql = "SELECT * FROM parks WHERE 1";
$params = [];
$types = "";

if ($category !== '') {
    $sql .= " AND category = ?";
    $params[] = $category;
    $types .= "s";
}

$sql .= " ORDER BY name ASC LIMIT ? OFFSET ?";
$params[] = $perPage;
$params[] = $offset;
$types .= "ii";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();

$parks = $stmt
    ->get_result()
    ->fetch_all(MYSQLI_ASSOC);

// Ambil semua taman yang memiliki koordinat untuk peta sebaran publik
$mapParks = getAll(
    $conn,
    "SELECT id, name, address, latitude, longitude, status, image, category, opening_hours
     FROM parks
     WHERE latitude IS NOT NULL AND longitude IS NOT NULL
       AND latitude <> '' AND longitude <> ''
     ORDER BY name ASC"
);

// Ambil daftar kategori
$categories = getAll(
    $conn,
    "SELECT DISTINCT category
     FROM parks
     WHERE category <> ''
     ORDER BY category"
);

// Statistik rekapitulasi data taman untuk informasi publik
$publicStats = getOne($conn, "SELECT COUNT(*) as c, COALESCE(SUM(area), 0) as total_area, COALESCE(SUM(employee_count), 0) as total_emp FROM parks");
$publicTotalParks = (int) ($publicStats['c'] ?? 0);
$publicTotalArea = (float) ($publicStats['total_area'] ?? 0);
$publicTotalEmployees = (int) ($publicStats['total_emp'] ?? 0);

// Helper URL Halaman
if (!function_exists('getPageUrl')) {
    function getPageUrl($pageNum, $categoryFilter) {
        $p = [];
        if ($categoryFilter !== '') $p['category'] = $categoryFilter;
        if ($pageNum > 1) $p['page'] = $pageNum;
        $qs = http_build_query($p);
        return 'index.php' . ($qs ? '?' . $qs : '') . '#taman';
    }
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$isAdminLoggedIn = !empty($_SESSION['admin']);

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>
        Taman <?= e($city['city_name'] ?? 'Kota') ?>
    </title>

    <link rel="stylesheet" href="assets/css/model.css?v=8">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
</head>

<body>

    <!-- =========================
         NAVBAR
    ========================== -->
    <header class="nav">

        <div class="container nav-in">

            <a class="logo" href="index.php" style="display: inline-flex; align-items: center; gap: 9px;">
                <img src="assets/icons/logo.png" alt="Logo" style="width: 36px; height: 36px; border-radius: 50%; object-fit: contain;">
                <span><?= e($city['city_name'] ?? 'Kota') ?> Parks</span>
            </a>

            <button id="menu">
                ☰
            </button> 

            <nav id="links">
                <a href="#home">Beranda</a>
                <a href="#taman">Daftar Taman</a>
                <a href="#tentang">Tentang</a>
                <a href="#kontak">Kontak</a>
            </nav>

        </div>

    </header>


    <!-- =========================
         HERO
    ========================== -->
    <section class="hero" id="home">

        <div class="container">

            <p class="eyebrow">
                PORTAL INFORMASI TAMAN KOTA
            </p>

            <h1>
                Temukan taman terbaik<br>
                di <span><?= e($city['city_name'] ?? 'kota Anda') ?>.</span>
            </h1>

            <p>
                <?= e(
                    $city['description']
                    ?? 'Temukan lokasi, fasilitas, jam buka, dan informasi taman di kota Anda.'
                ) ?>
            </p>


        </div>

    </section>

    <!-- =========================
         REKAPITULASI RTH KOTA
    ========================== -->
    <section class="rekap-public-section" style="margin-top: -30px; position: relative; z-index: 10;">
        <div class="container">
            <div style="background: #ffffff; border-radius: 16px; padding: 20px 28px; box-shadow: 0 10px 30px -5px rgba(0,0,0,0.08); border: 1px solid #e2e8f0; display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; align-items: center;">
                
                <div style="display: flex; align-items: center; gap: 14px;">
                    <div style="width: 46px; height: 46px; border-radius: 12px; background: #ecfdf5; display: flex; align-items: center; justify-content: center; color: #059669; font-size: 22px;">
                        🌳
                    </div>
                    <div>
                        <div style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b;">Total Taman Kota</div>
                        <div style="font-size: 1.35rem; font-weight: 800; color: #0f172a;"><?= $publicTotalParks ?> <span style="font-size: 0.9rem; font-weight: 600; color: #64748b;">Lokasi</span></div>
                    </div>
                </div>

                <div style="display: flex; align-items: center; gap: 14px;">
                    <div style="width: 46px; height: 46px; border-radius: 12px; background: #eff6ff; display: flex; align-items: center; justify-content: center; color: #2563eb; font-size: 22px;">
                        📐
                    </div>
                    <div>
                        <div style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b;">Luas Ruang Terbuka Hijau</div>
                        <div style="font-size: 1.35rem; font-weight: 800; color: #0f172a;"><?= number_format($publicTotalArea, 0, ',', '.') ?> <span style="font-size: 0.85rem; font-weight: 600; color: #64748b;">m²</span></div>
                    </div>
                </div>

                <div style="display: flex; align-items: center; gap: 14px;">
                    <div style="width: 46px; height: 46px; border-radius: 12px; background: #fef3c7; display: flex; align-items: center; justify-content: center; color: #d97706; font-size: 22px;">
                        👷
                    </div>
                    <div>
                        <div style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b;">Petugas Pemeliharaan</div>
                        <div style="font-size: 1.35rem; font-weight: 800; color: #0f172a;"><?= $publicTotalEmployees ?> <span style="font-size: 0.85rem; font-weight: 600; color: #64748b;">Tenaga Kerja</span></div>
                    </div>
                </div>

                <div style="display: flex; align-items: center; gap: 14px;">
                    <div style="width: 46px; height: 46px; border-radius: 12px; background: #f3e8ff; display: flex; align-items: center; justify-content: center; color: #7c3aed; font-size: 22px;">
                        🏷️
                    </div>
                    <div>
                        <div style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b;">Kategori Taman</div>
                        <div style="font-size: 1.35rem; font-weight: 800; color: #0f172a;"><?= count($categories) ?> <span style="font-size: 0.85rem; font-weight: 600; color: #64748b;">Jenis Variasi</span></div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- =========================
         PETA SEBARAN TAMAN
    ========================== -->
    <section class="map-section" id="peta-taman">

        <div class="container">

            <div class="map-heading">

                <div class="map-title-group">
                    <h2><span class="map-heading-icon" aria-hidden="true">⌖</span> Peta Sebaran Lokasi Taman Kota</h2>
                    <p>Klik marker pada peta untuk melihat informasi lokasi dan detail taman.</p>
                </div>

                <div class="map-legend" aria-label="Keterangan status taman">
                    <span><i class="legend-dot aktif"></i> Aktif</span>
                    <span><i class="legend-dot pasif"></i> Pasif</span>
                    <span><i class="legend-dot perawatan"></i> Perawatan</span>
                </div>

            </div>

            <div id="parksMap" class="parks-map"></div>

            <div id="mapEmptyNotice" class="map-empty-notice">
                Belum ada taman yang memiliki koordinat. Silakan isi Latitude dan Longitude dari halaman Admin &gt; Kelola Taman.
            </div>

        </div>

    </section>




    


    <!-- =========================
         DAFTAR TAMAN
    ========================== -->
    <section class="section" id="taman">

        <div class="container">

            <!-- Heading + Pencarian Realtime -->
            <div class="heading directory-heading">

                <div>
                    <p class="label">
                        JELAJAHI
                    </p>

                    <h2>
                        Daftar taman
                    </h2>
                    <p class="directory-subtitle">Cari taman berdasarkan nama, lokasi, kategori, atau informasi lainnya.</p>
                </div>

                <div class="directory-search-wrap">
                    <label class="live-search" for="parkLiveSearch">
                        <span class="live-search-icon" aria-hidden="true">⌕</span>
                        <input
                            id="parkLiveSearch"
                            type="search"
                            autocomplete="off"
                            placeholder="Cari taman atau lokasi..."
                            value="<?= e($q) ?>"
                        >
                        <button type="button" id="clearParkSearch" class="clear-search" aria-label="Hapus pencarian" title="Hapus pencarian">×</button>
                    </label>
                    <span id="parkResultCount" class="park-result-count" aria-live="polite"><?= count($parks) ?> taman ditemukan</span>
                </div>

            </div>


            <!-- Filter Kategori -->
            <div class="filters">

                <a
                    class="<?= $category === '' ? 'active' : '' ?>"
                    href="index.php#taman"
                >
                    Semua
                </a>

                <?php foreach ($categories as $c): ?>

                    <a
                        class="<?= $category === $c['category'] ? 'active' : '' ?>"
                        href="?category=<?= urlencode($c['category']) ?>#taman"
                    >
                        <?= e($c['category']) ?>
                    </a>

                <?php endforeach; ?>

            </div>


            <!-- Grid Taman -->
            <div class="grid">

                <?php foreach ($parks as $p): ?>

                    <article
                            class="park"
                            data-park-search="<?= e(strtolower(trim(($p['name'] ?? '') . ' ' . ($p['address'] ?? '') . ' ' . ($p['description'] ?? '') . ' ' . ($p['category'] ?? '') . ' ' . ($p['status'] ?? '')))) ?>"
                        >

                        <!-- Foto -->
                        <div class="photo">

                            <img
                                src="<?= e($p['image']) ?>"
                                alt="<?= e($p['name']) ?>"
                            >

                            <span>
                                <?= e($p['category']) ?>
                            </span>

                        </div>


                        <!-- Informasi Taman -->
                        <div class="park-body">

                            <h3>
                                <?= e($p['name']) ?>
                            </h3>

                            <p class="address">
                                <?= icon('map-pin', '', 14) ?> <?= e($p['address']) ?>
                            </p>

                            <p>
                                <?= e($p['description']) ?>
                            </p>


                            <!-- Meta -->
                            <div class="meta">

                                <span>
                                    <?= icon('clock', '', 14) ?> <?= e($p['opening_hours']) ?> WIB
                                </span>

                                <div class="park-actions">
                                    <a class="detail-link" href="detail.php?id=<?= $p['id'] ?>">
                                        Detail
                                    </a>

                                    <button
                                        type="button"
                                        class="map-focus-btn <?= ($p['latitude'] === null || $p['longitude'] === null || $p['latitude'] === '' || $p['longitude'] === '') ? 'disabled' : '' ?>"
                                        data-park-id="<?= (int) $p['id'] ?>"
                                        aria-label="Tampilkan <?= e($p['name']) ?> di peta"
                                        title="<?= ($p['latitude'] === null || $p['longitude'] === null || $p['latitude'] === '' || $p['longitude'] === '') ? 'Koordinat belum tersedia' : 'Lihat lokasi di peta' ?>"
                                        <?= ($p['latitude'] === null || $p['longitude'] === null || $p['latitude'] === '' || $p['longitude'] === '') ? 'disabled' : '' ?>
                                    >
                                        ➤
                                    </button>
                                </div>

                            </div>

                        </div>

                    </article>

                <?php endforeach; ?>

            </div>

            <!-- Navigasi Halaman (Pagination) -->
            <?php if ($totalPages > 1): ?>
                <nav class="pagination-nav" aria-label="Navigasi Halaman Taman">
                    <div class="pagination">

                        <!-- Tombol Sebelumnya -->
                        <?php if ($page > 1): ?>
                            <a href="<?= e(getPageUrl($page - 1, $category)) ?>" class="page-btn prev-btn" aria-label="Halaman Sebelumnya">
                                ‹ Sebelumnya
                            </a>
                        <?php else: ?>
                            <span class="page-btn prev-btn disabled" aria-disabled="true">
                                ‹ Sebelumnya
                            </span>
                        <?php endif; ?>

                        <!-- Nomor Halaman -->
                        <div class="page-numbers">
                            <?php
                            $startPage = max(1, $page - 2);
                            $endPage = min($totalPages, $page + 2);

                            if ($startPage > 1) {
                                echo '<a href="' . e(getPageUrl(1, $category)) . '" class="page-num">1</a>';
                                if ($startPage > 2) {
                                    echo '<span class="page-dots">…</span>';
                                }
                            }

                            for ($i = $startPage; $i <= $endPage; $i++) {
                                if ($i === $page) {
                                    echo '<span class="page-num active" aria-current="page">' . $i . '</span>';
                                } else {
                                    echo '<a href="' . e(getPageUrl($i, $category)) . '" class="page-num">' . $i . '</a>';
                                }
                            }

                            if ($endPage < $totalPages) {
                                if ($endPage < $totalPages - 1) {
                                    echo '<span class="page-dots">…</span>';
                                }
                                echo '<a href="' . e(getPageUrl($totalPages, $category)) . '" class="page-num">' . $totalPages . '</a>';
                            }
                            ?>
                        </div>

                        <!-- Tombol Selanjutnya -->
                        <?php if ($page < $totalPages): ?>
                            <a href="<?= e(getPageUrl($page + 1, $category)) ?>" class="page-btn next-btn" aria-label="Halaman Selanjutnya">
                                Selanjutnya ›
                            </a>
                        <?php else: ?>
                            <span class="page-btn next-btn disabled" aria-disabled="true">
                                Selanjutnya ›
                            </span>
                        <?php endif; ?>

                    </div>

                    <div class="pagination-info">
                        Menampilkan <strong><?= count($parks) ?></strong> dari total <strong><?= $totalParks ?></strong> taman (Halaman <?= $page ?> dari <?= $totalPages ?>)
                    </div>
                </nav>
            <?php endif; ?>

            <div id="liveSearchEmpty" class="live-search-empty" hidden>
                <span aria-hidden="true">⌕</span>
                <h3>Taman tidak ditemukan</h3>
                <p>Coba gunakan kata kunci lain atau hapus pencarian.</p>
            </div>


            <!-- Jika Tidak Ada Taman -->
            <?php if (!$parks): ?>

                <div class="empty">
                    Taman yang dicari belum ditemukan.
                </div>

            <?php endif; ?>

        </div>

    </section>


    <!-- =========================
         TENTANG PORTAL
    ========================== -->
    <section class="about" id="tentang">

        <div class="container about-grid">

            <div>

                <p class="label">
                    TENTANG PORTAL
                </p>

                <h2>
                    Satu tempat untuk menemukan ruang hijau kota.
                </h2>

                <p>
                    <?= e($city['about'] ?? '') ?>
                </p>

            </div>


            <div class="about-box">

                <img src="assets/icons/logo.png" alt="Logo Taman" class="about-logo-img">

                <strong>
                    Informasi lengkap
                </strong>

                <span>
                    Lokasi • Fasilitas • Jam buka • Foto
                </span>

            </div>

        </div>

    </section>


    <!-- =========================
         FOOTER
    ========================== -->
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
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
    const parksMapData = <?= json_encode(
        $mapParks,
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES |
        JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
    ) ?>;

    (function () {
        const mapElement = document.getElementById('parksMap');
        const emptyNotice = document.getElementById('mapEmptyNotice');

        if (!mapElement || !window.L) return;

        if (!parksMapData.length) {
            mapElement.style.display = 'none';
            if (emptyNotice) emptyNotice.style.display = 'block';
            return;
        }

        if (emptyNotice) emptyNotice.style.display = 'none';

        const first = parksMapData[0];
        const map = L.map('parksMap', {
            // Agar scroll halaman tidak membuat peta ikut zoom
            scrollWheelZoom: false
        }).setView([Number(first.latitude), Number(first.longitude)], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        const markers = {};

        function statusInfo(status) {
            const value = String(status || '').toLowerCase();
            if (value.includes('perawatan')) {
                return { marker: 'marker-perawatan', badge: 'badge-perawatan', label: 'Perawatan' };
            }
            if (value.includes('pasif') || value.includes('ditutup')) {
                return { marker: 'marker-pasif', badge: 'badge-pasif', label: 'Pasif' };
            }
            return { marker: 'marker-aktif', badge: 'badge-aktif', label: 'Aktif' };
        }

        function escapeHtml(value) {
            return String(value ?? '').replace(/[&<>"']/g, (char) => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            }[char]));
        }

        parksMapData.forEach((park) => {
            const lat = Number(park.latitude);
            const lng = Number(park.longitude);
            if (!Number.isFinite(lat) || !Number.isFinite(lng)) return;

            const status = statusInfo(park.status);
            const icon = L.divIcon({
                className: 'custom-park-marker',
                html: `
                    <div class="park-marker-circle ${status.marker}" aria-label="${escapeHtml(park.name || 'Lokasi taman')}">
                        <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                            <path d="M12 3.5c-2.7 0-4.9 2.2-4.9 4.9 0 1.9 1.1 3.5 2.6 4.3L7.9 17h3.1v3.5h2V17h3.1l-1.8-4.3c1.5-.8 2.6-2.4 2.6-4.3 0-2.7-2.2-4.9-4.9-4.9Zm0 2.1a2.8 2.8 0 1 1 0 5.6 2.8 2.8 0 0 1 0-5.6Z"/>
                        </svg>
                    </div>`,
                iconSize: [46, 46],
                iconAnchor: [23, 23],
                popupAnchor: [0, -25]
            });

            const image = escapeHtml(park.image || '');
            const popup = `
                <article class="map-popup-card">
                    <div class="map-popup-image-wrap">
                        ${image ? `<img class="map-popup-image" src="${image}" alt="${escapeHtml(park.name || 'Foto taman')}">` : '<div class="map-popup-image placeholder">🌳</div>'}
                        <span class="map-popup-status ${status.badge}">${status.label}</span>
                    </div>
                    <div class="map-popup-content">
                        <h3>${escapeHtml(park.name || 'Taman')}</h3>
                        <p class="map-popup-address">${escapeHtml(park.address || 'Alamat belum tersedia')}</p>
                        <a href="detail.php?id=${Number(park.id)}" class="map-popup-detail">Lihat Detail Lengkap</a>
                    </div>
                </article>
            `;

            const marker = L.marker([lat, lng], { icon, keyboard: true }).addTo(map);
            marker.bindPopup(popup, {
                className: 'park-info-popup',
                closeButton: false,
                maxWidth: 270,
                minWidth: 270,
                autoPanPadding: [28, 28]
            });

            markers[String(park.id)] = marker;
        });

        if (Object.keys(markers).length > 1) {
            const bounds = Object.values(markers).map(marker => marker.getLatLng());
            map.fitBounds(bounds, { padding: [55, 55], maxZoom: 14 });
        }

        document.querySelectorAll('.map-focus-btn:not([disabled])').forEach((button) => {
            button.addEventListener('click', () => {
                const marker = markers[String(button.dataset.parkId)];
                if (!marker) return;

                document.getElementById('peta-taman').scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });

                window.setTimeout(() => {
                    map.invalidateSize();
                    map.flyTo(marker.getLatLng(), 17, {
                        animate: true,
                        duration: 1.1
                    });
                    marker.openPopup();
                }, 500);
            });
        });

    })();
    </script>

    <script>
    // Pencarian taman secara realtime tanpa reload halaman
    (function () {
        const input = document.getElementById('parkLiveSearch');
        const clearButton = document.getElementById('clearParkSearch');
        const cards = Array.from(document.querySelectorAll('.park[data-park-search]'));
        const resultCount = document.getElementById('parkResultCount');
        const emptyState = document.getElementById('liveSearchEmpty');

        if (!input || !cards.length) return;

        function normalize(value) {
            return String(value || '')
                .toLocaleLowerCase('id-ID')
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '');
        }

        function filterParks() {
            const keyword = normalize(input.value.trim());
            let visible = 0;

            cards.forEach((card) => {
                const searchableText = normalize(card.dataset.parkSearch);
                const match = !keyword || searchableText.includes(keyword);
                card.hidden = !match;
                if (match) visible++;
            });

            if (resultCount) {
                resultCount.textContent = `${visible} taman ditemukan`;
            }

            if (emptyState) {
                emptyState.hidden = visible !== 0;
            }

            if (clearButton) {
                clearButton.classList.toggle('show', input.value.length > 0);
            }
        }

        input.addEventListener('input', filterParks);
        input.addEventListener('search', filterParks);

        clearButton?.addEventListener('click', () => {
            input.value = '';
            filterParks();
            input.focus();
        });

        filterParks();
    })();
    </script>

    <script src="assets/js/script.js?v=3"></script>

</body>

</html>