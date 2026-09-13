<?php

session_start();

require_once '../config/database.php';
require_once '../config/functions.php';

requireAdmin();

$uploadDir = '../uploads/parks/';
$dbDir = 'uploads/parks/';
$statusOptions = ['Aktif', 'Dalam Perawatan', 'Pasif'];

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

function deleteParkImage($path) {
    if (!$path || strpos($path, 'uploads/parks/') !== 0) return;
    $file = dirname(__DIR__) . '/' . $path;
    if (is_file($file)) @unlink($file);
}

function deleteGalleryImage($path) {
    deleteParkImage($path);
}

function uploadGalleryImages($files) {
    global $uploadDir, $dbDir;

    $result = [];

    if (!$files || empty($files['name']) || empty($files['name'][0])) {
        return $result;
    }

    $types = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp'
    ];

    $count = count($files['name']);

    for ($i = 0; $i < $count; $i++) {
        if ($files['error'][$i] === UPLOAD_ERR_NO_FILE) continue;

        if ($files['error'][$i] !== UPLOAD_ERR_OK) {
            throw new Exception('Salah satu foto galeri gagal diupload.');
        }

        if ($files['size'][$i] > 5 * 1024 * 1024) {
            throw new Exception('Ukuran setiap foto galeri maksimal 5 MB.');
        }

        $mime = mime_content_type($files['tmp_name'][$i]);

        if (!isset($types[$mime])) {
            throw new Exception('Format foto galeri harus JPG, PNG, atau WEBP.');
        }

        $name = 'park_' . bin2hex(random_bytes(10)) . '.' . $types[$mime];

        if (!move_uploaded_file($files['tmp_name'][$i], $uploadDir . $name)) {
            throw new Exception('Foto galeri gagal disimpan.');
        }

        $result[] = $dbDir . $name;
    }

    return $result;
}

function uploadParkImage($file) {
    global $uploadDir, $dbDir;

    if (!$file || $file['error'] === UPLOAD_ERR_NO_FILE) return null;

    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Upload foto gagal.');
    }

    if ($file['size'] > 5 * 1024 * 1024) {
        throw new Exception('Ukuran foto maksimal 5 MB.');
    }

    $types = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp'
    ];

    $mime = mime_content_type($file['tmp_name']);

    if (!isset($types[$mime])) {
        throw new Exception('Format foto harus JPG, PNG, atau WEBP.');
    }

    $name = 'park_' . bin2hex(random_bytes(10)) . '.' . $types[$mime];

    if (!move_uploaded_file($file['tmp_name'], $uploadDir . $name)) {
        throw new Exception('Foto gagal disimpan.');
    }

    return $dbDir . $name;
}

// ===============================
// PROSES DELETE (via POST)
// ===============================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    try {
        verifyCsrf();

        $id = (int) ($_POST['id'] ?? 0);

        $s = $conn->prepare("SELECT image FROM parks WHERE id=?");
        $s->bind_param("i", $id);
        $s->execute();
        $old = $s->get_result()->fetch_assoc();

        if ($old) {
            $gs = $conn->prepare("SELECT image FROM park_images WHERE park_id=?");
            $gs->bind_param("i", $id);
            $gs->execute();
            $gallery = $gs->get_result()->fetch_all(MYSQLI_ASSOC);

            $conn->prepare("DELETE FROM park_images WHERE park_id=?")->bind_param("i", $id);
            $conn->prepare("DELETE FROM park_images WHERE park_id=?")->execute();

            $ds = $conn->prepare("DELETE FROM parks WHERE id=?");
            $ds->bind_param("i", $id);
            $ds->execute();

            deleteParkImage($old['image']);
            foreach ($gallery as $img) {
                deleteGalleryImage($img['image']);
            }

            setFlash('success', 'Taman beserta fotonya berhasil dihapus.');
        }

    } catch (Throwable $e) {
        setFlash('error', $e->getMessage());
    }

    header("Location: parks.php");
    exit;
}

