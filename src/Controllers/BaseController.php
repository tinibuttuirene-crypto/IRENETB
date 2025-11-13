<?php
namespace Src\Controllers;

use Src\Helpers\Response;

class BaseController {
    // Menyimpan konfigurasi dari file env.php
    protected array $cfg;

    // Konstruktor untuk menerima konfigurasi
    public function __construct(array $cfg) {
        $this->cfg = $cfg;
    }

    // Method untuk mengirim respon sukses (HTTP 200 secara default)
    protected function ok($data = [], $code = 200) {
        Response::json($data, $code);
    }

    // Method untuk mengirim respon error (HTTP 4xx atau 5xx)
    protected function error($code, $msg, $errors = []) {
        Response::jsonError($code, $msg, $errors);
    }
}