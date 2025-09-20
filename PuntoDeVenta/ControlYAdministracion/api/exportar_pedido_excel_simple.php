<?php
// Exportación simple de pedido a Excel
session_start();

// Verificar sesión
if (!isset($_SESSION['ControlMaestro']) && !isset($_SESSION['AdministradorRH']) && !isset($_SESSION['Marketing'])) {
    http_response_code(401);
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

include_once "../Controladores/db_connect.php";

// Cargar PhpSpreadsheet
require_once '../../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Obtener ID del pedido
$pedido_id = $_REQUEST['pedido_id'] ?? '';

if (empty($pedido_id)) {
    http_response_code(400);
    echo json_encode(['error' => 'ID de pedido requerido']);
    exit;
}

// Convertir a entero
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
        http_response_code(404);
        echo json_encode(['error' => 'Pedido no encontrado']);
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
    http_response_code(500);
    echo json_encode(['error' => 'Error al generar Excel: ' . $e->getMessage()]);
}
?>
