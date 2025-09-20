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

// Obtener parámetros de filtro
$filtro_estado = $_REQUEST['estado'] ?? '';
$filtro_fecha_inicio = $_REQUEST['fecha_inicio'] ?? '';
$filtro_fecha_fin = $_REQUEST['fecha_fin'] ?? '';
$busqueda = $_REQUEST['busqueda'] ?? '';

try {
    // Construir la consulta SQL con filtros
    $sql = "SELECT 
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
            WHERE 1=1";
    
    $params = [];
    
    // Aplicar filtros
    if (!empty($filtro_estado)) {
        $sql .= " AND p.estado = ?";
        $params[] = $filtro_estado;
    }
    
    if (!empty($filtro_fecha_inicio)) {
        $sql .= " AND DATE(p.fecha_creacion) >= ?";
        $params[] = $filtro_fecha_inicio;
    }
    
    if (!empty($filtro_fecha_fin)) {
        $sql .= " AND DATE(p.fecha_creacion) <= ?";
        $params[] = $filtro_fecha_fin;
    }
    
    if (!empty($busqueda)) {
        $sql .= " AND (p.folio LIKE ? OR p.observaciones LIKE ? OR u.nombre LIKE ?)";
        $searchTerm = "%$busqueda%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }
    
    $sql .= " ORDER BY p.fecha_creacion DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Obtener productos de cada pedido
    foreach ($pedidos as &$pedido) {
        $sql_productos = "SELECT 
                            pp.cantidad,
                            pp.precio_unitario,
                            pp.subtotal,
                            pr.nombre as producto_nombre,
                            pr.codigo as producto_codigo
                         FROM pedidos_productos pp
                         LEFT JOIN productos pr ON pp.producto_id = pr.id
                         WHERE pp.pedido_id = ?";
        
        $stmt_productos = $conn->prepare($sql_productos);
        $stmt_productos->execute([$pedido['id']]);
        $pedido['productos'] = $stmt_productos->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Generar archivo Excel usando PhpSpreadsheet
    
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    // Configurar encabezados
    $sheet->setTitle('Pedidos');
    
    // Estilos para encabezados
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
    
    // Estilos para celdas de datos
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
    
    // Encabezados principales
    $headers = [
        'A1' => 'Folio',
        'B1' => 'Estado',
        'C1' => 'Fecha Creación',
        'D1' => 'Usuario',
        'E1' => 'Sucursal',
        'F1' => 'Total Estimado',
        'G1' => 'Prioridad',
        'H1' => 'Observaciones',
        'I1' => 'Productos'
    ];
    
    // Aplicar encabezados
    foreach ($headers as $cell => $value) {
        $sheet->setCellValue($cell, $value);
        $sheet->getStyle($cell)->applyFromArray($headerStyle);
    }
    
    // Ajustar ancho de columnas
    $sheet->getColumnDimension('A')->setWidth(15);
    $sheet->getColumnDimension('B')->setWidth(12);
    $sheet->getColumnDimension('C')->setWidth(18);
    $sheet->getColumnDimension('D')->setWidth(20);
    $sheet->getColumnDimension('E')->setWidth(20);
    $sheet->getColumnDimension('F')->setWidth(15);
    $sheet->getColumnDimension('G')->setWidth(12);
    $sheet->getColumnDimension('H')->setWidth(30);
    $sheet->getColumnDimension('I')->setWidth(40);
    
    // Llenar datos
    $row = 2;
    foreach ($pedidos as $pedido) {
        // Estado con colores
        $estado = ucfirst(str_replace('_', ' ', $pedido['estado']));
        
        // Prioridad
        $prioridad = ucfirst($pedido['prioridad']);
        
        // Formatear fecha
        $fecha = date('d/m/Y H:i', strtotime($pedido['fecha_creacion']));
        
        // Formatear total
        $total = '$' . number_format($pedido['total_estimado'], 2);
        
        // Lista de productos
        $productos_lista = [];
        foreach ($pedido['productos'] as $producto) {
            $productos_lista[] = $producto['producto_nombre'] . ' (x' . $producto['cantidad'] . ')';
        }
        $productos_texto = implode(', ', $productos_lista);
        
        // Datos de la fila
        $sheet->setCellValue('A' . $row, $pedido['folio']);
        $sheet->setCellValue('B' . $row, $estado);
        $sheet->setCellValue('C' . $row, $fecha);
        $sheet->setCellValue('D' . $row, $pedido['usuario_nombre']);
        $sheet->setCellValue('E' . $row, $pedido['sucursal_nombre']);
        $sheet->setCellValue('F' . $row, $total);
        $sheet->setCellValue('G' . $row, $prioridad);
        $sheet->setCellValue('H' . $row, $pedido['observaciones']);
        $sheet->setCellValue('I' . $row, $productos_texto);
        
        // Aplicar estilos a la fila
        $sheet->getStyle('A' . $row . ':I' . $row)->applyFromArray($dataStyle);
        
        // Colorear estado según su valor
        $estadoColor = '';
        switch ($pedido['estado']) {
            case 'pendiente':
                $estadoColor = 'FFC107'; // Amarillo
                break;
            case 'aprobado':
                $estadoColor = '28A745'; // Verde
                break;
            case 'rechazado':
                $estadoColor = 'DC3545'; // Rojo
                break;
            case 'en_proceso':
                $estadoColor = '17A2B8'; // Azul
                break;
            case 'completado':
                $estadoColor = '6F42C1'; // Morado
                break;
            case 'cancelado':
                $estadoColor = '6C757D'; // Gris
                break;
        }
        
        if ($estadoColor) {
            $sheet->getStyle('B' . $row)->getFill()
                  ->setFillType(Fill::FILL_SOLID)
                  ->getStartColor()->setRGB($estadoColor);
        }
        
        $row++;
    }
    
    // Agregar información de filtros aplicados
    $infoRow = $row + 2;
    $sheet->setCellValue('A' . $infoRow, 'Filtros aplicados:');
    $sheet->getStyle('A' . $infoRow)->getFont()->setBold(true);
    
    $infoRow++;
    if (!empty($filtro_estado)) {
        $sheet->setCellValue('A' . $infoRow, 'Estado: ' . ucfirst(str_replace('_', ' ', $filtro_estado)));
        $infoRow++;
    }
    if (!empty($filtro_fecha_inicio)) {
        $sheet->setCellValue('A' . $infoRow, 'Fecha inicio: ' . $filtro_fecha_inicio);
        $infoRow++;
    }
    if (!empty($filtro_fecha_fin)) {
        $sheet->setCellValue('A' . $infoRow, 'Fecha fin: ' . $filtro_fecha_fin);
        $infoRow++;
    }
    if (!empty($busqueda)) {
        $sheet->setCellValue('A' . $infoRow, 'Búsqueda: ' . $busqueda);
        $infoRow++;
    }
    
    // Agregar información de generación
    $infoRow++;
    $sheet->setCellValue('A' . $infoRow, 'Generado el: ' . date('d/m/Y H:i:s'));
    $sheet->setCellValue('A' . ($infoRow + 1), 'Total de pedidos: ' . count($pedidos));
    
    // Configurar headers para descarga
    $filename = 'pedidos_' . date('Y-m-d_H-i-s') . '.xlsx';
    
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    
    // Generar y enviar archivo
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    
} catch (Exception $e) {
    // Log del error para debugging
    error_log("Error en exportar_pedidos_excel.php: " . $e->getMessage());
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
