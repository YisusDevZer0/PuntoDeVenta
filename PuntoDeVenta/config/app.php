<?php
/**
 * Bootstrap de URL pública: BASE_PATH, BASE_URL, APP_ENV.
 *
 * - Producción (doctorpez.mx): app bajo el docroot → puede ser "/" o "/PuntoDeVenta/" según despliegue.
 * - Develop (develop.doctorpez.mx con docroot = carpeta física develop): las URLs NO llevan "/develop";
 *   BASE_PATH debe ser "/" si los PHP están en la raíz del vhost.
 *
 * La ruta web se infiere desde SCRIPT_NAME + SCRIPT_FILENAME respecto a la raíz de la app (directorio
 * padre de /config), no desde nombres fijos de carpetas ni asumiendo /develop en la URL.
 */
if (defined('FDP_APP_BOOTSTRAPPED')) {
    return;
}
define('FDP_APP_BOOTSTRAPPED', true);

$docRoot = realpath($_SERVER['DOCUMENT_ROOT'] ?? '');
$appRoot = realpath(__DIR__ . '/..');
$norm = static function (string $p): string {
    if ($p === '') {
        return '';
    }
    return str_replace('\\', '/', strtolower($p));
};
$docN = $norm($docRoot !== false ? $docRoot : '');
$appN = $norm($appRoot !== false ? $appRoot : '');
$docPrefix = rtrim($docN, '/') . '/';
$appInsideDocroot = $docN !== '' && $appN !== ''
    && (strpos($appN, $docPrefix) === 0 || $appN === rtrim($docN, '/'));
if (
    ($docRoot === false || $appRoot === false || !$appInsideDocroot)
    && getenv('FDP_RELAX_DOCROOT_CHECK') !== '1'
) {
    http_response_code(500);
    exit('Configuración de rutas: DOCUMENT_ROOT no contiene la aplicación.');
}

/**
 * Normaliza path URI con barra inicial y final (excepto raíz → "/").
 */
$fdp_uri_base = static function (string $dir): string {
    $dir = str_replace('\\', '/', $dir);
    $dir = rtrim($dir, '/');
    if ($dir === '' || $dir === '.') {
        return '/';
    }
    return '/' . ltrim($dir, '/') . '/';
};

$isCli = PHP_SAPI === 'cli' || PHP_SAPI === 'phpdbg';
$scriptFile = isset($_SERVER['SCRIPT_FILENAME']) ? realpath($_SERVER['SCRIPT_FILENAME']) : false;

$basePath = null;

if (!$isCli && $scriptFile && strpos($scriptFile, $appRoot) === 0) {
    // Ruta del script bajo la raíz de la app (filesystem), p.ej. ControlPOS.php o Modulo/sub/archivo.php
    $relFs = ltrim(str_replace('\\', '/', substr($scriptFile, strlen($appRoot))), '/');

    $sn = $_SERVER['SCRIPT_NAME'] ?? '/';
    $sn = str_replace('\\', '/', $sn);
    if ($sn === '' || $sn[0] !== '/') {
        $sn = '/' . ltrim($sn, '/');
    }

    // Subir en la URL tantos niveles como separa el fichero de la raíz de la app (alineado con dirname(SCRIPT_NAME))
    $stepsUp = substr_count($relFs, '/') + 1;
    $baseDir = $sn;
    for ($i = 0; $i < $stepsUp; $i++) {
        $baseDir = dirname($baseDir);
    }
    $basePath = $fdp_uri_base($baseDir);
} else {
    // CLI, pruebas o SCRIPT fuera de la app: usar posición bajo DOCUMENT_ROOT
    $rel = trim(str_replace('\\', '/', substr($appRoot, strlen($docRoot))), '/');
    $basePath = $rel === '' ? '/' : '/' . $rel . '/';
}

$https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (isset($_SERVER['SERVER_PORT']) && (string) $_SERVER['SERVER_PORT'] === '443')
    || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
$host = $_SERVER['HTTP_X_FORWARDED_HOST'] ?? $_SERVER['HTTP_HOST'] ?? 'localhost';
$origin = ($https ? 'https' : 'http') . '://' . $host;

define('BASE_PATH', $basePath);
define('BASE_URL', rtrim($origin . $basePath, '/') . '/');
define('ASSETS_URL', BASE_URL);

// Sin nombres de carpetas fijos: solo "¿la app está en la raíz del vhost (path /)?"
define('APP_ENV', $basePath === '/' ? 'production' : 'staging');

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
