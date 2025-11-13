<?php
namespace Src\Middlewares;

use Src\Helpers\Response;
use Src\Helpers\Jwt;

class AuthMiddleware
{
    // Middleware untuk memeriksa token user
    public static function user(array $cfg)
    {
        $hdr = $_SERVER['HTTP_AUTHORIZATION'] ?? '';

        // Cek apakah header Authorization ada dan menggunakan format Bearer
        if (!preg_match('/Bearer\s+(.*)/', $hdr, $m)) {
            Response::jsonError(401, 'Missing token');
        }

        // Verifikasi token JWT
        $pl = Jwt::verify($m[1], $cfg['app']['jwt_secret']);
        if (!$pl) {
            Response::jsonError(401, 'Invalid or expired token');
        }

        return $pl; // payload dikembalikan jika valid
    }

    // Middleware untuk admin saja
    public static function admin(array $cfg)
    {
        $pl = self::user($cfg);
        if (($pl['role'] ?? 'user') !== 'admin') {
            Response::jsonError(403, 'Forbidden');
        }
        return $pl;
    }
}