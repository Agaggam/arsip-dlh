<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/functions.php';

header('Content-Type: application/json; charset=utf-8');

if (empty($_SESSION['admin'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Sesi login tidak valid']);
    exit;
}

$inputUrl = trim($_GET['url'] ?? '');

if ($inputUrl === '') {
    echo json_encode(['success' => false, 'message' => 'URL tidak boleh kosong.']);
    exit;
}

function extractFromGoogleUrl($url) {
    $lat = null;
    $lng = null;
    $name = null;
    $address = null;

    // Prioritas 0: Format Dropped Pin / Titik Baru (/maps/search/-7.912416,+112.577562)
    if (preg_match('#/maps/search/(-?\d+\.\d+)[,\+](?:\+|%20|\s)*(-?\d+\.\d+)#i', $url, $m)) {
        $lat = (float)$m[1];
        $lng = (float)$m[2];
    }

    // Prioritas 1: Titik Pin Tempat (!3d-7.9096497!4d112.5670425)
    if ($lat === null && preg_match('/!3d(-?\d+\.\d+)!4d(-?\d+\.\d+)/', $url, $m)) {
        $lat = (float)$m[1];
        $lng = (float)$m[2];
    }

    // Prioritas 2: Viewport Camera (@-7.9144772,112.5442967)
    if ($lat === null && preg_match('/@(-?\d+\.\d+),(-?\d+\.\d+)/', $url, $m)) {
        $lat = (float)$m[1];
        $lng = (float)$m[2];
    }

    // Prioritas 3: Parameter q= atau ll= (?q=-7.912416,112.577562 atau ?q=-7.912416,+112.577562)
    if ($lat === null && preg_match('/[?&](?:q|ll)=(-?\d+\.\d+)[,\+](?:\+|%20|\s)*(-?\d+\.\d+)/', $url, $m)) {
        $lat = (float)$m[1];
        $lng = (float)$m[2];
    }

    // Nama Tempat: /maps/place/Nama+Tempat/
    if (preg_match('#/maps/place/([^/@?]+)#i', $url, $m)) {
        $rawName = urldecode($m[1]);
        $rawName = str_replace('+', ' ', $rawName);
        $name = trim($rawName);
    }

    return [
        'latitude'  => $lat,
        'longitude' => $lng,
        'name'      => $name,
        'address'   => $address
    ];
}

function resolveGoogleMapsUrl($url) {
    $currentUrl = $url;
    $maxHops = 6;

    while ($maxHops-- > 0) {
        $extracted = extractFromGoogleUrl($currentUrl);
        if ($extracted['latitude'] !== null && $extracted['longitude'] !== null) {
            $extracted['final_url'] = $currentUrl;
            return $extracted;
        }

        $ch = curl_init($currentUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER         => true,
            CURLOPT_NOBODY         => true,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_TIMEOUT        => 6,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
        ]);

        $header = curl_exec($ch);
        $redirectUrl = curl_getinfo($ch, CURLINFO_REDIRECT_URL);
        curl_close($ch);

        if (!$redirectUrl && preg_match('/^location:\s*(.+)$/im', $header, $m)) {
            $redirectUrl = trim($m[1]);
        }

        if ($redirectUrl) {
            $currentUrl = $redirectUrl;
        } else {
            break;
        }
    }

    $finalExtracted = extractFromGoogleUrl($currentUrl);
    $finalExtracted['final_url'] = $currentUrl;
    return $finalExtracted;
}

$data = resolveGoogleMapsUrl($inputUrl);

if ($data['latitude'] === null && $data['longitude'] === null && empty($data['name'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Tidak dapat mendeteksi koordinat dari link tersebut. Pastikan link Google Maps benar.'
    ]);
    exit;
}

echo json_encode([
    'success'   => true,
    'latitude'  => $data['latitude'],
    'longitude' => $data['longitude'],
    'name'      => $data['name'],
    'address'   => $data['address'],
    'final_url' => $data['final_url'] ?? $inputUrl,
    'message'   => 'Data lokasi berhasil diekstrak!'
]);
