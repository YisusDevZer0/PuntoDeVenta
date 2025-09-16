<?php
include_once "../Controladores/ControladorUsuario.php";
include_once "../Controladores/BitacoraLimpiezaAdminControllerSimple.php";

// Verificar sesión administrativa
if(!isset($_SESSION['ControlMaestro']) && !isset($_SESSION['AdministradorRH']) && !isset($_SESSION['Marketing'])){
    http_response_code(403);
    exit('Acceso denegado');
}

$controller = new BitacoraLimpiezaAdminControllerSimple($conn);

// Obtener filtros
$filtros = [];
if (isset($_GET['sucursal']) && !empty($_GET['sucursal'])) {
    $filtros['sucursal'] = $_GET['sucursal'];
}
if (isset($_GET['area']) && !empty($_GET['area'])) {
    $filtros['area'] = $_GET['area'];
}
if (isset($_GET['fecha_inicio']) && !empty($_GET['fecha_inicio'])) {
    $filtros['fecha_inicio'] = $_GET['fecha_inicio'];
}
if (isset($_GET['fecha_fin']) && !empty($_GET['fecha_fin'])) {
    $filtros['fecha_fin'] = $_GET['fecha_fin'];
}

// Generar CSV
$csv = $controller->exportarDatosCSV($filtros);

// Configurar headers para descarga
$filename = 'bitacoras_limpieza_' . date('Y-m-d_H-i-s') . '.csv';
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');

// Agregar BOM para UTF-8
echo "\xEF\xBB\xBF";

// Output CSV
echo $csv;
exit;
?>
