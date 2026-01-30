<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

try {
    include_once __DIR__ . '/db_connect.php';
    include_once __DIR__ . '/ControladorUsuario.php';

    if (!isset($conn) || !$conn) {
        throw new Exception('Error de conexión a la base de datos');
    }

    if (!isset($row) || !isset($row['Fk_Sucursal'])) {
        throw new Exception('Usuario no autenticado o sin sucursal asignada');
    }

    $sucursal = (int) $row['Fk_Sucursal'];
    if ($sucursal <= 0) {
        throw new Exception('Sucursal inválida');
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
    $sql .= " AND (tyc.Cod_Barra LIKE ? OR tyc.Nombre_Prod LIKE ?)";
    $like = "%{$codigo}%";
    $params[] = $like;
    $params[] = $like;
    $types .= "ss";
}

$sql .= " ORDER BY tyc.Fecha_venta DESC, tyc.TraspaNotID DESC";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception('Error al preparar consulta: ' . $conn->error);
    }

    $data = [];
    $stmt->bind_param($types, ...$params);
    
    if (!$stmt->execute()) {
        throw new Exception('Error al ejecutar consulta: ' . $stmt->error);
    }
    
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

    echo json_encode([
        'sEcho' => 1,
        'iTotalRecords' => count($data),
        'iTotalDisplayRecords' => count($data),
        'aaData' => $data
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'sEcho' => 1,
        'iTotalRecords' => 0,
        'iTotalDisplayRecords' => 0,
        'aaData' => [],
        'error' => $e->getMessage()
    ]);
    error_log('Error en DataRecepcionTraspasos.php: ' . $e->getMessage());
}
