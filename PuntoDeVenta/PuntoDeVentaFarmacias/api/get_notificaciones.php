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
if (!isset($_SESSION['VentasPos'])) {
    echo json_encode(['error' => true, 'message' => 'No autorizado']);
    exit;
}

$idPvUser = $_SESSION['VentasPos'];
$sql = "SELECT Fk_Sucursal FROM Usuarios_PV WHERE Id_PvUser = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['error' => true, 'message' => 'Error al preparar la consulta: ' . $conn->error]);
    exit;
}
$stmt->bind_param('s', $idPvUser);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) {
    echo json_encode(['error' => true, 'message' => 'Usuario no encontrado']);
    exit;
}
$row = $res->fetch_assoc();
$idSucursal = intval($row['Fk_Sucursal']);
$stmt->close();

// Consulta notificaciones de la sucursal
$sql = "SELECT n.ID_Notificacion, n.Mensaje, n.Leida, n.Fecha, s.Nombre AS NombreSucursal
        FROM notificaciones n
        LEFT JOIN sucursales s ON n.ID_Sucursal = s.ID_Sucursal
        WHERE n.ID_Sucursal = ?
        ORDER BY n.Fecha DESC
        LIMIT 10";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['error' => true, 'message' => 'Error al preparar la consulta de notificaciones: ' . $conn->error]);
    exit;
}
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
if (!$stmt) {
    echo json_encode(['error' => true, 'message' => 'Error al preparar la consulta de conteo: ' . $conn->error]);
    exit;
}
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

// Agregar un bloque try-catch para capturar excepciones (por ejemplo, si la ejecución de la consulta falla)
try {
    // (El código de consulta ya se ejecuta arriba, por lo que no se repite aquí.)
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => 'Error interno: ' . $e->getMessage()]);
    exit;
} 