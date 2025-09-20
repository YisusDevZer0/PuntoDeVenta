<?php
// Habilitar reporte de errores para debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include_once "../Controladores/ControladorUsuario.php";
include "../Controladores/db_connect.php";

// Cargar PhpSpreadsheet
require_once '../../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

// Verificar que el usuario esté autenticado (usando la misma validación que ControladorUsuario.php)
if (!isset($_SESSION['ControlMaestro']) && !isset($_SESSION['AdministradorRH']) && !isset($_SESSION['Marketing'])) {
    http_response_code(401);
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

// Obtener ID del pedido
$pedido_id = $_REQUEST['pedido_id'] ?? '';

if (empty($pedido_id)) {
    http_response_code(400);
    echo json_encode(['error' => 'ID de pedido requerido']);
    exit;
}

try {
    // Obtener información del pedido
    $sql_pedido = "SELECT 
                        p.id,
                        p.folio,
                        p.estado,
                        p.fecha_creacion,
                        p.fecha_aprobacion,
                        p.fecha_completado,
                        p.total_estimado,
                        p.observaciones,
                        p.prioridad,
                        p.tipo_origen,
                        u.Nombre_Apellidos as usuario_nombre,
                        s.Nombre_Sucursal as sucursal_nombre
                    FROM pedidos p
                    LEFT JOIN Usuarios_PV u ON p.usuario_id = u.Id_PvUser
                    LEFT JOIN Sucursales s ON p.sucursal_id = s.ID_Sucursal
                    WHERE p.id = ?";
    
    $stmt_pedido = $conn->prepare($sql_pedido);
    $stmt_pedido->execute([$pedido_id]);
    $pedido = $stmt_pedido->fetch(PDO::FETCH_ASSOC);
    
    if (!$pedido) {
        http_response_code(404);
        echo json_encode(['error' => 'Pedido no encontrado']);
        exit;
    }
    
    // Obtener productos del pedido
    $sql_productos = "SELECT 
                        pp.cantidad,
                        pp.precio_unitario,
                        pp.subtotal,
                        pr.Nombre_Producto as producto_nombre,
                        pr.Codigo_Producto as producto_codigo,
                        pr.Descripcion as producto_descripcion
                     FROM pedidos_productos pp
                     LEFT JOIN Productos pr ON pp.producto_id = pr.ID_Producto
                     WHERE pp.pedido_id = ?
                     ORDER BY pr.Nombre_Producto";
    
    $stmt_productos = $conn->prepare($sql_productos);
    $stmt_productos->execute([$pedido_id]);
    $productos = $stmt_productos->fetchAll(PDO::FETCH_ASSOC);
    
    // Obtener historial de cambios
    $sql_historial = "SELECT 
                        h.estado_anterior,
                        h.estado_nuevo,
                        h.fecha_cambio,
                        h.comentario,
                        u.nombre as usuario_nombre
                     FROM historial_pedidos h
                     LEFT JOIN usuarios u ON h.usuario_id = u.id
                     WHERE h.pedido_id = ?
                     ORDER BY h.fecha_cambio DESC";
    
    $stmt_historial = $conn->prepare($sql_historial);
    $stmt_historial->execute([$pedido_id]);
    $historial = $stmt_historial->fetchAll(PDO::FETCH_ASSOC);
    
    // Generar archivo Excel
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    // Configurar encabezados
    $sheet->setTitle('Pedido ' . $pedido['folio']);
    
    // Estilos
    $headerStyle = [
        'font' => [
            'bold' => true,
            'color' => ['rgb' => 'FFFFFF']
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['rgb' => '007bff']
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['rgb' => '000000']
            ]
        ]
    ];
    
    $dataStyle = [
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['rgb' => '000000']
            ]
        ],
        'alignment' => [
            'vertical' => Alignment::VERTICAL_CENTER
        ]
    ];
    
    $labelStyle = [
        'font' => ['bold' => true],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['rgb' => 'f8f9fa']
        ]
    ];
    
    // Información del pedido
    $row = 1;
    $sheet->setCellValue('A' . $row, 'INFORMACIÓN DEL PEDIDO');
    $sheet->mergeCells('A' . $row . ':D' . $row);
    $sheet->getStyle('A' . $row . ':D' . $row)->applyFromArray($headerStyle);
    $row++;
    
    $info_pedido = [
        'Folio' => $pedido['folio'],
        'Estado' => ucfirst(str_replace('_', ' ', $pedido['estado'])),
        'Fecha de Creación' => date('d/m/Y H:i', strtotime($pedido['fecha_creacion'])),
        'Usuario' => $pedido['usuario_nombre'],
        'Sucursal' => $pedido['sucursal_nombre'],
        'Total Estimado' => '$' . number_format($pedido['total_estimado'], 2),
        'Prioridad' => ucfirst($pedido['prioridad']),
        'Observaciones' => $pedido['observaciones'] ?: 'Sin observaciones'
    ];
    
    foreach ($info_pedido as $label => $value) {
        $sheet->setCellValue('A' . $row, $label . ':');
        $sheet->getStyle('A' . $row)->applyFromArray($labelStyle);
        $sheet->setCellValue('B' . $row, $value);
        $sheet->getStyle('B' . $row)->applyFromArray($dataStyle);
        $row++;
    }
    
    $row += 2;
    
    // Productos del pedido
    $sheet->setCellValue('A' . $row, 'PRODUCTOS DEL PEDIDO');
    $sheet->mergeCells('A' . $row . ':F' . $row);
    $sheet->getStyle('A' . $row . ':F' . $row)->applyFromArray($headerStyle);
    $row++;
    
    // Encabezados de productos
    $productos_headers = [
        'A' . $row => 'Código',
        'B' . $row => 'Producto',
        'C' . $row => 'Descripción',
        'D' . $row => 'Cantidad',
        'E' . $row => 'Precio Unitario',
        'F' . $row => 'Subtotal'
    ];
    
    foreach ($productos_headers as $cell => $value) {
        $sheet->setCellValue($cell, $value);
        $sheet->getStyle($cell)->applyFromArray($headerStyle);
    }
    $row++;
    
    // Datos de productos
    $total_general = 0;
    foreach ($productos as $producto) {
        $sheet->setCellValue('A' . $row, $producto['producto_codigo']);
        $sheet->setCellValue('B' . $row, $producto['producto_nombre']);
        $sheet->setCellValue('C' . $row, $producto['producto_descripcion']);
        $sheet->setCellValue('D' . $row, $producto['cantidad']);
        $sheet->setCellValue('E' . $row, '$' . number_format($producto['precio_unitario'], 2));
        $sheet->setCellValue('F' . $row, '$' . number_format($producto['subtotal'], 2));
        
        $sheet->getStyle('A' . $row . ':F' . $row)->applyFromArray($dataStyle);
        $total_general += $producto['subtotal'];
        $row++;
    }
    
    // Total general
    $row++;
    $sheet->setCellValue('E' . $row, 'TOTAL:');
    $sheet->getStyle('E' . $row)->applyFromArray($labelStyle);
    $sheet->setCellValue('F' . $row, '$' . number_format($total_general, 2));
    $sheet->getStyle('F' . $row)->applyFromArray($labelStyle);
    
    $row += 2;
    
    // Historial de cambios
    if (!empty($historial)) {
        $sheet->setCellValue('A' . $row, 'HISTORIAL DE CAMBIOS');
        $sheet->mergeCells('A' . $row . ':D' . $row);
        $sheet->getStyle('A' . $row . ':D' . $row)->applyFromArray($headerStyle);
        $row++;
        
        $historial_headers = [
            'A' . $row => 'Fecha',
            'B' . $row => 'Estado Anterior',
            'C' . $row => 'Estado Nuevo',
            'D' . $row => 'Usuario',
            'E' . $row => 'Comentario'
        ];
        
        foreach ($historial_headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
            $sheet->getStyle($cell)->applyFromArray($headerStyle);
        }
        $row++;
        
        foreach ($historial as $hist) {
            $sheet->setCellValue('A' . $row, date('d/m/Y H:i', strtotime($hist['fecha_cambio'])));
            $sheet->setCellValue('B' . $row, $hist['estado_anterior'] ?: 'Nuevo');
            $sheet->setCellValue('C' . $row, $hist['estado_nuevo']);
            $sheet->setCellValue('D' . $row, $hist['usuario_nombre'] ?: 'Sistema');
            $sheet->setCellValue('E' . $row, $hist['comentario'] ?: 'Sin comentarios');
            
            $sheet->getStyle('A' . $row . ':E' . $row)->applyFromArray($dataStyle);
            $row++;
        }
    }
    
    // Información de generación
    $row += 2;
    $sheet->setCellValue('A' . $row, 'Generado el: ' . date('d/m/Y H:i:s'));
    $sheet->setCellValue('A' . ($row + 1), 'Por: ' . ($_SESSION['usuario_nombre'] ?? 'Sistema'));
    
    // Ajustar ancho de columnas
    $sheet->getColumnDimension('A')->setWidth(20);
    $sheet->getColumnDimension('B')->setWidth(30);
    $sheet->getColumnDimension('C')->setWidth(40);
    $sheet->getColumnDimension('D')->setWidth(15);
    $sheet->getColumnDimension('E')->setWidth(18);
    $sheet->getColumnDimension('F')->setWidth(18);
    
    // Configurar headers para descarga
    $filename = 'pedido_' . $pedido['folio'] . '_' . date('Y-m-d_H-i-s') . '.xlsx';
    
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    
    // Generar y enviar archivo
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    
} catch (Exception $e) {
    // Log del error para debugging
    error_log("Error en exportar_pedido_excel.php: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    
    http_response_code(500);
    
    // En modo desarrollo, mostrar más detalles del error
    if (ini_get('display_errors')) {
        echo json_encode([
            'error' => 'Error al generar Excel: ' . $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);
    } else {
        echo json_encode(['error' => 'Error al generar Excel. Contacta al administrador.']);
    }
}
?>
