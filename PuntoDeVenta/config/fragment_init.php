<?php
/**
 * Cargar app.php una sola vez. Usar al inicio de cualquier .php que emita HTML/JS
 * con <?php echo BASE_URL ?> o similar, sobre todo fragmentos cargados por AJAX.
 *
 * require_once __DIR__ . '/../config/fragment_init.php';  (desde un subdirectorio)
 * o la ruta relativa correcta hasta /config/fragment_init.php
 */
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/app.php';
}
