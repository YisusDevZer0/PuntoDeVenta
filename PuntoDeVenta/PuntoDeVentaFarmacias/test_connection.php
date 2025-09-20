<?php
header('Content-Type: application/json');
include_once "Controladores/db_connect.php";
include_once "Controladores/ControladorUsuario.php";

// Verificar sesión
session_start();
if(!isset($_SESSION['VentasPos'])){
    echo json_encode(['success' => false, 'message' => 'Sesión no válida']);
    exit();
}

// Obtener datos del usuario
$userId = $_SESSION['VentasPos'];
$sql = "SELECT
    Usuarios_PV.Id_PvUser,
    Usuarios_PV.Nombre_Apellidos,
    Usuarios_PV.Fk_Sucursal,
    Sucursales.Nombre_Sucursal
FROM
    Usuarios_PV
INNER JOIN Sucursales ON Usuarios_PV.Fk_Sucursal = Sucursales.ID_Sucursal 
WHERE Usuarios_PV.Id_PvUser = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $userId);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    echo json_encode(['success' => false, 'message' => 'Error al obtener datos del usuario']);
    exit();
}

echo json_encode([
    'success' => true,
    'message' => 'Conexión exitosa',
    'usuario' => $row,
    'sucursal_id' => $row['Fk_Sucursal']
]);
?>
