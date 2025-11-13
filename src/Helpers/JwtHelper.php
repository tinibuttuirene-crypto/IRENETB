<?php
namespace Src\Helpers;

class JwtHelper
{
    private static string $secret = 'Cinta_sejati_takkan_pernah_pudar_oleh_waktu';

    // --- Fungsi encode base64URL ---
    private static function base64url($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    // --- Fungsi decode base64URL ---
    private static function base64url_decode($data)
    {
        return base64_decode(strtr($data, '-_', '+/'));
    }

    // --- Membuat token baru ---
    public static function createToken(array $data, int $duration = 60): string
    {
        $header = ['typ' => 'JWT', 'alg' => 'HS256'];
        $payload = array_merge($data, [
            'iat' => time(),
            'exp' => time() + $duration
        ]);

        $header64 = self::base64url(json_encode($header));
        $payload64 = self::base64url(json_encode($payload));
        $signature = self::base64url(hash_hmac('sha256', "$header64.$payload64", self::$secret, true));

        return "$header64.$payload64.$signature";
    }

    // --- Verifikasi token ---
    public static function verifyToken(string $jwt): array
    {
        $parts = explode('.', $jwt);
        if (count($parts) !== 3) {
            return ['status' => 'invalid', 'message' => 'Struktur token tidak valid.'];
        }

        [$header64, $payload64, $signatureProvided] = $parts;

        $expectedSig = self::base64url(hash_hmac('sha256', "$header64.$payload64", self::$secret, true));
        if (!hash_equals($expectedSig, $signatureProvided)) {
            return ['status' => 'invalid', 'message' => 'Signature tidak cocok.'];
        }

        $payload = json_decode(self::base64url_decode($payload64), true);
        $current = time();
        $exp = $payload['exp'] ?? null;

        if ($exp && $current > $exp) {
            return [
                'status' => 'expired',
                'message' => 'Token sudah kadaluarsa.',
                'expired_at' => date('Y-m-d H:i:s', $exp),
                'payload' => $payload
            ];
        }

        return [
            'status' => 'valid',
            'message' => 'Token masih berlaku.',
            'expired_at' => date('Y-m-d H:i:s', $exp),
            'payload' => $payload
        ];
    }
}
