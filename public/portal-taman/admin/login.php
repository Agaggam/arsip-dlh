<?php

session_start();

require_once '../config/database.php';
require_once '../config/functions.php';

// Jika sudah login, langsung ke dashboard
if (!empty($_SESSION['admin'])) {
    header("Location: dashboard.php");
    exit;
}

$err = '';

// Proses Login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $err = 'Username dan password wajib diisi.';
    } else {
        $stmt = $conn->prepare(
            "SELECT * FROM admins WHERE username = ? LIMIT 1"
        );

        $stmt->bind_param("s", $username);
        $stmt->execute();

        $admin = $stmt->get_result()->fetch_assoc();

        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin'] = true;
            $_SESSION['admin_username'] = $admin['username'];

            // Regenerate session ID untuk keamanan
            session_regenerate_id(true);

            header("Location: dashboard.php");
            exit;
        }

        $err = 'Username atau password salah.';
    }
}

?>

<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Admin - Portal Taman</title>
    <link rel="stylesheet" href="admin.css?v=10">
</head>

<body class="login-page">

    <div class="login-card">

        <div class="login-brand">
            <img src="../assets/icons/logo.png" alt="Logo" class="login-logo-img">
            <h1>Portal Taman</h1>
            <p>Masuk ke panel admin</p>
        </div>

        <?php if ($err): ?>
            <div class="err" style="display: flex; align-items: center; gap: 8px;">
                <?= icon('alert', '', 18) ?> <span><?= e($err) ?></span>
            </div>
        <?php endif; ?>

        <form method="post">

            <div class="form-group">
                <label for="username">Username</label>
                <input
                    type="text"
                    id="username"
                    name="username"
                    value="<?= e($username ?? '') ?>"
                    placeholder="Masukkan username"
                    autocomplete="username"
                    required
                    autofocus
                >
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Masukkan password"
                    autocomplete="current-password"
                    required
                >
            </div>

            <button type="submit">
                Masuk
            </button>

        </form>

        <div class="login-footer">
            <a href="../index.php">← Kembali ke Website</a>
        </div>

    </div>

</body>

</html>
