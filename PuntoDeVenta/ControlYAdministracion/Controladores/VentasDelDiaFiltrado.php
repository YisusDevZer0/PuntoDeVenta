<?php
include_once "db_connect.php";

// Obtener parámetros de filtro
$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : date('Y-m-01');
$fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : date('Y-m-d');
$sucursal = isset($_GET['sucursal']) ? $_GET['sucursal'] : '';

// Construir la consulta SQL con filtros
$sql = "SELECT 
    v.Cod_Barra,
    v.Nombre_Prod,
    v.PrecioCompra,
    v.PrecioVenta,
    v.FolioTicket,
    s.Nombre_Sucursal as Sucursal,
    v.Turno,
    v.Cantidad_Venta,
    v.Total_Venta,
    v.Importe,
    v.Descuento,
    v.FormaPago,
    v.Cliente,
    v.FolioSignoVital,
    v.NomServ,
    v.AgregadoEl,
    v.AgregadoEnMomento,
    v.AgregadoPor
FROM Ventas v
LEFT JOIN Sucursales s ON v.ID_Sucursal = s.ID_Sucursal
WHERE v.AgregadoEl BETWEEN ? AND ?
";

$params = [$fecha_inicio . ' 00:00:00', $fecha_fin . ' 23:59:59'];
$types = "ss";

// Agregar filtro de sucursal si se especifica
if (!empty($sucursal)) {
    $sql .= " AND v.ID_Sucursal = ?";
    $params[] = $sucursal;
    $types .= "i";
}

$sql .= " ORDER BY v.AgregadoEl DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

// Devolver los datos en formato JSON para DataTables
echo json_encode([
    "data" => $data
]);
?>
