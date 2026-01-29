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
    tg.ID_Traspaso_Generado,
    tg.Num_Orden,
    tg.Num_Factura,
    tg.Cod_Barra,
    tg.Nombre_Prod,
    tg.Fk_SucDestino,
    tg.Precio_Venta,
    tg.Precio_Compra,
    tg.Cantidad_Enviada,
    tg.FechaEntrega,
    tg.TraspasoGeneradoPor,
    tg.Estatus,
    s.Nombre_Sucursal AS Sucursal_Destino
FROM Traspasos_generados tg
INNER JOIN Sucursales s ON tg.Fk_SucDestino = s.ID_Sucursal
WHERE tg.Fk_SucDestino = ? AND tg.Estatus = 'Generado'";

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
        $id = (int) $fila['ID_Traspaso_Generado'];
        $data[] = [
            'ID_Traspaso_Generado' => $id,
            'Num_Orden' => $fila['Num_Orden'],
            'Num_Factura' => $fila['Num_Factura'],
            'Cod_Barra' => $fila['Cod_Barra'],
            'Nombre_Prod' => $fila['Nombre_Prod'],
            'Cantidad_Enviada' => (int) $fila['Cantidad_Enviada'],
            'FechaEntrega' => date('d/m/Y', strtotime($fila['FechaEntrega'])),
            'TraspasoGeneradoPor' => $fila['TraspasoGeneradoPor'],
            'Estatus' => $fila['Estatus'],
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
