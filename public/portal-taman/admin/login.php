<?php

session_start();

// Halaman login portal taman telah ditiadakan.
// Akses admin hanya tersedia melalui SSO Super Admin dari aplikasi E-Arsip DLH.
if (!empty($_SESSION['admin'])) {
    header("Location: dashboard.php");
    exit;
}

header("Location: ../index.php");
exit;