// ===============================
// PROSES CREATE / UPDATE
// ===============================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($_POST['action'] ?? '', ['create', 'update'])) {
    try {
        verifyCsrf();

        $action = $_POST['action'];

        $name = trim($_POST['name'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $opening = trim($_POST['opening_hours'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $plant_species = trim($_POST['plant_species'] ?? '');
        $facilities = trim($_POST['facilities'] ?? '');
        $map = trim($_POST['map_url'] ?? '');
        $latitudeRaw = trim($_POST['latitude'] ?? '');
        $longitudeRaw = trim($_POST['longitude'] ?? '');
        $latitude = $latitudeRaw === '' ? null : (float) $latitudeRaw;
        $longitude = $longitudeRaw === '' ? null : (float) $longitudeRaw;
        $employee_count = (int) ($_POST['employee_count'] ?? 0);
        $area = (float) ($_POST['area'] ?? 0);
        $status = trim($_POST['status'] ?? '');

        // Validasi wajib
        if (!$name || !$category || !$address || !$opening || !$description || !$plant_species) {
            throw new Exception('Lengkapi field wajib terlebih dahulu.');
        }

        // Validasi panjang
        if (mb_strlen($name) > 150) {
            throw new Exception('Nama taman maksimal 150 karakter.');
        }

        if (mb_strlen($category) > 80) {
            throw new Exception('Kategori maksimal 80 karakter.');
        }

        // Validasi angka
        if ($employee_count < 0 || $area < 0) {
            throw new Exception('Jumlah pegawai dan luas taman tidak boleh negatif.');
        }

        // Validasi koordinat
        if (($latitude === null) !== ($longitude === null)) {
            throw new Exception('Latitude dan longitude harus diisi keduanya atau dikosongkan.');
        }

        if ($latitude !== null && ($latitude < -90 || $latitude > 90)) {
            throw new Exception('Latitude harus antara -90 sampai 90.');
        }

        if ($longitude !== null && ($longitude < -180 || $longitude > 180)) {
            throw new Exception('Longitude harus antara -180 sampai 180.');
        }

        // Validasi status
        if (!in_array($status, $statusOptions, true)) {
            throw new Exception('Status taman tidak valid.');
        }

        // Validasi URL map
        if ($map !== '' && !filter_var($map, FILTER_VALIDATE_URL)) {
            throw new Exception('URL Google Maps tidak valid.');
        }

        if ($action === 'create') {
            $image = uploadParkImage($_FILES['image'] ?? null);

            if (!$image) {
                throw new Exception('Pilih foto taman terlebih dahulu.');
            }

            $s = $conn->prepare("
                INSERT INTO parks
                (name, category, address, opening_hours, description, plant_species, facilities, image, map_url, latitude, longitude, employee_count, area, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $s->bind_param(
                "sssssssssddids",
                $name, $category, $address, $opening, $description,
                $plant_species, $facilities, $image, $map,
                $latitude, $longitude, $employee_count, $area, $status
            );

            if (!$s->execute()) {
                throw new Exception('Gagal menyimpan taman: ' . $s->error);
            }

            $parkId = $conn->insert_id;

            $galleryImages = uploadGalleryImages($_FILES['gallery'] ?? null);

            if ($galleryImages) {
                $gs = $conn->prepare("INSERT INTO park_images (park_id, image, sort_order) VALUES (?, ?, ?)");
                foreach ($galleryImages as $i => $img) {
                    $gs->bind_param("isi", $parkId, $img, $i);
                    $gs->execute();
                }
            }

            setFlash('success', 'Taman "' . $name . '" berhasil ditambahkan.');
            header("Location: parks.php");
            exit;
        }

        if ($action === 'update') {
            $id = (int) ($_POST['id'] ?? 0);

            $s = $conn->prepare("SELECT image FROM parks WHERE id=?");
            $s->bind_param("i", $id);
            $s->execute();
            $old = $s->get_result()->fetch_assoc();

            if (!$old) {
                throw new Exception('Data taman tidak ditemukan.');
            }

            $image = uploadParkImage($_FILES['image'] ?? null);

            if ($image) {
                $s = $conn->prepare("
                    UPDATE parks
                    SET name=?, category=?, address=?, opening_hours=?,
                        description=?, plant_species=?, facilities=?, image=?, map_url=?,
                        latitude=?, longitude=?, employee_count=?, area=?, status=?
                    WHERE id=?
                ");

                $s->bind_param(
                    "sssssssssddidsi",
                    $name, $category, $address, $opening, $description,
                    $plant_species, $facilities, $image, $map,
                    $latitude, $longitude, $employee_count, $area, $status, $id
                );

                if (!$s->execute()) {
                    throw new Exception('Gagal memperbarui taman: ' . $s->error);
                }
                deleteParkImage($old['image']);

            } else {
                $s = $conn->prepare("
                    UPDATE parks
                    SET name=?, category=?, address=?, opening_hours=?,
                        description=?, plant_species=?, facilities=?, map_url=?,
                        latitude=?, longitude=?, employee_count=?, area=?, status=?
                    WHERE id=?
                ");

                $s->bind_param(
                    "ssssssssddidsi",
                    $name, $category, $address, $opening, $description,
                    $plant_species, $facilities, $map,
                    $latitude, $longitude, $employee_count, $area, $status, $id
                );

                if (!$s->execute()) {
                    throw new Exception('Gagal memperbarui taman: ' . $s->error);
                }
            }

            // Hapus foto galeri yang dicentang
            $deleteIds = array_filter(array_map('intval', $_POST['delete_gallery'] ?? []));

            if ($deleteIds) {
                $idList = implode(',', $deleteIds);
                $toDelete = $conn->query(
                    "SELECT image FROM park_images WHERE id IN ($idList) AND park_id = " . (int) $id
                )->fetch_all(MYSQLI_ASSOC);

                foreach ($toDelete as $img) {
                    deleteGalleryImage($img['image']);
                }

                $conn->query("DELETE FROM park_images WHERE id IN ($idList) AND park_id = " . (int) $id);
            }

            // Tambah foto galeri baru
            $newGalleryImages = uploadGalleryImages($_FILES['gallery'] ?? null);

            if ($newGalleryImages) {
                $countRow = $conn->query(
                    "SELECT COUNT(*) c FROM park_images WHERE park_id=" . (int) $id
                )->fetch_assoc();
                $startOrder = (int) $countRow['c'];

                $gs = $conn->prepare("INSERT INTO park_images (park_id, image, sort_order) VALUES (?, ?, ?)");

                foreach ($newGalleryImages as $i => $img) {
                    $order = $startOrder + $i;
                    $gs->bind_param("isi", $id, $img, $order);
                    $gs->execute();
                }
            }

            setFlash('success', 'Taman "' . $name . '" berhasil diperbarui.');
            header("Location: parks.php");
            exit;
        }

    } catch (Throwable $e) {
        setFlash('error', $e->getMessage());
        header("Location: parks.php" . (isset($id) && $id ? "?edit=$id" : ''));
        exit;
    }
}

// ===============================
// AMBIL DATA
// ===============================
$flash = getFlash();

$edit = null;
$editGallery = [];

if (isset($_GET['edit'])) {
    $id = (int) $_GET['edit'];
    $s = $conn->prepare("SELECT * FROM parks WHERE id=?");
    $s->bind_param("i", $id);
    $s->execute();
    $edit = $s->get_result()->fetch_assoc();

    if ($edit) {
        $gs = $conn->prepare("SELECT id, image FROM park_images WHERE park_id=? ORDER BY sort_order ASC, id ASC");
        $gs->bind_param("i", $edit['id']);
        $gs->execute();
        $editGallery = $gs->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}

$data = getAll($conn, "SELECT * FROM parks ORDER BY id DESC");

// Hitung rekapitulasi statistik data taman
$totalParks = count($data);
$totalArea = 0;
$totalEmployees = 0;
$statusCounts = ['Aktif' => 0, 'Dalam Perawatan' => 0, 'Pasif' => 0];
$categoryCounts = [];

foreach ($data as $p) {
    $totalArea += (float)($p['area'] ?? 0);
    $totalEmployees += (int)($p['employee_count'] ?? 0);
    $st = $p['status'] ?? 'Aktif';
    $cat = $p['category'] ?? 'Lainnya';

    if (isset($statusCounts[$st])) {
        $statusCounts[$st]++;
    } else {
        $statusCounts[$st] = 1;
    }

    if (!isset($categoryCounts[$cat])) {
        $categoryCounts[$cat] = 0;
    }
    $categoryCounts[$cat]++;
}
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= $edit ? 'Edit Taman' : 'Kelola Taman' ?> - Portal Taman</title>
<link rel="stylesheet" href="admin.css?v=9">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
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

    <a href="dashboard.php">
        <img src="../assets/icons/dashboard.png" class="nav-icon-img" alt="Dashboard">
        <span class="nav-text">Dashboard</span>
    </a>

    <a class="active" href="parks.php">
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

    <a class="nav-logout" href="logout.php">
        <?= icon('logout', '', 18) ?>
        <span class="nav-text">Keluar</span>
    </a>
</aside>

<!-- MAIN -->
<main>

<h1><?= $edit ? 'Edit Taman' : 'Kelola Taman' ?></h1>
<p class="page-subtitle"><?= $edit ? 'Perbarui data taman yang dipilih.' : 'Tambah, edit, atau hapus data taman.' ?></p>

<?php if ($flash): ?>
    <div class="notice <?= e($flash['type']) ?>">
        <span class="notice-icon"><?= $flash['type'] === 'success' ? icon('check', '', 20) : icon('alert', '', 20) ?></span>
        <?= e($flash['message']) ?>
    </div>
<?php endif; ?>

<!-- FORM -->
<section>
<h2><?= $edit ? 'Edit Data Taman' : 'Tambah Taman Baru' ?></h2>

<form method="post" enctype="multipart/form-data">
    <?= csrfField() ?>

    <input type="hidden" name="action" value="<?= $edit ? 'update' : 'create' ?>">

    <?php if ($edit): ?>
        <input type="hidden" name="id" value="<?= (int) $edit['id'] ?>">
    <?php endif; ?>

    <div class="form-group">
        <label for="name">Nama Taman *</label>
        <input id="name" name="name" value="<?= e($edit['name'] ?? '') ?>"
               placeholder="Masukkan nama taman" maxlength="150" required>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="category">Kategori *</label>
            <select id="category" name="category" required>
                <option value="" disabled <?= empty($edit['category']) ? 'selected' : '' ?>>Pilih kategori</option>
                <option value="Taman Kota" <?= ($edit['category'] ?? '') == 'Taman Kota' ? 'selected' : '' ?>>Taman Kota</option>
                <option value="Taman Keluarga" <?= ($edit['category'] ?? '') == 'Taman Keluarga' ? 'selected' : '' ?>>Taman Keluarga</option>
                <option value="Taman Olahraga" <?= ($edit['category'] ?? '') == 'Taman Olahraga' ? 'selected' : '' ?>>Taman Olahraga</option>
                <option value="Taman Tematik" <?= ($edit['category'] ?? '') == 'Taman Tematik' ? 'selected' : '' ?>>Taman Tematik</option>
            </select>
        </div>

        <div class="form-group">
            <label for="status">Status *</label>
            <select id="status" name="status" required>
                <option value="" disabled <?= empty($edit['status']) ? 'selected' : '' ?>>Pilih status</option>
                <?php foreach ($statusOptions as $opt): ?>
                    <option value="<?= e($opt) ?>" <?= ($edit['status'] ?? '') === $opt ? 'selected' : '' ?>><?= e($opt) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <!-- INTEGRASI GOOGLE MAPS & AUTO-FILL -->
    <div class="form-group map-integration-card">
        <label for="map_url" style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px;">
            <span style="display: inline-flex; align-items: center; gap: 8px; font-weight: 700; color: var(--text);">
                <?= icon('globe', '', 18) ?> Link Google Maps (Otomatis Isi Koordinat & Info)
            </span>
            <span class="auto-badge" id="mapAutoBadge" style="display: none;">
                <?= icon('check', '', 14) ?> Terekstrak Otomatis
            </span>
        </label>
        
        <div class="map-input-wrap">
            <input id="map_url" name="map_url" value="<?= e($edit['map_url'] ?? '') ?>"
                   placeholder="Tempel link Google Maps di sini (contoh: https://maps.app.goo.gl/... atau https://www.google.com/maps/...)"
                   type="url" autocomplete="off">
            <button type="button" id="btnExtractMap" class="btn-extract-map" title="Ekstrak data dari link Google Maps">
                <?= icon('globe', '', 16) ?> <span>Ekstrak Link</span>
            </button>
        </div>
        <span class="form-hint" id="mapExtractHint">
            Saat Anda menempelkan link Google Maps (termasuk link pendek <code>maps.app.goo.gl</code>), <strong>Latitude</strong>, <strong>Longitude</strong>, <strong>Alamat</strong>, dan <strong>Nama Taman</strong> akan terisi otomatis!
        </span>
        <div id="mapExtractStatus" class="map-extract-status" style="display: none;"></div>
    </div>

    <div class="coordinate-fields">
        <div class="form-group">
            <label for="latitude">Latitude</label>
            <input id="latitude" type="number" name="latitude"
                   value="<?= e($edit['latitude'] ?? '') ?>"
                   placeholder="Contoh: -7.795580" min="-90" max="90" step="any">
        </div>
        <div class="form-group">
            <label for="longitude">Longitude</label>
            <input id="longitude" type="number" name="longitude"
                   value="<?= e($edit['longitude'] ?? '') ?>"
                   placeholder="Contoh: 110.369490" min="-180" max="180" step="any">
        </div>
    </div>

    <!-- MINI MAP PREVIEW & PIN PICKER -->
    <div class="form-group">
        <label style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px;">
            <span style="display: inline-flex; align-items: center; gap: 6px; font-weight: 600;">
                <?= icon('map-pin', '', 16) ?> <span>Peta Titik Lokasi (Klik atau geser pin pada peta untuk menyesuaikan titik)</span>
            </span>
            <button type="button" id="btnCurrentLocation" class="btn-location-picker" title="Gunakan lokasi perangkat saat ini">
                📍 Lokasi Saat Ini
            </button>
        </label>
        <div id="adminMiniMap" class="admin-mini-map"></div>
    </div>

    <div class="form-group">
        <label for="address">Alamat *</label>
        <input id="address" name="address" value="<?= e($edit['address'] ?? '') ?>"
               placeholder="Alamat lengkap taman" required>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="opening_hours">Jam Buka *</label>
            <input id="opening_hours" name="opening_hours" value="<?= e($edit['opening_hours'] ?? '') ?>"
                   placeholder="Contoh: 06.00 - 21.00" required>
        </div>

        <div class="form-group">
            <label for="employee_count">Jumlah Pegawai</label>
            <input id="employee_count" type="number" name="employee_count"
                   value="<?= e($edit['employee_count'] ?? 0) ?>" min="0" step="1">
        </div>
    </div>

    <div class="form-group">
        <label for="area">Luas Taman (m²)</label>
        <input id="area" type="number" name="area"
               value="<?= e($edit['area'] ?? '') ?>" min="0" step="0.01"
               placeholder="Luas dalam meter persegi">
    </div>

    <div class="form-group">
        <label for="description_field">Deskripsi Taman *</label>
        <textarea id="description_field" name="description" placeholder="Deskripsi lengkap taman..." required><?= e($edit['description'] ?? '') ?></textarea>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="plant_species">Jenis Tanaman *</label>
            <span class="form-hint">Pisahkan dengan koma (,)</span>
            <input id="plant_species" name="plant_species" value="<?= e($edit['plant_species'] ?? '') ?>"
                   placeholder="Pohon trembesi, Pucuk merah, ..." required>
        </div>

        <div class="form-group">
            <label for="facilities">Fasilitas</label>
            <span class="form-hint">Pisahkan dengan koma (,)</span>
            <input id="facilities" name="facilities" value="<?= e($edit['facilities'] ?? '') ?>"
                   placeholder="Gazebo, Toilet, Parkir, ...">
        </div>
    </div>

    <!-- FOTO UTAMA -->
    <div class="form-group">
        <label>Foto Utama <?= $edit ? '' : '*' ?></label>
        <label class="upload-box">
            <div class="upload-box-icon">
                <?= icon('camera', '', 28) ?>
            </div>
            <strong>Klik untuk memilih foto</strong>
            <small>JPG, PNG, WEBP — maksimal 5 MB</small>

            <input id="image" type="file" name="image"
                   accept="image/jpeg,image/png,image/webp">

            <img id="preview" class="preview" alt="Preview foto">

            <button type="button" id="removePreview" class="remove-preview">
                ✕ Hapus foto pilihan
            </button>
        </label>

        <?php if ($edit && !empty($edit['image'])): ?>
            <small style="color: var(--text-muted);">Foto saat ini:</small>
            <img class="current-photo" src="../<?= e($edit['image']) ?>" alt="<?= e($edit['name']) ?>">
            <small style="color: var(--text-muted);">Jika tidak memilih foto baru, foto lama tetap digunakan.</small>
        <?php endif; ?>
    </div>

    <!-- FOTO GALERI -->
    <div class="form-group">
        <label>Foto Galeri</label>
        <div class="upload-box gallery-drop-zone" id="galleryDropZone">
            <div class="upload-box-icon">
                <?= icon('gallery', '', 28) ?>
            </div>
            <strong>Drag & Drop Foto Galeri</strong>
            <p>atau klik untuk memilih beberapa foto sekaligus</p>
            <small>JPG, PNG, WEBP — maks 5 MB per foto. Maks 5 foto.</small>

            <input id="gallery" type="file" name="gallery[]"
                   accept="image/jpeg,image/png,image/webp" multiple>
        </div>

        <div id="galleryPreview" class="gallery-preview"></div>

        <?php if ($edit && $editGallery): ?>
            <small style="color: var(--text-muted);">Foto galeri saat ini (centang untuk hapus):</small>
            <div class="gallery-current">
                <?php foreach ($editGallery as $g): ?>
                    <label class="gallery-item" style="cursor:pointer;">
                        <img src="../<?= e($g['image']) ?>" alt="">
                        <span>
                            <input type="checkbox" name="delete_gallery[]" value="<?= (int) $g['id'] ?>">
                            Hapus
                        </span>
                    </label>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="btn-group">
        <button type="submit">
            <?= $edit ? icon('save', '', 18) . ' Simpan Perubahan' : icon('plus', '', 18) . ' Tambah Taman' ?>
        </button>

        <?php if ($edit): ?>
            <a href="parks.php" class="btn btn-secondary">Batal</a>
        <?php endif; ?>
    </div>

</form>
</section>

<!-- MODAL CROP -->
<div id="cropModal" class="crop-modal">
    <div class="crop-modal-inner">
        <h3>Atur / Crop Foto</h3>
        <p class="crop-hint">Geser dan zoom untuk pilih bagian foto yang mau ditampilkan.</p>
        <div class="crop-container">
            <img id="cropImage" alt="Crop preview">
        </div>
        <div class="crop-actions">
            <button type="button" id="cropCancel" class="btn-cancel">Batal</button>
            <button type="button" id="cropApply" class="btn-save">✓ Terapkan</button>
        </div>
    </div>
</div>

<!-- REKAPITULASI & DAFTAR TAMAN -->
<section>

<div class="rekap-header-wrap">
    <div class="rekap-header-text">
        <h2>
            <?= icon('tree', '', 24) ?> Rekapitulasi Data Taman Kota
        </h2>
        <p>Ringkasan akumulasi seluruh taman, ruang terbuka hijau (RTH), dan tenaga pemeliharaan DLH Kota Batu.</p>
    </div>
    <div class="rekap-btn-group">
        <a href="export_rekap.php" class="btn-rekap btn-rekap-excel" title="Download rekapitulasi data taman dalam format Excel">
            <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <span>Excel Rekap</span>
        </a>
        <a href="print_rekap.php" target="_blank" rel="noopener noreferrer" class="btn-rekap btn-rekap-pdf" title="Buka pratinjau cetak dan simpan sebagai PDF ber-KOP resmi DLH">
            <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            <span>Cetak / PDF Rekap</span>
        </a>
    </div>
</div>

<!-- KARTU KPI REKAPITULASI -->
<div class="rekap-cards-grid">
    <!-- Card 1: Total Taman -->
    <div class="rekap-kpi-card">
        <div class="rekap-kpi-icon icon-emerald">
            <?= icon('tree', '', 24) ?>
        </div>
        <div class="rekap-kpi-info">
            <div class="rekap-kpi-label">Total Taman</div>
            <div class="rekap-kpi-val"><?= $totalParks ?></div>
            <div class="rekap-kpi-sub">Lokasi Ruang Terbuka Hijau</div>
        </div>
    </div>

    <!-- Card 2: Total Luas RTH -->
    <div class="rekap-kpi-card">
        <div class="rekap-kpi-icon icon-green">
            <svg style="width:24px;height:24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
        </div>
        <div class="rekap-kpi-info">
            <div class="rekap-kpi-label">Total Luas RTH</div>
            <div class="rekap-kpi-val"><?= number_format($totalArea, 0, ',', '.') ?> <span style="font-size: 0.85rem; font-weight: 600;">m²</span></div>
            <div class="rekap-kpi-sub">± <?= number_format($totalArea / 10000, 2, ',', '.') ?> Hektar</div>
        </div>
    </div>

    <!-- Card 3: Tenaga Kerja -->
    <div class="rekap-kpi-card">
        <div class="rekap-kpi-icon icon-blue">
            <?= icon('user', '', 22) ?>
        </div>
        <div class="rekap-kpi-info">
            <div class="rekap-kpi-label">Total Tenaga Kerja</div>
            <div class="rekap-kpi-val"><?= $totalEmployees ?> <span style="font-size: 0.85rem; font-weight: 600;">Orang</span></div>
            <div class="rekap-kpi-sub">Petugas Pemeliharaan Taman</div>
        </div>
    </div>

    <!-- Card 4: Status Operasional -->
    <div class="rekap-kpi-card">
        <div class="rekap-kpi-icon icon-amber">
            <?= icon('check', '', 22) ?>
        </div>
        <div class="rekap-kpi-info">
            <div class="rekap-kpi-label">Status Operasional</div>
            <div class="rekap-kpi-val" style="font-size: 1.1rem; margin-top: 4px;">
                <span style="color: #059669;"><?= $statusCounts['Aktif'] ?? 0 ?> Aktif</span>
                <span style="color: #d97706; margin-left: 4px;"><?= $statusCounts['Dalam Perawatan'] ?? 0 ?> Rawat</span>
            </div>
            <div class="rekap-kpi-sub"><?= $statusCounts['Pasif'] ?? 0 ?> Pasif / Non-Aktif</div>
        </div>
    </div>
</div>

<div class="park-search">
    <input type="search" id="parkSearch"
           placeholder="Cari nama, kategori, alamat, atau status..."
           aria-label="Cari taman">
    <span id="parkSearchCount" class="search-count"></span>
</div>

<div class="table-wrapper">
<table id="parkTable">

<thead>
<tr>
    <th>Foto</th>
    <th>Nama</th>
    <th>Kategori</th>
    <th>Alamat</th>
    <th>Pegawai</th>
    <th>Luas</th>
    <th>Status</th>
    <th>Aksi</th>
</tr>
</thead>

<tbody>
<?php foreach ($data as $p): ?>
<tr class="park-row"
    data-search="<?= e(strtolower(
        ($p['name'] ?? '') . ' ' .
        ($p['category'] ?? '') . ' ' .
        ($p['address'] ?? '') . ' ' .
        ($p['status'] ?? '')
    )) ?>">

    <td>
        <img class="thumb" src="../<?= e($p['image']) ?>" alt="<?= e($p['name']) ?>">
    </td>

    <td class="park-name"><?= e($p['name']) ?></td>
    <td class="park-category"><?= e($p['category']) ?></td>
    <td class="park-address"><?= e($p['address']) ?></td>
    <td style="text-align:center;"><?= (int) ($p['employee_count'] ?? 0) ?></td>
    <td style="text-align:center; white-space:nowrap;"><?= e(rtrim(rtrim(number_format((float) ($p['area'] ?? 0), 2, ',', '.'), '0'), ',')) ?> m²</td>

    <td>
        <span class="status-badge status-<?= e(strtolower(str_replace(' ', '-', $p['status'] ?? ''))) ?>">
            <?= e($p['status'] ?? '-') ?>
        </span>
    </td>

    <td>
        <div class="actions">
            <a href="parks.php?edit=<?= (int) $p['id'] ?>" class="btn-edit">
                <?= icon('edit', '', 14) ?> <span>Edit</span>
            </a>

            <form method="post" style="display:inline;" onsubmit="return confirm('Hapus taman <?= e($p['name']) ?> beserta fotonya?')">
                <?= csrfField() ?>
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
                <button type="submit" class="btn-del">
                    <?= icon('trash', '', 14) ?> <span>Hapus</span>
                </button>
            </form>
        </div>
    </td>

</tr>
<?php endforeach; ?>
</tbody>

<tfoot>
<tr class="table-total-row">
    <td colspan="4" style="text-align: right; font-weight: 800; color: #145f3c; letter-spacing: 0.5px;">TOTAL KESELURUHAN REKAPITULASI:</td>
    <td style="text-align: center; font-weight: 800; color: #145f3c;"><?= $totalEmployees ?> Org</td>
    <td style="text-align: center; font-weight: 800; white-space: nowrap; color: #145f3c;"><?= number_format($totalArea, 2, ',', '.') ?> m²</td>
    <td colspan="2" style="text-align: center; font-weight: 700; color: #059669;"><?= $totalParks ?> Taman Terdata</td>
</tr>
</tfoot>

</table>
</div>
</section>

</main>

<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
const input = document.getElementById('image');
const preview = document.getElementById('preview');
const removePreview = document.getElementById('removePreview');
const galleryInput = document.getElementById('gallery');
const galleryPreview = document.getElementById('galleryPreview');

function clearSelectedImage() {
    input.value = '';
    preview.removeAttribute('src');
    preview.style.display = 'none';
    removePreview.style.display = 'none';
}

// ===== MODAL CROP =====

const cropModal = document.getElementById('cropModal');
const cropImageEl = document.getElementById('cropImage');
const cropApplyBtn = document.getElementById('cropApply');
const cropCancelBtn = document.getElementById('cropCancel');

let cropper = null;

function openCropper(file) {
    return new Promise(function (resolve) {
        const reader = new FileReader();

        reader.onload = function (e) {
            cropImageEl.src = e.target.result;
            cropModal.classList.add('show');

            if (cropper) cropper.destroy();

            cropper = new Cropper(cropImageEl, {
                aspectRatio: 16 / 9,
                viewMode: 1,
                autoCropArea: 1,
                background: false
            });

            cropApplyBtn.onclick = function () {
                cropper.getCroppedCanvas({
                    width: 1200,
                    height: 675
                }).toBlob(function (blob) {
                    cropModal.classList.remove('show');
                    cropper.destroy();
                    cropper = null;

                    const croppedFile = new File(
                        [blob],
                        file.name.replace(/\.[^.]+$/, '') + '.jpg',
                        { type: 'image/jpeg' }
                    );
                    resolve(croppedFile);
                }, 'image/jpeg', 0.9);
            };

            cropCancelBtn.onclick = function () {
                cropModal.classList.remove('show');
                cropper.destroy();
                cropper = null;
                resolve(null);
            };
        };

        reader.readAsDataURL(file);
    });
}

// ===== FOTO UTAMA =====

input?.addEventListener('change', async function () {
    const file = this.files[0];

    if (!file) { clearSelectedImage(); return; }

    if (file.size > 5 * 1024 * 1024) {
        alert('Ukuran foto maksimal 5 MB.');
        clearSelectedImage();
        return;
    }

    if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) {
        alert('Format foto harus JPG, PNG, atau WEBP.');
        clearSelectedImage();
        return;
    }

    const cropped = await openCropper(file);

    if (!cropped) { clearSelectedImage(); return; }

    const dt = new DataTransfer();
    dt.items.add(cropped);
    input.files = dt.files;

    preview.src = URL.createObjectURL(cropped);
    preview.style.display = 'block';
    removePreview.style.display = 'block';
});

removePreview?.addEventListener('click', function (event) {
    event.preventDefault();
    event.stopPropagation();
    clearSelectedImage();
});

// ===== FOTO GALERI =====

const galleryDropZone = document.getElementById('galleryDropZone');
let selectedGalleryFiles = [];

function updateGalleryInput() {
    const dt = new DataTransfer();
    selectedGalleryFiles.forEach(function (file) { dt.items.add(file); });
    galleryInput.files = dt.files;
}

function renderGalleryPreview() {
    galleryPreview.innerHTML = '';

    selectedGalleryFiles.forEach(function (file, index) {
        const item = document.createElement('div');
        item.className = 'gallery-item';

        const img = document.createElement('img');
        img.src = URL.createObjectURL(file);

        const removeButton = document.createElement('button');
        removeButton.type = 'button';
        removeButton.className = 'remove-gallery-image';
        removeButton.innerHTML = '✕';
        removeButton.title = 'Hapus foto';

        removeButton.addEventListener('click', function () {
            selectedGalleryFiles.splice(index, 1);
            updateGalleryInput();
            renderGalleryPreview();
        });

        item.appendChild(img);
        item.appendChild(removeButton);
        galleryPreview.appendChild(item);
    });
}

async function addGalleryFiles(files) {
    const newFiles = [...files];

    for (const file of newFiles) {
        if (selectedGalleryFiles.length >= 5) {
            alert('Maksimal hanya 5 foto galeri.');
            break;
        }

        if (file.size > 5 * 1024 * 1024) {
            alert('Foto "' + file.name + '" dilewati karena lebih dari 5 MB.');
            continue;
        }

        if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) {
            alert('Foto "' + file.name + '" dilewati karena format tidak didukung.');
            continue;
        }

        const cropped = await openCropper(file);
        if (!cropped) continue;

        selectedGalleryFiles.push(cropped);
    }

    updateGalleryInput();
    renderGalleryPreview();
}

galleryDropZone?.addEventListener('click', function (event) {
    if (event.target.closest('.remove-gallery-image')) return;
    galleryInput?.click();
});

galleryInput?.addEventListener('change', async function () {
    await addGalleryFiles(this.files);
    galleryInput.value = '';
});

galleryDropZone?.addEventListener('dragover', function (event) {
    event.preventDefault();
    galleryDropZone.classList.add('dragover');
});

galleryDropZone?.addEventListener('dragenter', function (event) {
    event.preventDefault();
    galleryDropZone.classList.add('dragover');
});

galleryDropZone?.addEventListener('dragleave', function (event) {
    if (!galleryDropZone.contains(event.relatedTarget)) {
        galleryDropZone.classList.remove('dragover');
    }
});

galleryDropZone?.addEventListener('drop', async function (event) {
    event.preventDefault();
    galleryDropZone.classList.remove('dragover');
    await addGalleryFiles(event.dataTransfer.files);
});

// ===== GOOGLE MAPS AUTO-FILL & LEAFLET MINI MAP =====

const mapUrlInput = document.getElementById('map_url');
const btnExtractMap = document.getElementById('btnExtractMap');
const mapExtractStatus = document.getElementById('mapExtractStatus');
const mapAutoBadge = document.getElementById('mapAutoBadge');
const latInput = document.getElementById('latitude');
const lngInput = document.getElementById('longitude');
const nameInput = document.getElementById('name');
const addressInput = document.getElementById('address');
const btnCurrentLocation = document.getElementById('btnCurrentLocation');

function pulseField(el) {
    if (!el) return;
    el.classList.remove('field-highlight-success');
    void el.offsetWidth;
    el.classList.add('field-highlight-success');
}

// 1. Inisialisasi Leaflet Mini Map
let miniMap = null;
let marker = null;

function initAdminMap() {
    const mapEl = document.getElementById('adminMiniMap');
    if (!mapEl) return;

    let initLat = parseFloat(latInput.value);
    let initLng = parseFloat(lngInput.value);

    const hasValidCoords = !isNaN(initLat) && !isNaN(initLng) && initLat >= -90 && initLat <= 90 && initLng >= -180 && initLng <= 180;

    const defaultCenter = hasValidCoords ? [initLat, initLng] : [-7.871147, 112.527340];
    const defaultZoom = hasValidCoords ? 15 : 12;

    miniMap = L.map('adminMiniMap', {
        attributionControl: false
    }).setView(defaultCenter, defaultZoom);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19
    }).addTo(miniMap);

    if (hasValidCoords) {
        marker = L.marker([initLat, initLng], { draggable: true }).addTo(miniMap);
        marker.on('dragend', function (e) {
            const pos = e.target.getLatLng();
            updateCoordsFromMap(pos.lat, pos.lng);
        });
    }

    miniMap.on('click', function (e) {
        const lat = e.latlng.lat;
        const lng = e.latlng.lng;
        setMapMarker(lat, lng);
        updateCoordsFromMap(lat, lng);
    });
}

function updateCoordsFromMap(lat, lng) {
    latInput.value = lat.toFixed(6);
    lngInput.value = lng.toFixed(6);
    pulseField(latInput);
    pulseField(lngInput);
}

function setMapMarker(lat, lng, zoom = 15) {
    if (!miniMap) return;
    if (marker) {
        marker.setLatLng([lat, lng]);
    } else {
        marker = L.marker([lat, lng], { draggable: true }).addTo(miniMap);
        marker.on('dragend', function (e) {
            const pos = e.target.getLatLng();
            updateCoordsFromMap(pos.lat, pos.lng);
        });
    }
    miniMap.setView([lat, lng], zoom);
}

function onManualCoordChange() {
    const lat = parseFloat(latInput.value);
    const lng = parseFloat(lngInput.value);
    if (!isNaN(lat) && !isNaN(lng) && lat >= -90 && lat <= 90 && lng >= -180 && lng <= 180) {
        setMapMarker(lat, lng);
    }
}
latInput?.addEventListener('input', onManualCoordChange);
lngInput?.addEventListener('input', onManualCoordChange);

// 2. Client-side Regex Parser (Instan jika URL lengkap)
function parseGoogleMapsClient(url) {
    if (!url) return null;

    // Jika URL pendek, wajib resolve ke backend
    if (url.includes('maps.app.goo.gl') || url.includes('goo.gl/maps') || url.includes('bit.ly')) {
        return null;
    }

    let lat = null, lng = null, name = null;

    // Prioritas 0: Format Dropped Pin / Titik Baru (/maps/search/-7.912416,+112.577562)
    let m = url.match(/\/maps\/search\/(-?\d+\.\d+)[,\+](?:\+|%20|\s)*(-?\d+\.\d+)/i);
    if (m) {
        lat = parseFloat(m[1]);
        lng = parseFloat(m[2]);
    }

    // Prioritas 1: Titik Pin Tempat (!3d-7.9096497!4d112.5670425)
    if (lat === null) {
        m = url.match(/!3d(-?\d+\.\d+)!4d(-?\d+\.\d+)/);
        if (m) {
            lat = parseFloat(m[1]);
            lng = parseFloat(m[2]);
        }
    }

    // Prioritas 2: Viewport Camera (@-7.9144772,112.5442967)
    if (lat === null) {
        m = url.match(/@(-?\d+\.\d+),(-?\d+\.\d+)/);
        if (m) {
            lat = parseFloat(m[1]);
            lng = parseFloat(m[2]);
        }
    }

    // Prioritas 3: Parameter q= atau ll= (?q=-7.912416,112.577562 atau ?q=-7.912416,+112.577562)
    if (lat === null) {
        m = url.match(/[?&](?:q|ll)=(-?\d+\.\d+)[,\+](?:\+|%20|\s)*(-?\d+\.\d+)/);
        if (m) {
            lat = parseFloat(m[1]);
            lng = parseFloat(m[2]);
        }
    }

    // Nama Tempat: /maps/place/Nama+Tempat/
    m = url.match(/\/maps\/place\/([^/@?]+)/);
    if (m) {
        try {
            name = decodeURIComponent(m[1].replace(/\+/g, ' ')).trim();
        } catch(e) {}
    }

    return (lat !== null && lng !== null) ? { latitude: lat, longitude: lng, name: name } : null;
}

// 3. Handler Utama Ekstraksi Google Maps
async function handleExtractMapUrl(isManualClick = false) {
    const rawUrl = (mapUrlInput.value || '').trim();
    if (!rawUrl) {
        if (isManualClick) {
            showMapStatus('error', 'Silakan tempel link Google Maps terlebih dahulu.');
        }
        return;
    }

    // Coba parsing langsung di browser jika link lengkap
    const clientData = parseGoogleMapsClient(rawUrl);
    if (clientData) {
        applyExtractedData(clientData);
        showMapStatus('success', `✓ Sukses! Koordinat (${clientData.latitude}, ${clientData.longitude}) ${clientData.name ? 'dan nama "' + clientData.name + '"' : ''} terekstrak otomatis.`);
        return;
    }

    // Panggil backend resolver untuk membuka redirect link pendek (maps.app.goo.gl)
    btnExtractMap.classList.add('loading');
    btnExtractMap.disabled = true;
    showMapStatus('loading', '⏳ Sedang menyelesaikan link dan mengekstrak koordinat dari Google Maps...');

    try {
        const response = await fetch(`resolve_map.php?url=${encodeURIComponent(rawUrl)}`);
        const result = await response.json();

        if (result.success && result.latitude !== null && result.longitude !== null) {
            applyExtractedData(result);
            showMapStatus('success', `✓ Sukses! Koordinat (${result.latitude}, ${result.longitude}) ${result.name ? 'dan nama "' + result.name + '"' : ''} berhasil diekstrak!`);
        } else {
            showMapStatus('error', result.message || 'Tidak dapat mendeteksi koordinat dari link tersebut. Anda dapat memilih titik langsung pada peta di bawah.');
        }
    } catch (err) {
        showMapStatus('error', 'Gagal memproses link Google Maps. Silakan periksa koneksi internet atau pilih titik di peta.');
    } finally {
        btnExtractMap.classList.remove('loading');
        btnExtractMap.disabled = false;
    }
}

function applyExtractedData(data) {
    if (data.latitude !== null && data.longitude !== null) {
        latInput.value = data.latitude;
        lngInput.value = data.longitude;
        pulseField(latInput);
        pulseField(lngInput);
        setMapMarker(data.latitude, data.longitude, 16);
    }

    if (data.name) {
        nameInput.value = data.name;
        pulseField(nameInput);
    }

    if (data.address) {
        addressInput.value = data.address;
        pulseField(addressInput);
    }

    if (data.final_url && mapUrlInput.value !== data.final_url) {
        mapUrlInput.value = data.final_url;
        pulseField(mapUrlInput);
    }

    if (mapAutoBadge) {
        mapAutoBadge.style.display = 'inline-flex';
    }
}

function showMapStatus(type, message) {
    mapExtractStatus.className = `map-extract-status ${type}`;
    mapExtractStatus.innerHTML = message;
    mapExtractStatus.style.display = 'block';

    if (type === 'success') {
        setTimeout(() => {
            mapExtractStatus.style.display = 'none';
        }, 6000);
    }
}

let mapUrlDebounce = null;
mapUrlInput?.addEventListener('input', function () {
    clearTimeout(mapUrlDebounce);
    mapUrlDebounce = setTimeout(() => {
        handleExtractMapUrl(false);
    }, 400);
});

mapUrlInput?.addEventListener('paste', function () {
    setTimeout(() => {
        handleExtractMapUrl(false);
    }, 100);
});

btnExtractMap?.addEventListener('click', function () {
    handleExtractMapUrl(true);
});

btnCurrentLocation?.addEventListener('click', function () {
    if (!navigator.geolocation) {
        alert('Geolocation tidak didukung oleh browser Anda.');
        return;
    }
    btnCurrentLocation.textContent = '⏳ Mendeteksi...';
    navigator.geolocation.getCurrentPosition(
        function (pos) {
            btnCurrentLocation.textContent = '📍 Lokasi Saat Ini';
            const lat = pos.coords.latitude;
            const lng = pos.coords.longitude;
            updateCoordsFromMap(lat, lng);
            setMapMarker(lat, lng, 16);
            showMapStatus('success', `✓ Lokasi perangkat terdeteksi (${lat.toFixed(6)}, ${lng.toFixed(6)})!`);
        },
        function (err) {
            btnCurrentLocation.textContent = '📍 Lokasi Saat Ini';
            alert('Tidak dapat mendeteksi lokasi: ' + err.message);
        },
        { enableHighAccuracy: true, timeout: 8000 }
    );
});

// Inisialisasi peta saat load
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAdminMap);
} else {
    initAdminMap();
}

// ===== SEARCH TABLE =====

const parkSearch = document.getElementById('parkSearch');
const parkRows = Array.from(document.querySelectorAll('#parkTable .park-row'));
const parkSearchCount = document.getElementById('parkSearchCount');

function filterParks() {
    const keyword = (parkSearch.value || '').trim().toLowerCase();
    let visible = 0;

    parkRows.forEach(row => {
        const matched = !keyword || row.dataset.search.includes(keyword);
        row.style.display = matched ? '' : 'none';
        if (matched) visible++;
    });

    parkSearchCount.textContent = keyword
        ? `${visible} taman ditemukan`
        : `${visible} taman`;
}

if (parkSearch) {
    parkSearch.addEventListener('input', filterParks);
    filterParks();
}
</script>

</body>
</html>
