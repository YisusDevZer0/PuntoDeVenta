<?php
include_once "db_connect.php";
include_once "ControladorUsuario.php";
include_once "TareasController.php";

// Obtener datos del usuario actual
$userId = $row['Id_PvUser'];
$sucursalId = $row['Fk_Sucursal'];

$tareasController = new TareasController($conn, $userId, $sucursalId);

// Obtener filtros de la URL
$filtros = [];
if (isset($_GET['filtros'])) {
    $filtros = json_decode($_GET['filtros'], true);
}

// Obtener las tareas con los filtros aplicados
$result = $tareasController->getTareasAsignadas($filtros);

// Configurar headers para descarga
$filename = 'tareas_' . date('Y-m-d_H-i-s') . '.csv';
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Crear el archivo CSV
$output = fopen('php://output', 'w');

// Escribir BOM para UTF-8
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Escribir encabezados
fputcsv($output, [
    'ID',
    'Título',
    'Descripción',
    'Prioridad',
    'Fecha Límite',
    'Estado',
    'Asignado a',
    'Creado por',
    'Fecha Creación',
    'Fecha Actualización'
]);

// Escribir datos
while ($row = $result->fetch_assoc()) {
    fputcsv($output, [
        $row['id'],
        $row['titulo'],
        $row['descripcion'],
        $row['prioridad'],
        $row['fecha_limite'],
        $row['estado'],
        $row['asignado_nombre'],
        $row['creador_nombre'],
        $row['fecha_creacion'],
        $row['fecha_actualizacion']
    ]);
}

fclose($output);
exit;
?>
