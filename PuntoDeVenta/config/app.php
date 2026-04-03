<?php
/**
 * Bootstrap de URL pública: BASE_PATH, BASE_URL, APP_ENV.
 * Misma base en cualquier subcarpeta o dominio (sin hardcodear /develop).
 */
if (defined('FDP_APP_BOOTSTRAPPED')) {
    return;
}
define('FDP_APP_BOOTSTRAPPED', true);

$docRoot = realpath($_SERVER['DOCUMENT_ROOT'] ?? '');
$appRoot = realpath(__DIR__ . '/..');
if ($docRoot === false || $appRoot === false || strpos($appRoot, $docRoot) !== 0) {
    http_response_code(500);
    exit('Configuración de rutas: DOCUMENT_ROOT no contiene la aplicación.');
}
$rel = trim(str_replace('\\', '/', substr($appRoot, strlen($docRoot))), '/');
$basePath = $rel === '' ? '/' : '/' . $rel . '/';

$https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (isset($_SERVER['SERVER_PORT']) && (string) $_SERVER['SERVER_PORT'] === '443')
    || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
$host = $_SERVER['HTTP_X_FORWARDED_HOST'] ?? $_SERVER['HTTP_HOST'] ?? 'localhost';
$origin = ($https ? 'https' : 'http') . '://' . $host;

define('BASE_PATH', $basePath);
define('BASE_URL', rtrim($origin . $basePath, '/') . '/');
define('ASSETS_URL', BASE_URL);

$trimmed = trim($basePath, '/');
$segments = $trimmed === '' ? [] : explode('/', $trimmed);
$firstSeg = $segments[0] ?? '';
define('APP_ENV', in_array($firstSeg, ['develop', 'beta'], true) ? 'staging' : 'production');

if (!function_exists('fdp_url')) {
    /**
     * URL absoluta bajo la raíz pública de la app (path sin barra inicial).
     */
    function fdp_url(string $path = ''): string
    {
        $path = ltrim($path, '/');
        return $path === '' ? BASE_URL : BASE_URL . $path;
    }
}
