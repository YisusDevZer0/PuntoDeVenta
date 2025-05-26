<?php
session_start();
header('Content-Type: application/json');

// Ajusta los datos de conexión según tu entorno
$host = 'localhost';
$user = 'u858848268_devpezer0';
$pass = 'F9+nIIOuCh8yI6wu4!08';
$db = 'u858848268_doctorpez';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    echo json_encode(['error' => true, 'message' => 'Error de conexión a la base de datos']);
    exit;
}

// Verifica la sesión y obtiene la sucursal y usuario
if (!isset($_SESSION['ID_Sucursal']) || !isset($_SESSION['ID_Usuario'])) {
    echo json_encode(['error' => true, 'message' => 'No autorizado']);
    exit;
}

$idSucursal = intval($_SESSION['ID_Sucursal']);
$idUsuario = intval($_SESSION['ID_Usuario']);

// Consulta notificaciones de la sucursal
$sql = "SELECT n.ID_Notificacion, n.Mensaje, n.Leida, n.Fecha, s.Nombre AS NombreSucursal
        FROM notificaciones n
        LEFT JOIN sucursales s ON n.ID_Sucursal = s.ID_Sucursal
        WHERE n.ID_Sucursal = ?
        ORDER BY n.Fecha DESC
        LIMIT 10";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $idSucursal);
$stmt->execute();
$result = $stmt->get_result();
$notificaciones = [];
while ($row = $result->fetch_assoc()) {
    $notificaciones[] = $row;
}
$stmt->close();

// Cuenta no leídas
$sqlCount = "SELECT COUNT(*) as total FROM notificaciones WHERE ID_Sucursal = ? AND Leida = 0";
$stmt = $conn->prepare($sqlCount);
$stmt->bind_param('i', $idSucursal);
$stmt->execute();
$resCount = $stmt->get_result();
$total = $resCount->fetch_assoc()['total'];
$stmt->close();

$conn->close();

echo json_encode([
    'success' => true,
    'notificaciones' => $notificaciones,
    'total' => $total
]); 