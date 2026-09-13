<?php

// 1. Baca konfigurasi database secara otomatis dari file .env Laravel
$envFile = dirname(__DIR__, 2) . '/.env';
$host = '127.0.0.1';
$user = 'root';
$pass = '';
$db   = 'arsip_dlh';
$port = 3306;

if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if (strpos($line, '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($key, $val) = explode('=', $line, 2);
            $key = trim($key);
            $val = trim($val, " \t\n\r\0\x0B\"'");
            if ($key === 'DB_HOST') $host = $val ?: '127.0.0.1';
            if ($key === 'DB_PORT') $port = (int)($val ?: 3306);
            if ($key === 'DB_DATABASE') $db = $val ?: 'arsip_dlh';
            if ($key === 'DB_USERNAME') $user = $val ?: 'root';
            if ($key === 'DB_PASSWORD') $pass = $val;
        }
    }
}

mysqli_report(MYSQLI_REPORT_OFF);

$conn = new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    die('Koneksi database Portal Taman gagal: ' . $conn->connect_error);
}

$conn->set_charset('utf8mb4');

/*
 * Auto-migration helper: memastikan semua field tabel parks & park_images selalu kompatibel
 */
$schemaQueries = [
    "ALTER TABLE parks ADD COLUMN IF NOT EXISTS plant_species TEXT DEFAULT NULL",
    "ALTER TABLE parks ADD COLUMN IF NOT EXISTS employee_count INT(11) NOT NULL DEFAULT 0",
    "ALTER TABLE parks ADD COLUMN IF NOT EXISTS area DECIMAL(12,2) NOT NULL DEFAULT 0.00",
    "ALTER TABLE parks ADD COLUMN IF NOT EXISTS status ENUM('Aktif','Dalam Perawatan','Pasif') NOT NULL DEFAULT 'Aktif'",
    "ALTER TABLE parks ADD COLUMN IF NOT EXISTS map_url TEXT DEFAULT NULL",
    "ALTER TABLE parks ADD COLUMN IF NOT EXISTS latitude DECIMAL(10,7) DEFAULT NULL",
    "ALTER TABLE parks ADD COLUMN IF NOT EXISTS longitude DECIMAL(10,7) DEFAULT NULL",
    "CREATE TABLE IF NOT EXISTS park_images (
        id INT(11) NOT NULL AUTO_INCREMENT,
        park_id INT(11) NOT NULL,
        image VARCHAR(255) NOT NULL,
        sort_order INT(11) NOT NULL DEFAULT 0,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY idx_park_images_park_id (park_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
];

foreach ($schemaQueries as $schemaQuery) {
    if (!$conn->query($schemaQuery)) {
        if ($conn->errno !== 1060) {
            // ignore duplicate column errors
        }
    }
}
