<?php
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="Reporte_Ventas_Producto_' . date('Y-m-d_H-i-s') . '.xls"');
header('Cache-Control: max-age=0');

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

if (!empty($sucursal)) {
    $stmt->bind_param("sss", $fecha_inicio, $fecha_fin, $sucursal);
} else {
    $stmt->bind_param("ss", $fecha_inicio, $fecha_fin);
}

$stmt->execute();
$result = $stmt->get_result();

// Crear el archivo Excel
echo '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
echo '<head>';
echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
echo '<style>';
echo 'table { border-collapse: collapse; }';
echo 'th, td { border: 1px solid #000; padding: 5px; }';
echo 'th { background-color: #f0f0f0; font-weight: bold; }';
echo '</style>';
echo '</head>';
echo '<body>';

// Título del reporte
echo '<h2>Reporte de Ventas por Producto</h2>';
echo '<p><strong>Período:</strong> ' . date('d/m/Y', strtotime($fecha_inicio)) . ' - ' . date('d/m/Y', strtotime($fecha_fin)) . '</p>';
echo '<p><strong>Generado:</strong> ' . date('d/m/Y H:i:s') . '</p>';

// Tabla de datos
echo '<table>';
echo '<thead>';
echo '<tr>';
echo '<th>ID Producto</th>';
echo '<th>Código de Barras</th>';
echo '<th>Nombre del Producto</th>';
echo '<th>Tipo</th>';
echo '<th>Sucursal</th>';
echo '<th>Precio Venta</th>';
echo '<th>Precio Compra</th>';
echo '<th>Existencias</th>';
echo '<th>Total Vendido</th>';
echo '<th>Total Importe</th>';
echo '<th>Total Venta</th>';
echo '<th>Total Descuento</th>';
echo '<th>Número Ventas</th>';
echo '<th>Vendedor</th>';
echo '<th>Primera Venta</th>';
echo '<th>Última Venta</th>';
echo '</tr>';
echo '</thead>';
echo '<tbody>';

$total_importe = 0;
$total_ventas = 0;
$total_unidades = 0;

while ($row = $result->fetch_assoc()) {
    echo '<tr>';
    echo '<td>' . $row['ID_Prod_POS'] . '</td>';
    echo '<td>' . ($row['Cod_Barra'] ?: '') . '</td>';
    echo '<td>' . ($row['Nombre_Prod'] ?: 'Sin nombre') . '</td>';
    echo '<td>' . ($row['Tipo'] ?: '') . '</td>';
    echo '<td>' . ($row['Nombre_Sucursal'] ?: 'Sucursal no encontrada') . '</td>';
    echo '<td>' . ($row['Precio_Venta'] ? '$' . number_format($row['Precio_Venta'], 2) : '') . '</td>';
    echo '<td>' . ($row['Precio_C'] ? '$' . number_format($row['Precio_C'], 2) : '') . '</td>';
    echo '<td>' . ($row['Existencias_R'] ? number_format($row['Existencias_R']) : '0') . '</td>';
    echo '<td>' . number_format($row['Total_Vendido']) . '</td>';
    echo '<td>$' . number_format($row['Total_Importe'], 2) . '</td>';
    echo '<td>$' . number_format($row['Total_Venta'], 2) . '</td>';
    echo '<td>$' . number_format($row['Total_Descuento'], 2) . '</td>';
    echo '<td>' . number_format($row['Numero_Ventas']) . '</td>';
    echo '<td>' . ($row['AgregadoPor'] ?: '') . '</td>';
    echo '<td>' . ($row['Primera_Venta'] ? date('d/m/Y', strtotime($row['Primera_Venta'])) : '') . '</td>';
    echo '<td>' . ($row['Ultima_Venta'] ? date('d/m/Y', strtotime($row['Ultima_Venta'])) : '') . '</td>';
    echo '</tr>';
    
    $total_importe += $row['Total_Importe'];
    $total_ventas += $row['Total_Venta'];
    $total_unidades += $row['Total_Vendido'];
}

echo '</tbody>';
echo '</table>';

// Totales
echo '<br><br>';
echo '<h3>Resumen</h3>';
echo '<table>';
echo '<tr><td><strong>Total Productos:</strong></td><td>' . $result->num_rows . '</td></tr>';
echo '<tr><td><strong>Total Unidades Vendidas:</strong></td><td>' . number_format($total_unidades) . '</td></tr>';
echo '<tr><td><strong>Total Importe:</strong></td><td>$' . number_format($total_importe, 2) . '</td></tr>';
echo '<tr><td><strong>Total Ventas:</strong></td><td>$' . number_format($total_ventas, 2) . '</td></tr>';
echo '</table>';

echo '</body>';
echo '</html>';

// Cerrar conexión
$stmt->close();
$conn->close();
?> 