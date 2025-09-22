<?php
// Exportación de resumen de pedido a Excel
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
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

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
                        p.prioridad,
                        u.Nombre_Apellidos as usuario_nombre,
                        s.Nombre_Sucursal as sucursal_nombre
                    FROM pedidos p
                    LEFT JOIN Usuarios_PV u ON p.usuario_id = u.Id_PvUser
                    LEFT JOIN Sucursales s ON p.sucursal_id = s.ID_Sucursal
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
    
    // Obtener productos del pedido
    $sql_productos = "SELECT 
                        pd.cantidad_solicitada as cantidad,
                        pd.precio_unitario,
                        pd.subtotal,
                        pr.Nombre_Prod as producto_nombre,
                        pr.Cod_Barra as producto_codigo,
                        pr.Componente_Activo as producto_descripcion
                     FROM pedido_detalles pd
                     LEFT JOIN Productos_POS pr ON pd.producto_id = pr.ID_Prod_POS
                     WHERE pd.pedido_id = ?
                     ORDER BY pr.Nombre_Prod";
    
    $stmt_productos = $conn->prepare($sql_productos);
    $stmt_productos->bind_param("i", $pedido_id);
    $stmt_productos->execute();
    $result_productos = $stmt_productos->get_result();
    $productos = [];
    while ($row = $result_productos->fetch_assoc()) {
        $productos[] = $row;
    }
    
    // Crear archivo Excel
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    // Configurar encabezados
    $sheet->setTitle('Resumen Pedido ' . $pedido['folio']);
    
    // Estilos
    $headerStyle = [
        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['rgb' => '4472C4']
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['rgb' => '000000']
            ]
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER
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
    
    // Iniciar desde la fila 1 directamente con los productos
    $row = 1;
    
    // Productos del pedido
    $sheet->setCellValue('A' . $row, 'PRODUCTOS DEL PEDIDO');
    $sheet->mergeCells('A' . $row . ':E' . $row);
    $sheet->getStyle('A' . $row . ':E' . $row)->applyFromArray($headerStyle);
    $row++;
    
    // Encabezados de productos
    $productos_headers = [
        'A' . $row => 'Código',
        'B' . $row => 'Producto',
        'C' . $row => 'Cantidad',
        'D' . $row => 'Precio Unitario',
        'E' . $row => 'Subtotal'
    ];
    
    foreach ($productos_headers as $cell => $value) {
        $sheet->setCellValue($cell, $value);
        $sheet->getStyle($cell)->applyFromArray($headerStyle);
    }
    $row++;
    
    // Datos de productos
    $total_general = 0;
    foreach ($productos as $producto) {
        // Configurar código de barras como texto para evitar notación científica
        $sheet->setCellValue('A' . $row, $producto['producto_codigo'] ?: 'N/A');
        $sheet->getStyle('A' . $row)->getNumberFormat()->setFormatCode('@'); // Formato de texto
        
        $sheet->setCellValue('B' . $row, $producto['producto_nombre']);
        $sheet->setCellValue('C' . $row, $producto['cantidad']);
        $sheet->setCellValue('D' . $row, '$' . number_format($producto['precio_unitario'], 2));
        $sheet->setCellValue('E' . $row, '$' . number_format($producto['subtotal'], 2));
        
        $sheet->getStyle('A' . $row . ':E' . $row)->applyFromArray($dataStyle);
        $total_general += $producto['subtotal'];
        $row++;
    }
    
    // Total general
    $row++;
    $sheet->setCellValue('D' . $row, 'TOTAL:');
    $sheet->setCellValue('E' . $row, '$' . number_format($total_general, 2));
    $sheet->getStyle('D' . $row . ':E' . $row)->applyFromArray($headerStyle);
    
    // Ajustar ancho de columnas
    $sheet->getColumnDimension('A')->setWidth(15);
    $sheet->getColumnDimension('B')->setWidth(30);
    $sheet->getColumnDimension('C')->setWidth(10);
    $sheet->getColumnDimension('D')->setWidth(15);
    $sheet->getColumnDimension('E')->setWidth(15);
    
    // Configurar headers para descarga
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="resumen_pedido_' . $pedido_id . '_' . date('Y-m-d') . '.xlsx"');
    header('Cache-Control: max-age=0');
    
    // Crear el archivo Excel
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al generar Excel: ' . $e->getMessage()]);
}
?>
