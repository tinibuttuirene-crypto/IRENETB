<?php
namespace Src\Helpers;

class RateLimiter
{
    /**
     * Membatasi jumlah request per user/IP dalam jangka waktu tertentu.
     *
     * @param string $key    Identitas unik (misalnya IP atau token)
     * @param int    $max    Jumlah maksimum request
     * @param int    $window Waktu dalam detik untuk menghitung batas (misalnya 60 detik)
     * @return bool          true jika masih boleh request, false jika sudah melebihi batas
     */
    public static function check($key, $max = 60, $window = 60)
    {
        // Tentukan folder penyimpanan log (naik dua folder dari /Helpers)
        $logDir = dirname(__DIR__, 2) . '/logs';

        // Buat folder logs jika belum ada
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }

        // File penyimpanan berdasarkan hash key unik
        $file = $logDir . '/ratelimit_' . md5($key) . '.txt';

        $now = time();
        $hits = [];

        // Baca data request sebelumnya (jika ada)
        if (file_exists($file)) {
            $hits = array_filter(
                array_map('intval', explode("\n", trim(file_get_contents($file)))),
                fn($t) => $t > $now - $window // hanya simpan request dalam jangka waktu window
            );
        }

        // Jika sudah melebihi batas
        if (count($hits) >= $max) {
            return false;
        }

        // Simpan waktu request baru
        $hits[] = $now;
        file_put_contents($file, implode("\n", $hits));

        return true;
    }
}