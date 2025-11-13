<?php
namespace Src\Controllers;

class HealthController extends BaseController {
    // Endpoint: GET /api/v1/health
    public function show() {
        $this->ok([
            'status' => 'ok',
            'time'   => date('c') // format waktu ISO 8601
        ]);
    }

    // Endpoint tambahan untuk tugas:
    // GET /api/v1/version
    public function version() {
        $this->ok([
            'version' => '1.0.0',
            'framework' => 'Simple PHP Router'
        ]);
    }
}