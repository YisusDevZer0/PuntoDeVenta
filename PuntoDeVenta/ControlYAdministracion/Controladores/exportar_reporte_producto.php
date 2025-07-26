<?php
// Habilitar reporte de errores para debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Verificar si existe el archivo autoload.php
$autoloadPath = '../../vendor/autoload.php';
if (!file_exists($autoloadPath)) {
    header('Content-Type: application/json');
    echo json_encode([
        'error' => true,
        'message' => 'No se encontró la librería PhpSpreadsheet. Verifica que esté instalada.'
    ]);
    exit;
}

// Usar PhpSpreadsheet para generar un archivo Excel real
require_once $autoloadPath;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;

try {
    include("db_connect.php");
    include("ControladorUsuario.php");
    
    // Obtener parámetros de filtro
    $fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : date('Y-m-01');
    $fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : date('Y-m-d');
    $sucursal = isset($_GET['sucursal']) ? $_GET['sucursal'] : '';
    
    // Consulta SQL con JOINs para obtener precios y nombres de sucursales
    $sql = "SELECT 
        v.ID_Prod_POS,
        v.Cod_Barra,
        v.Nombre_Prod,
        v.Tipo,
        v.Fk_sucursal,
        s.Nombre_Sucursal,
        p.Precio_Venta,
        p.Precio_C,
        p.Tipo_Servicio,
        p.Componente_Activo,
        st.Existencias_R,
        SUM(v.Cantidad_Venta) AS Total_Vendido,
        SUM(v.Importe) AS Total_Importe,
        SUM(v.Total_Venta) AS Total_Venta,
        SUM(v.DescuentoAplicado) AS Total_Descuento,
        COUNT(*) AS Numero_Ventas,
        v.AgregadoPor,
        MIN(v.Fecha_venta) AS Primera_Venta,
        MAX(v.Fecha_venta) AS Ultima_Venta
    FROM Ventas_POS v
    LEFT JOIN Productos_POS p ON v.ID_Prod_POS = p.ID_Prod_POS
    LEFT JOIN Stock_POS st ON v.ID_Prod_POS = st.ID_Prod_POS AND v.Fk_sucursal = st.Fk_sucursal
    LEFT JOIN Sucursales s ON v.Fk_sucursal = s.ID_Sucursal
    WHERE v.Fecha_venta BETWEEN ? AND ?
    AND v.Estatus = 'Pagado'";
    
    // Agregar filtro de sucursal si se especifica
    if (!empty($sucursal)) {
        $sql .= " AND v.Fk_sucursal = ?";
    }
    
    $sql .= " GROUP BY v.ID_Prod_POS, v.Cod_Barra, v.Nombre_Prod, v.Tipo, v.Fk_sucursal, s.Nombre_Sucursal, p.Precio_Venta, p.Precio_C, p.Tipo_Servicio, p.Componente_Activo, st.Existencias_R, v.AgregadoPor
    ORDER BY Total_Vendido DESC";
    
    // Preparar la consulta
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception('Error al preparar la consulta: ' . $conn->error);
    }
    
    if (!empty($sucursal)) {
        $stmt->bind_param("sss", $fecha_inicio, $fecha_fin, $sucursal);
    } else {
        $stmt->bind_param("ss", $fecha_inicio, $fecha_fin);
    }
    
    if (!$stmt->execute()) {
        throw new Exception('Error al ejecutar la consulta: ' . $stmt->error);
    }
    
    $result = $stmt->get_result();
    if (!$result) {
        throw new Exception('Error al obtener resultados: ' . $stmt->error);
    }
    
    // Crear nuevo documento Excel
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    // Configurar el título del reporte
    $sheet->setCellValue('A1', 'Reporte de Ventas por Producto');
    $sheet->mergeCells('A1:P1');
    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
    $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    
    // Información del período
    $sheet->setCellValue('A2', 'Período: ' . date('d/m/Y', strtotime($fecha_inicio)) . ' - ' . date('d/m/Y', strtotime($fecha_fin)));
    $sheet->mergeCells('A2:P2');
    
    $sheet->setCellValue('A3', 'Generado: ' . date('d/m/Y H:i:s'));
    $sheet->mergeCells('A3:P3');
    
    // Headers de la tabla
    $headers = array(
        'ID Producto', 'Código de Barras', 'Nombre del Producto', 'Tipo', 'Sucursal',
        'Precio Venta', 'Precio Compra', 'Existencias', 'Total Vendido', 'Total Importe',
        'Total Venta', 'Total Descuento', 'Número Ventas', 'Vendedor', 'Primera Venta', 'Última Venta'
    );
    
    $col = 'A';
    foreach ($headers as $header) {
        $sheet->setCellValue($col . '5', $header);
        $col++;
    }
    
    // Estilo para los headers
    $headerStyle = [
        'font' => [
            'bold' => true,
            'color' => ['rgb' => 'FFFFFF'],
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['rgb' => 'EF7980'],
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER,
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['rgb' => '000000'],
            ],
        ],
    ];
    
    $sheet->getStyle('A5:P5')->applyFromArray($headerStyle);
    
    // Datos de la tabla
    $total_importe = 0;
    $total_ventas = 0;
    $total_unidades = 0;
    $row_num = 6;
    
    while ($row = $result->fetch_assoc()) {
        $sheet->setCellValue('A' . $row_num, $row['ID_Prod_POS']);
        $sheet->setCellValue('B' . $row_num, $row['Cod_Barra'] ?: '');
        $sheet->setCellValue('C' . $row_num, $row['Nombre_Prod'] ?: 'Sin nombre');
        $sheet->setCellValue('D' . $row_num, $row['Tipo'] ?: '');
        $sheet->setCellValue('E' . $row_num, $row['Nombre_Sucursal'] ?: 'Sucursal no encontrada');
        $sheet->setCellValue('F' . $row_num, $row['Precio_Venta'] ?: 0);
        $sheet->setCellValue('G' . $row_num, $row['Precio_C'] ?: 0);
        $sheet->setCellValue('H' . $row_num, $row['Existencias_R'] ?: 0);
        $sheet->setCellValue('I' . $row_num, $row['Total_Vendido']);
        $sheet->setCellValue('J' . $row_num, $row['Total_Importe']);
        $sheet->setCellValue('K' . $row_num, $row['Total_Venta']);
        $sheet->setCellValue('L' . $row_num, $row['Total_Descuento']);
        $sheet->setCellValue('M' . $row_num, $row['Numero_Ventas']);
        $sheet->setCellValue('N' . $row_num, $row['AgregadoPor'] ?: '');
        $sheet->setCellValue('O' . $row_num, $row['Primera_Venta'] ? date('d/m/Y', strtotime($row['Primera_Venta'])) : '');
        $sheet->setCellValue('P' . $row_num, $row['Ultima_Venta'] ? date('d/m/Y', strtotime($row['Ultima_Venta'])) : '');
        
        $total_importe += $row['Total_Importe'];
        $total_ventas += $row['Total_Venta'];
        $total_unidades += $row['Total_Vendido'];
        $row_num++;
    }
    
    // Estilo para las filas de datos
    $dataStyle = [
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['rgb' => '000000'],
            ],
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER,
        ],
    ];
    
    if ($row_num > 6) {
        $sheet->getStyle('A6:P' . ($row_num - 1))->applyFromArray($dataStyle);
    }
    
    // Formato para monedas
    $sheet->getStyle('F:G')->getNumberFormat()->setFormatCode('$#,##0.00');
    $sheet->getStyle('J:L')->getNumberFormat()->setFormatCode('$#,##0.00');
    
    // Formato para números
    $sheet->getStyle('H:I')->getNumberFormat()->setFormatCode('#,##0');
    $sheet->getStyle('M')->getNumberFormat()->setFormatCode('#,##0');
    
    // Ajustar ancho de columnas
    foreach (range('A', 'P') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }
    
    // Agregar resumen
    $summaryRow = $row_num + 2;
    $sheet->setCellValue('A' . $summaryRow, 'RESUMEN');
    $sheet->mergeCells('A' . $summaryRow . ':B' . $summaryRow);
    $sheet->getStyle('A' . $summaryRow)->getFont()->setBold(true);
    $sheet->getStyle('A' . $summaryRow)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('EF7980');
    $sheet->getStyle('A' . $summaryRow)->getFont()->getColor()->setRGB('FFFFFF');
    
    $summaryRow++;
    $sheet->setCellValue('A' . $summaryRow, 'Total Productos:');
    $sheet->setCellValue('B' . $summaryRow, $result->num_rows);
    
    $summaryRow++;
    $sheet->setCellValue('A' . $summaryRow, 'Total Unidades Vendidas:');
    $sheet->setCellValue('B' . $summaryRow, $total_unidades);
    $sheet->getStyle('B' . $summaryRow)->getNumberFormat()->setFormatCode('#,##0');
    
    $summaryRow++;
    $sheet->setCellValue('A' . $summaryRow, 'Total Importe:');
    $sheet->setCellValue('B' . $summaryRow, $total_importe);
    $sheet->getStyle('B' . $summaryRow)->getNumberFormat()->setFormatCode('$#,##0.00');
    
    $summaryRow++;
    $sheet->setCellValue('A' . $summaryRow, 'Total Ventas:');
    $sheet->setCellValue('B' . $summaryRow, $total_ventas);
    $sheet->getStyle('B' . $summaryRow)->getNumberFormat()->setFormatCode('$#,##0.00');
    
    // Configurar headers para descarga
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="Reporte_Ventas_Producto_' . date('Y-m-d_H-i-s') . '.xlsx"');
    header('Cache-Control: max-age=0');
    header('Cache-Control: max-age=1');
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
    header('Cache-Control: cache, must-revalidate');
    header('Pragma: public');
    
    // Crear el archivo Excel
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    
    // Cerrar conexión
    $stmt->close();
    $conn->close();
    
} catch (Exception $e) {
    // Si hay error, mostrar mensaje de error
    error_log('Error en exportar_reporte_producto.php: ' . $e->getMessage());
    
    // Enviar respuesta de error
    header('Content-Type: application/json');
    echo json_encode([
        'error' => true,
        'message' => 'Error al generar el reporte: ' . $e->getMessage()
    ]);
}
?> 