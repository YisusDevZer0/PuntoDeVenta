<?php
header('Content-Type: application/json; charset=utf-8');
include "db_connect.php";
include_once "ControladorUsuario.php";

$fecha_desde = isset($_GET['fecha_desde']) ? trim($_GET['fecha_desde']) : date('Y-m-d', strtotime('-30 days'));
$fecha_hasta = isset($_GET['fecha_hasta']) ? trim($_GET['fecha_hasta']) : date('Y-m-d');
$sucursal_id = isset($_GET['sucursal_id']) ? (int)$_GET['sucursal_id'] : 0;

$sql = "SELECT 
    i.IdProdCedis,
    i.ID_Prod_POS,
    i.NumFactura,
    i.Proveedor,
    i.Cod_Barra,
    i.Nombre_Prod,
    i.Fk_Sucursal,
    s.Nombre_Sucursal,
    i.Contabilizado,
    i.Fecha_Caducidad,
    i.Lote,
    i.PrecioMaximo,
    i.Precio_Venta,
    i.Precio_C,
    i.AgregadoPor,
    i.AgregadoEl,
    i.FechaInventario,
    i.Estatus,
    i.NumOrden
FROM IngresosFarmacias i
LEFT JOIN Sucursales s ON i.Fk_Sucursal = s.ID_Sucursal
WHERE DATE(i.AgregadoEl) BETWEEN ? AND ?";

$params = [$fecha_desde, $fecha_hasta];
$types = "ss";

if ($sucursal_id > 0) {
    $sql .= " AND i.Fk_Sucursal = ?";
    $params[] = $sucursal_id;
    $types .= "i";
}

$sql .= " ORDER BY i.AgregadoEl DESC";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['aaData' => [], 'iTotalRecords' => 0, 'iTotalDisplayRecords' => 0]);
    exit;
}
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($fila = $result->fetch_assoc()) {
    $data[] = [
        'IdProdCedis' => $fila['IdProdCedis'],
        'NumFactura' => $fila['NumFactura'],
        'Proveedor' => $fila['Proveedor'],
        'Cod_Barra' => $fila['Cod_Barra'],
        'Nombre_Prod' => $fila['Nombre_Prod'],
        'Sucursal' => $fila['Nombre_Sucursal'] ?: $fila['Fk_Sucursal'],
        'Contabilizado' => $fila['Contabilizado'],
        'Fecha_Caducidad' => $fila['Fecha_Caducidad'],
        'Lote' => $fila['Lote'],
        'PrecioMaximo' => $fila['PrecioMaximo'],
        'AgregadoPor' => $fila['AgregadoPor'],
        'AgregadoEl' => $fila['AgregadoEl'],
        'Estatus' => $fila['Estatus'],
    ];
}

$stmt->close();
$conn->close();

echo json_encode([
    'sEcho' => 1,
    'iTotalRecords' => count($data),
    'iTotalDisplayRecords' => count($data),
    'aaData' => $data,
]);
