<?php
require_once __DIR__ . '/Src/Helpers/JwtHelper.php';
use Src\Helpers\JwtHelper;

header('Content-Type: application/json');

// buat token dengan masa berlaku 15 detik (biar mudah lihat expired)
$token = JwtHelper::createToken([
    'id' => 1,
    'name' => 'irene',
    'role' => 'admin'
], 15);

echo json_encode([
    'message' => 'Token berhasil dibuat',
    'token' => $token
], JSON_PRETTY_PRINT);
