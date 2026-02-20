<?php
header('Content-Type: application/json');
include_once "db_connect.php";
include_once "ControladorUsuario.php";

$sucursal_usuario = (int) $row['Fk_Sucursal'];
$es_admin = in_array($row['TipoUsuario'] ?? '', ['Administrador', 'MKT']);

$sql = "SELECT 
    s.ID_Solicitud,
    s.Cod_Barra,
    s.Nombre_Prod,
    s.Cantidad_solicitada,
    s.Estatus,
    s.Solicitado_por,
    s.Solicitado_el,
    suc_solicitante.Nombre_Sucursal AS Sucursal_solicitante,
    suc_solicitada.Nombre_Sucursal AS Sucursal_solicitada,
    s.Fk_sucursal_solicitante,
    s.Fk_sucursal_solicitada
FROM Solicitudes_Traspaso_Sucursales s
INNER JOIN Sucursales suc_solicitante ON s.Fk_sucursal_solicitante = suc_solicitante.ID_Sucursal
INNER JOIN Sucursales suc_solicitada ON s.Fk_sucursal_solicitada = suc_solicitada.ID_Sucursal
";
$params = [];
$types = "";
if (!$es_admin) {
    $sql .= " WHERE s.Fk_sucursal_solicitada = ?";
    $params[] = $sucursal_usuario;
    $types .= "i";
}
$sql .= " ORDER BY s.Solicitado_el DESC";

$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($fila = $result->fetch_assoc()) {
    $data[] = [
        'ID_Solicitud' => (int) $fila['ID_Solicitud'],
        'Cod_Barra' => $fila['Cod_Barra'],
        'Nombre_Prod' => $fila['Nombre_Prod'],
        'Cantidad_solicitada' => (int) $fila['Cantidad_solicitada'],
        'Estatus' => $fila['Estatus'],
        'Solicitado_por' => $fila['Solicitado_por'],
        'Solicitado_el' => $fila['Solicitado_el'],
        'Sucursal_solicitante' => $fila['Sucursal_solicitante'],
        'Sucursal_solicitada' => $fila['Sucursal_solicitada'],
        'Fk_sucursal_solicitante' => (int) $fila['Fk_sucursal_solicitante'],
        'Fk_sucursal_solicitada' => (int) $fila['Fk_sucursal_solicitada']
    ];
}
$stmt->close();

echo json_encode([
    'sEcho' => isset($_GET['sEcho']) ? (int) $_GET['sEcho'] : 1,
    'iTotalRecords' => count($data),
    'iTotalDisplayRecords' => count($data),
    'aaData' => $data
]);
$conn->close();
