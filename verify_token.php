<?php
require_once __DIR__ . '/Src/Helpers/JwtHelper.php';
use Src\Helpers\JwtHelper;

header('Content-Type: application/json');

// ambil token dari parameter GET
$token = $_GET['token'] ?? '';

if (empty($token)) {
    echo json_encode(['error' => 'Token tidak ditemukan. Kirim lewat parameter ?token='], JSON_PRETTY_PRINT);
    exit;
}

// verifikasi token
$result = JwtHelper::verifyToken($token);

echo json_encode($result, JSON_PRETTY_PRINT);
