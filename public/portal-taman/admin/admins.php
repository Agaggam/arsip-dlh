<?php

session_start();

require_once '../config/database.php';
require_once '../config/functions.php';

requireAdmin();

// ===============================
// HAPUS ADMIN
// ===============================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    try {
        verifyCsrf();

        $deleteId = (int) ($_POST['id'] ?? 0);

        // Cek jumlah admin — minimal harus ada 1
        $count = (int) getOne($conn, "SELECT COUNT(*) c FROM admins")['c'];

        if ($count <= 1) {
            throw new Exception('Tidak dapat menghapus admin terakhir.');
        }

        $stmt = $conn->prepare("DELETE FROM admins WHERE id = ?");
        $stmt->bind_param("i", $deleteId);

        if (!$stmt->execute()) {
            throw new Exception('Gagal menghapus admin.');
        }

        if ($stmt->affected_rows > 0) {
            setFlash('success', 'Admin berhasil dihapus.');
        } else {
            setFlash('error', 'Admin tidak ditemukan.');
        }

    } catch (Throwable $e) {
        setFlash('error', $e->getMessage());
    }

    header("Location: admins.php");
    exit;
}

// ===============================
// TAMBAH ADMIN
// ===============================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'create') {
    try {
        verifyCsrf();

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['confirm_password'] ?? '';

        if ($username === '' || $password === '' || $confirm === '') {
            throw new Exception('Semua field wajib diisi.');
        }

        if (strlen($username) < 3) {
            throw new Exception('Username minimal 3 karakter.');
        }

        if (strlen($username) > 50) {
            throw new Exception('Username maksimal 50 karakter.');
        }

        if (!preg_match('/^[a-zA-Z0-9._-]+$/', $username)) {
            throw new Exception('Username hanya boleh huruf, angka, titik, strip, dan underscore.');
        }

        if (strlen($password) < 6) {
            throw new Exception('Password minimal 6 karakter.');
        }

        if ($password !== $confirm) {
            throw new Exception('Konfirmasi password tidak sama.');
        }

        // Cek duplikat username
        $stmt = $conn->prepare("SELECT id FROM admins WHERE username = ? LIMIT 1");
        $stmt->bind_param("s", $username);
        $stmt->execute();

        if ($stmt->get_result()->num_rows > 0) {
            throw new Exception('Username sudah digunakan.');
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $hash);

        if (!$stmt->execute()) {
            throw new Exception('Gagal menambahkan admin.');
        }

        setFlash('success', 'Admin "' . $username . '" berhasil ditambahkan.');

    } catch (Throwable $e) {
        setFlash('error', $e->getMessage());
    }

    header("Location: admins.php");
    exit;
}

// Ambil flash message dan data
$flash = getFlash();
$admins = getAll($conn, "SELECT id, username FROM admins ORDER BY id ASC");
$adminCount = count($admins);

?>

<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kelola Admin - Portal Taman</title>
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

    <a href="city.php">
        <img src="../assets/icons/city.png" class="nav-icon-img" alt="Informasi Kota">
        <span class="nav-text">Informasi Kota</span>
    </a>

    <a class="active" href="admins.php">
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

<h1>Kelola Admin</h1>
<p class="page-subtitle">Tambah atau hapus akun administrator portal.</p>

<?php if ($flash): ?>
    <div class="notice <?= e($flash['type']) ?>">
        <span class="notice-icon"><?= $flash['type'] === 'success' ? icon('check', '', 20) : icon('alert', '', 20) ?></span>
        <?= e($flash['message']) ?>
    </div>
<?php endif; ?>

<!-- FORM TAMBAH -->
<section>
    <h2>Tambah Admin Baru</h2>

    <form method="post">
        <?= csrfField() ?>
        <input type="hidden" name="action" value="create">

        <div class="form-group">
            <label for="username">Username</label>
            <span class="form-hint">Minimal 3 karakter. Huruf, angka, titik, strip, underscore.</span>
            <input
                type="text"
                id="username"
                name="username"
                placeholder="Masukkan username"
                minlength="3"
                maxlength="50"
                pattern="[a-zA-Z0-9._-]+"
                required
            >
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="password">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Minimal 6 karakter"
                    minlength="6"
                    required
                >
            </div>

            <div class="form-group">
                <label for="confirm_password">Konfirmasi Password</label>
                <input
                    type="password"
                    id="confirm_password"
                    name="confirm_password"
                    placeholder="Ulangi password"
                    minlength="6"
                    required
                >
            </div>
        </div>

        <div class="btn-group">
            <button type="submit">
                <?= icon('plus', '', 18) ?> <span>Tambah Admin</span>
            </button>
        </div>
    </form>
</section>

<!-- DAFTAR ADMIN -->
<section>
    <h2>Daftar Admin (<?= $adminCount ?>)</h2>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>

            <?php if ($admins): ?>
                <?php foreach ($admins as $row): ?>
                    <tr>
                        <td><?= e($row['id']) ?></td>
                        <td style="font-weight: 700; color: var(--text);"><?= e($row['username']) ?></td>
                        <td>
                            <span class="status-badge status-aktif">Aktif</span>
                        </td>
                        <td>
                            <?php if ($adminCount > 1): ?>
                                <form method="post" style="display:inline;" onsubmit="return confirm('Hapus admin <?= e($row['username']) ?>?')">
                                    <?= csrfField() ?>
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                                    <button type="submit" class="btn-del">
                                        <?= icon('trash', '', 14) ?> <span>Hapus</span>
                                    </button>
                                </form>
                            <?php else: ?>
                                <span style="color: var(--text-muted); font-size: 0.82rem; font-weight: 500;">Admin Utama</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" style="text-align:center; color: var(--text-muted);">Belum ada admin.</td>
                </tr>
            <?php endif; ?>

            </tbody>
        </table>
    </div>
</section>

</main>

</body>

</html>