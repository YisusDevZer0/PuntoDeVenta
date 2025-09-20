<?php
// Prueba simple de descarga Excel
session_start();

// Verificar sesión
if (!isset($_SESSION['ControlMaestro']) && !isset($_SESSION['AdministradorRH']) && !isset($_SESSION['Marketing'])) {
    echo json_encode(['error' => 'No autorizado - Sesión no válida']);
    exit;
}

// Cargar PhpSpreadsheet
require_once '../../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

try {
    // Crear un Excel simple
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    // Agregar datos de prueba
    $sheet->setCellValue('A1', 'ID');
    $sheet->setCellValue('B1', 'Folio');
    $sheet->setCellValue('C1', 'Estado');
    $sheet->setCellValue('D1', 'Fecha');
    
    $sheet->setCellValue('A2', '1');
    $sheet->setCellValue('B2', 'PED202509200001');
    $sheet->setCellValue('C2', 'pendiente');
    $sheet->setCellValue('D2', date('Y-m-d H:i:s'));
    
    // Configurar headers para descarga
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="test_pedidos_' . date('Y-m-d') . '.xlsx"');
    header('Cache-Control: max-age=0');
    
    // Crear el archivo Excel
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    
} catch (Exception $e) {
    echo json_encode(['error' => 'Error al generar Excel: ' . $e->getMessage()]);
}
?>
