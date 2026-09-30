<?php

/**
 * SSO Handler - Portal Taman Kota
 * 
 * Menerima token dari Laravel (E-Arsip DLH) dan login otomatis
 * tanpa perlu input username/password.
 * 
 * Token bersifat ONE-TIME dan berlaku 60 detik.
 */

session_start();

require_once '../config/database.php';
require_once '../config/functions.php';

// Jika sudah login, langsung ke dashboard
if (!empty($_SESSION['admin'])) {
    header("Location: dashboard.php");
    exit;
}

$token = trim($_GET['token'] ?? '');

if (empty($token) || !preg_match('/^[a-zA-Z0-9]{64}$/', $token)) {
    header("Location: ../index.php");
    exit;
}

$payload = null;

// 1. Cek dari MySQL Cache Table (Laravel database cache driver)
if (isset($conn) && $conn instanceof mysqli) {
    $searchPattern = '%' . $token;
    $now = time();
    $stmt = $conn->prepare("SELECT `key`, `value`, `expiration` FROM `cache` WHERE `key` LIKE ? AND `expiration` >= ? LIMIT 1");
    if ($stmt) {
        $stmt->bind_param("si", $searchPattern, $now);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        if ($res && !empty($res['value'])) {
            $data = @unserialize($res['value']);
            if (is_array($data)) {
                $payload = $data;
            }
            // Hapus token setelah dibaca (one-time token)
            $matchedKey = $res['key'];
            $del = $conn->prepare("DELETE FROM `cache` WHERE `key` = ?");
            if ($del) {
                $del->bind_param("s", $matchedKey);
                $del->execute();
            }
        }
    }
}

// 2. Fallback: Cek file cache Laravel (jika CACHE_STORE=file)
if (!$payload) {
    $cacheKey  = 'portal_taman_sso_' . $token;
    $cacheHash = sha1($cacheKey);
    $cachePath = dirname(__DIR__, 3) . '/storage/framework/cache/data/'
               . substr($cacheHash, 0, 2) . '/'
               . substr($cacheHash, 2, 2) . '/'
               . $cacheHash;

    if (file_exists($cachePath)) {
        $raw = file_get_contents($cachePath);
        if ($raw !== false && strlen($raw) > 10) {
            $expireAt = (int) substr($raw, 0, 10);
            if (time() <= $expireAt) {
                $serialized = substr($raw, 10);
                $data = @unserialize($serialized);
                if (is_array($data) && isset($data['data'])) {
                    $payload = $data['data'];
                } elseif (is_array($data)) {
                    $payload = $data;
                }
            }
            @unlink($cachePath);
        }
    }
}

if (!$payload || empty($payload['username'])) {
    header("Location: /admin/dashboard");
    exit;
}

// Cek apakah role benar-benar super_admin
if (($payload['role'] ?? '') !== 'super_admin') {
    header("Location: /admin/dashboard");
    exit;
}

// ✅ Token valid — set session admin
$_SESSION['admin']          = true;
$_SESSION['admin_username'] = $payload['username'];
$_SESSION['admin_email']    = $payload['email'] ?? '';
$_SESSION['sso_login']      = true; // Penanda login via SSO E-Arsip
$_SESSION['sso_role']       = 'super_admin';

session_regenerate_id(true);

header("Location: dashboard.php");
exit;

