<?php
// Prueba directa de Excel sin validación de sesión
include_once "../Controladores/db_connect.php";

// Cargar PhpSpreadsheet
require_once '../../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Obtener ID del pedido
$pedido_id = $_REQUEST['pedido_id'] ?? 1; // Usar 1 por defecto para pruebas
$pedido_id = (int)$pedido_id;

try {
    // Consulta simple del pedido
    $sql_pedido = "SELECT 
                        p.id,
                        p.folio,
                        p.estado,
                        p.fecha_creacion,
                        p.total_estimado,
                        p.observaciones,
                        p.prioridad
                    FROM pedidos p
                    WHERE p.id = ?";
    
    $stmt_pedido = $conn->prepare($sql_pedido);
    $stmt_pedido->bind_param("i", $pedido_id);
    $stmt_pedido->execute();
    $result_pedido = $stmt_pedido->get_result();
    $pedido = $result_pedido->fetch_assoc();
    
    if (!$pedido) {
        echo "Pedido no encontrado con ID: " . $pedido_id;
        exit;
    }
    
    // Crear archivo Excel simple
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    // Configurar encabezados
    $sheet->setTitle('Pedido ' . $pedido['folio']);
    
    // Agregar datos del pedido
    $sheet->setCellValue('A1', 'ID');
    $sheet->setCellValue('B1', 'Folio');
    $sheet->setCellValue('C1', 'Estado');
    $sheet->setCellValue('D1', 'Fecha');
    $sheet->setCellValue('E1', 'Total');
    $sheet->setCellValue('F1', 'Prioridad');
    
    $sheet->setCellValue('A2', $pedido['id']);
    $sheet->setCellValue('B2', $pedido['folio']);
    $sheet->setCellValue('C2', $pedido['estado']);
    $sheet->setCellValue('D2', $pedido['fecha_creacion']);
    $sheet->setCellValue('E2', $pedido['total_estimado']);
    $sheet->setCellValue('F2', $pedido['prioridad']);
    
    // Configurar headers para descarga
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="pedido_' . $pedido_id . '_' . date('Y-m-d') . '.xlsx"');
    header('Cache-Control: max-age=0');
    
    // Crear el archivo Excel
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    
} catch (Exception $e) {
    echo "Error al generar Excel: " . $e->getMessage();
}
?>
