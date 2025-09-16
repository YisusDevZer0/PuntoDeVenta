<?php
include_once "../../Consultas/db_connect.php";
include_once "../Controladores/BitacoraLimpiezaAdminController.php";

try {
    // Obtener filtros
    $filtros = [
        'sucursal' => $_GET['sucursal'] ?? '',
        'area' => $_GET['area'] ?? '',
        'fecha_inicio' => $_GET['fecha_inicio'] ?? '',
        'fecha_fin' => $_GET['fecha_fin'] ?? '',
        'estado' => $_GET['estado'] ?? ''
    ];
    
    // Crear controlador y obtener datos
    $controller = new BitacoraLimpiezaAdminController($conn);
    $csv_data = $controller->exportarDatosCSV($filtros);
    
    // Configurar headers para descarga
    $filename = 'bitacoras_limpieza_' . date('Y-m-d_H-i-s') . '.csv';
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    // Crear archivo CSV
    $output = fopen('php://output', 'w');
    
    // Agregar BOM para UTF-8
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    foreach ($csv_data as $row) {
        fputcsv($output, $row);
    }
    
    fclose($output);
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>