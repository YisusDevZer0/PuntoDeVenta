<?php
header('Content-Type: application/json');
include_once __DIR__ . '/db_connect.php';
include_once __DIR__ . '/ControladorUsuario.php';

if (!isset($conn) || !$conn) {
    echo json_encode(['sEcho' => 1, 'iTotalRecords' => 0, 'iTotalDisplayRecords' => 0, 'aaData' => []]);
    exit;
}

$sucursal = (int) ($row['Fk_Sucursal'] ?? 0);
if ($sucursal <= 0) {
    echo json_encode(['sEcho' => 1, 'iTotalRecords' => 0, 'iTotalDisplayRecords' => 0, 'aaData' => []]);
    exit;
}

$codigo = isset($_GET['codigo']) ? trim($_GET['codigo']) : '';

$sql = "SELECT 
    tyc.TraspaNotID,
    tyc.Folio_Ticket,
    tyc.Cod_Barra,
    tyc.Nombre_Prod,
    tyc.Fk_SucursalDestino,
    tyc.Cantidad,
    tyc.Fecha_venta,
    tyc.Estatus,
    tyc.AgregadoPor,
    suc_origen.Nombre_Sucursal AS Sucursal_Origen,
    suc_destino.Nombre_Sucursal AS Sucursal_Destino
FROM TraspasosYNotasC tyc
INNER JOIN Sucursales suc_origen ON tyc.Fk_sucursal = suc_origen.ID_Sucursal
INNER JOIN Sucursales suc_destino ON tyc.Fk_SucursalDestino = suc_destino.ID_Sucursal
WHERE tyc.Fk_SucursalDestino = ? AND tyc.Estatus = 'Generado'";

$params = [$sucursal];
$types = "i";

if ($codigo !== '') {
    $sql .= " AND (tg.Cod_Barra LIKE ? OR tg.Nombre_Prod LIKE ?)";
    $like = "%{$codigo}%";
    $params[] = $like;
    $params[] = $like;
    $types .= "ss";
}

$sql .= " ORDER BY tg.FechaEntrega DESC, tg.ID_Traspaso_Generado DESC";

$stmt = $conn->prepare($sql);
$data = [];

if ($stmt) {
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($fila = $result->fetch_assoc()) {
        $id = (int) $fila['TraspaNotID'];
        $data[] = [
            'TraspaNotID' => $id,
            'Folio_Ticket' => $fila['Folio_Ticket'],
            'Cod_Barra' => $fila['Cod_Barra'],
            'Nombre_Prod' => $fila['Nombre_Prod'],
            'Cantidad' => (int) $fila['Cantidad'],
            'Fecha_venta' => date('d/m/Y', strtotime($fila['Fecha_venta'])),
            'AgregadoPor' => $fila['AgregadoPor'],
            'Estatus' => $fila['Estatus'],
            'Sucursal_Origen' => $fila['Sucursal_Origen'],
            'Sucursal_Destino' => $fila['Sucursal_Destino'],
            'Recibir' => "<button type='button' class='btn btn-sm btn-primary btn-recibir-traspaso' data-id='{$id}' title='Recibir y registrar lote/caducidad'><i class='fa-solid fa-truck-ramp-box'></i> Recibir</button>"
        ];
    }
    $stmt->close();
}

echo json_encode([
    'sEcho' => 1,
    'iTotalRecords' => count($data),
    'iTotalDisplayRecords' => count($data),
    'aaData' => $data
]);
