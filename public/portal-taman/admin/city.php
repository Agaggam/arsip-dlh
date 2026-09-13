<?php

session_start();

require_once '../config/database.php';
require_once '../config/functions.php';

requireAdmin();

$c = getOne($conn, "SELECT * FROM city_info LIMIT 1");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        verifyCsrf();

        $city_name   = trim($_POST['city_name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $about       = trim($_POST['about'] ?? '');
        $contact     = trim($_POST['contact'] ?? '');

        if ($city_name === '') {
            throw new Exception('Nama kota wajib diisi.');
        }

        if (mb_strlen($city_name) > 100) {
            throw new Exception('Nama kota maksimal 100 karakter.');
        }

        $s = $conn->prepare(
            "UPDATE city_info
             SET city_name = ?, description = ?, about = ?, contact = ?
             WHERE id = ?"
        );

        $s->bind_param("ssssi", $city_name, $description, $about, $contact, $c['id']);

        if (!$s->execute()) {
            throw new Exception('Gagal menyimpan: ' . $s->error);
        }

        setFlash('success', 'Informasi kota berhasil disimpan.');

    } catch (Throwable $e) {
        setFlash('error', $e->getMessage());
    }

    header("Location: city.php");
    exit;
}

$flash = getFlash();

?>

<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Informasi Kota - Portal Taman</title>
    <link rel="stylesheet" href="admin.css?v=4">
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

    <a href="parks.php">
        <img src="../assets/icons/parks.png" class="nav-icon-img" alt="Kelola Taman">
        <span class="nav-text">Kelola Taman</span>
    </a>

    <a class="active" href="city.php">
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

<h1>Informasi Kota</h1>
<p class="page-subtitle">Edit informasi kota yang ditampilkan di halaman publik.</p>

<?php if ($flash): ?>
    <div class="notice <?= e($flash['type']) ?>">
        <span class="notice-icon"><?= $flash['type'] === 'success' ? icon('check', '', 20) : icon('alert', '', 20) ?></span>
        <?= e($flash['message']) ?>
    </div>
<?php endif; ?>

<section>
    <h2>Edit Informasi Kota</h2>

    <form method="post">
        <?= csrfField() ?>

        <div class="form-group">
            <label for="city_name">Nama Kota</label>
            <input
                id="city_name"
                name="city_name"
                value="<?= e($c['city_name'] ?? '') ?>"
                placeholder="Masukkan nama kota"
                required
                maxlength="100"
            >
        </div>

        <div class="form-group">
            <label for="description">Deskripsi</label>
            <span class="form-hint">Deskripsi singkat yang muncul di halaman utama.</span>
            <textarea
                id="description"
                name="description"
                placeholder="Deskripsi kota..."
            ><?= e($c['description'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
            <label for="about">Tentang Portal</label>
            <span class="form-hint">Teks lengkap di bagian "Tentang" halaman publik.</span>
            <textarea
                id="about"
                name="about"
                placeholder="Tentang portal taman..."
            ><?= e($c['about'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
            <label for="contact">Kontak</label>
            <input
                id="contact"
                name="contact"
                value="<?= e($c['contact'] ?? '') ?>"
                placeholder="Kontak / email / telepon"
            >
        </div>

        <div class="btn-group">
            <button type="submit">
                <?= icon('save', '', 18) ?> <span>Simpan Perubahan</span>
            </button>
        </div>

    </form>
</section>

</main>

</body>

</html>
