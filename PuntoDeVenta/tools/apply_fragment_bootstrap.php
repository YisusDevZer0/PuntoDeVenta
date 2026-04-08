<?php
/**
 * Añade require de config/fragment_init.php a .php que usan BASE_URL / BASE_PATH / fdp_url
 * y aún no cargan app.php ni fragment_init.php.
 *
 * Uso (desde la carpeta PuntoDeVenta/PuntoDeVenta):
 *   php tools/apply_fragment_bootstrap.php        # solo lista
 *   php tools/apply_fragment_bootstrap.php --apply
 */
declare(strict_types=1);

if (!function_exists('str_contains')) {
    function str_contains(string $haystack, string $needle): bool
    {
        return $needle === '' || strpos($haystack, $needle) !== false;
    }
}
if (!function_exists('str_starts_with')) {
    function str_starts_with(string $haystack, string $needle): bool
    {
        return $needle === '' || strpos($haystack, $needle) === 0;
    }
}
if (!function_exists('str_ends_with')) {
    function str_ends_with(string $haystack, string $needle): bool
    {
        $len = strlen($needle);
        return $len === 0 || substr($haystack, -$len) === $needle;
    }
}

$apply = in_array('--apply', $argv ?? [], true);
$toolsDir = dirname(__DIR__);
$appRoot = realpath($toolsDir);
if ($appRoot === false) {
    fwrite(STDERR, "No se pudo resolver la raíz de la app.\n");
    exit(1);
}

$fragmentRelToConfig = 'config/fragment_init.php';
$fragmentAbs = $appRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $fragmentRelToConfig);
if (!is_file($fragmentAbs)) {
    fwrite(STDERR, "Falta: $fragmentAbs\n");
    exit(1);
}

$skipDirs = ['vendor', '.git', 'node_modules', 'lib'];

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator(
        $appRoot,
        FilesystemIterator::SKIP_DOTS | FilesystemIterator::FOLLOW_SYMLINKS
    )
);

$changed = 0;
$skipped = 0;
$would = [];

/** @var SplFileInfo $file */
foreach ($iterator as $file) {
    if (!$file->isFile() || strtolower($file->getExtension()) !== 'php') {
        continue;
    }
    $path = $file->getPathname();
    $norm = str_replace('\\', '/', $path);
    foreach ($skipDirs as $sd) {
        if (str_contains($norm, '/' . $sd . '/')) {
            continue 2;
        }
    }
    if (str_ends_with($norm, '/tools/apply_fragment_bootstrap.php')) {
        continue;
    }
    if (str_ends_with($norm, '/config/app.php') || str_ends_with($norm, '/config/fragment_init.php')) {
        continue;
    }

    $content = file_get_contents($path);
    if ($content === false) {
        continue;
    }

    // Ya cargan app o fragment (incl. require_once __DIR__ . '.../config/app.php')
    if (str_contains($content, 'fragment_init.php')) {
        $skipped++;
        continue;
    }
    if (preg_match('/require_once[^;]{0,400}config\/app\.php/', $content)) {
        $skipped++;
        continue;
    }

    if (!preg_match('/\bBASE_URL\b|\bBASE_PATH\b|\bfdp_url\s*\(/', $content)) {
        $skipped++;
        continue;
    }

    $fileDir = dirname($path);
    $fileDirNorm = rtrim(str_replace('\\', '/', realpath($fileDir) ?: $fileDir), '/');
    $appNorm = rtrim(str_replace('\\', '/', $appRoot), '/');
    if (!str_starts_with($fileDirNorm, $appNorm)) {
        $skipped++;
        continue;
    }
    $suffix = substr($fileDirNorm, strlen($appNorm));
    $suffix = trim($suffix, '/');
    $steps = $suffix === '' ? 0 : substr_count($suffix, '/') + 1;
    $rel = $steps > 0 ? str_repeat('../', $steps) . 'config/fragment_init.php' : 'config/fragment_init.php';
    $line = "require_once __DIR__ . '/{$rel}';";

    $newContent = insertBootstrapLine($content, $line);
    if ($newContent === null || $newContent === $content) {
        $skipped++;
        continue;
    }

    $would[] = $path;
    if ($apply) {
        if (file_put_contents($path, $newContent) === false) {
            fwrite(STDERR, "Error escribiendo: $path\n");
            exit(1);
        }
        $changed++;
    }
}

if (!$apply) {
    echo "Modo simulación (--apply para escribir). Archivos a modificar: " . count($would) . "\n";
    foreach ($would as $p) {
        echo $p . "\n";
    }
    exit(0);
}

echo "Archivos actualizados: $changed\n";

/**
 * @return string|null nuevo contenido o null si no aplica
 */
function insertBootstrapLine(string $content, string $line): ?string
{
    $trim = ltrim($content, "\xEF\xBB\xBF");
    $hadBom = strlen($trim) < strlen($content);

    if (str_contains($trim, 'fragment_init.php')) {
        return null;
    }

    if (preg_match('/^<\?php\s*/', $trim, $m)) {
        $len = strlen($m[0]);
        $tail = substr($trim, $len);
        $new = substr($trim, 0, $len) . "\n{$line}\n" . $tail;
    } else {
        $new = "<?php\n{$line}\n?>\n" . $trim;
    }

    return $hadBom ? ("\xEF\xBB\xBF" . $new) : $new;
}
