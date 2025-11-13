<?php
// === AUTOLOAD CLASS ===
spl_autoload_register(function ($c) {
    $p = __DIR__ . '/..';
    $c = str_replace('\\', '/', $c);
    $paths = ["$p/src/$c.php", "$p/$c.php"];
    foreach ($paths as $f) {
        if (file_exists($f)) require $f;
    }
});

$cfg = require __DIR__ . '/../config/env.php';

use Src\Helpers\Response;
use Src\Middlewares\CorsMiddleware;
use Src\Helpers\RateLimiter;

// === HANDLE CORS ===
CorsMiddleware::handle($cfg);
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// === PARSE URL DAN METHOD (VERSI LARAGON FIX) ===
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));

// Hilangkan path tambahan dari Laragon (misalnya /api-php-native-irenet/public)
$path = '/' . trim(str_replace($scriptDir, '', $uri), '/');
$path = preg_replace('#^api-php-native-irenet/public/#', '', ltrim($path, '/'));
$path = '/' . trim($path, '/');

$method = $_SERVER['REQUEST_METHOD'];

// (opsional) log untuk debug
// error_log("METHOD: $method | PATH: $path");

// === DEFINISI ROUTES ===
$routes = [
    ['GET', '/api/v1/health', 'Src\\Controllers\\HealthController@show'],
    ['POST', '/api/v1/auth/login', 'Src\\Controllers\\AuthController@login'],
    ['GET', '/api/v1/users', 'Src\\Controllers\\UserController@index'],
    ['GET', '/api/v1/users/{id}', 'Src\\Controllers\\UserController@show'],
    ['POST', '/api/v1/users', 'Src\\Controllers\\UserController@store'],
    ['PUT', '/api/v1/users/{id}', 'Src\\Controllers\\UserController@update'],
    ['DELETE', '/api/v1/users/{id}', 'Src\\Controllers\\UserController@destroy'],
    ['POST', '/api/v1/upload', 'Src\\Controllers\\UploadController@store'],
    ['GET', '/api/v1/version', 'Src\\Controllers\\VersionController@show'],
];

// === FUNGSI COCOKKAN ROUTE ===
function matchRoute($routes, $method, $path)
{
    foreach ($routes as $r) {
        [$m, $p, $h] = $r;
        if ($m !== $method) continue;

        $regex = preg_replace('#\{[^/]+\}#', '([\w-]+)', $p);
        if (preg_match('#^' . $regex . '$#', $path, $mch)) {
            array_shift($mch);
            return [$h, $mch];
        }
    }
    return [null, null];
}

// === COCOKKAN ROUTE ===
[$handler, $params] = matchRoute($routes, $method, $path);
if (!$handler) {
    Response::jsonError(404, 'Route not found');
}

// === RATE LIMITER HANYA DIJALANKAN KALAU ROUTE VALID ===
$key = $_SERVER['HTTP_AUTHORIZATION'] ?? 'IP:' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
if (!RateLimiter::check($key, 5, 60)) {
    Response::jsonError(429, 'Too Many Requests');
}

// === JALANKAN CONTROLLER ===
[$class, $action] = explode('@', $handler);

if (!class_exists($class)) {
    Response::jsonError(500, "Controller $class tidak ditemukan");
}

$controller = new $class($cfg);
if (!method_exists($controller, $action)) {
    Response::jsonError(405, 'Method not allowed');
}

call_user_func_array([$controller, $action], $params);
