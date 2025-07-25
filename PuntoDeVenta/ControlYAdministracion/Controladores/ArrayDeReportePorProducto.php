<?php
header('Content-Type: application/json');
include("db_connect.php");
include("ControladorUsuario.php");

// Obtener parámetros de filtro (opcional)
$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : date('Y-m-01'); // Primer día del mes actual
$fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : date('Y-m-d'); // Día actual
$sucursal = isset($_GET['sucursal']) ? $_GET['sucursal'] : '';

// Consulta SQL para ventas por producto
$sql = "SELECT 
    v.ID_Prod_POS,
    v.Cod_Barra,
    v.Nombre_Prod,
    SUM(v.Cantidad_Venta) AS Total_Vendido,
    SUM(v.Importe) AS Total_Importe,
    SUM(v.Total_Venta) AS Total_Venta,
    SUM(v.DescuentoAplicado) AS Total_Descuento,
    COUNT(*) AS Numero_Ventas,
    v.Fk_sucursal,
    s.Nombre_Sucursal,
    v.Precio_Venta,
    v.Precio_C,
    v.Tipo,
    v.AgregadoPor,
    MAX(v.Fecha_venta) AS Ultima_Venta
FROM Ventas_POS v
LEFT JOIN Sucursales s ON v.Fk_sucursal = s.ID_Sucursal
WHERE v.Fecha_venta BETWEEN ? AND ?
  AND v.Estatus = 'vendido'";

// Agregar filtro de sucursal si se especifica
if (!empty($sucursal)) {
    $sql .= " AND v.Fk_sucursal = ?";
}

$sql .= " GROUP BY v.ID_Prod_POS, v.Cod_Barra, v.Nombre_Prod, v.Fk_sucursal, s.Nombre_Sucursal, v.Precio_Venta, v.Precio_C, v.Tipo, v.AgregadoPor
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

$data = [];

if ($result && $result->num_rows > 0) {
    while ($fila = $result->fetch_assoc()) {
        $data[] = [
            "ID_Prod_POS" => $fila["ID_Prod_POS"],
            "Cod_Barra" => $fila["Cod_Barra"],
            "Nombre_Prod" => $fila["Nombre_Prod"],
            "Total_Vendido" => number_format($fila["Total_Vendido"], 0),
            "Total_Importe" => '$' . number_format($fila["Total_Importe"], 2),
            "Total_Venta" => '$' . number_format($fila["Total_Venta"], 2),
            "Total_Descuento" => '$' . number_format($fila["Total_Descuento"], 2),
            "Numero_Ventas" => $fila["Numero_Ventas"],
            "Nombre_Sucursal" => $fila["Nombre_Sucursal"],
            "Precio_Venta" => '$' . number_format($fila["Precio_Venta"], 2),
            "Precio_C" => '$' . number_format($fila["Precio_C"], 2),
            "Tipo" => $fila["Tipo"],
            "AgregadoPor" => $fila["AgregadoPor"],
            "Ultima_Venta" => date('d/m/Y H:i', strtotime($fila["Ultima_Venta"]))
        ];
    }
}

// Construir el array de resultados para la respuesta JSON
$results = [
    "sEcho" => 1,
    "iTotalRecords" => count($data),
    "iTotalDisplayRecords" => count($data),
    "aaData" => $data
];

// Imprimir la respuesta JSON
echo json_encode($results);

// Cerrar la conexión
$stmt->close();
$conn->close();
?>
