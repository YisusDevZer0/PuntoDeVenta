<?php
// Desactivar la salida de errores al navegador
error_reporting(0);
ini_set('display_errors', 0);

// Iniciar sesión y establecer headers
session_start();
header('Content-Type: application/json');

try {
    // Verificar sesión primero
    if (!isset($_SESSION['VentasPos'])) {
        throw new Exception('No autorizado');
    }

    // Datos de conexión
    $host = 'localhost';
    $user = 'u858848268_devpezer0';
    $pass = 'F9+nIIOuCh8yI6wu4!08';
    $db = 'u858848268_doctorpez';

    // Intentar conexión
    $conn = new mysqli($host, $user, $pass, $db);
    if ($conn->connect_error) {
        throw new Exception('Error de conexión a la base de datos');
    }

    // Obtener ID de usuario y sucursal
    $idPvUser = $_SESSION['VentasPos'];
    
    // Consultar sucursal del usuario
    $stmt = $conn->prepare("SELECT Fk_Sucursal FROM Usuarios_PV WHERE Id_PvUser = ?");
    if (!$stmt) {
        throw new Exception('Error al preparar consulta de usuario: ' . $conn->error);
    }

    $stmt->bind_param('s', $idPvUser);
    if (!$stmt->execute()) {
        throw new Exception('Error al ejecutar consulta de usuario: ' . $stmt->error);
    }

    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        throw new Exception('Usuario no encontrado');
    }

    $row = $result->fetch_assoc();
    $idSucursal = intval($row['Fk_Sucursal']);
    $stmt->close();

    // Consultar notificaciones
    $stmt = $conn->prepare("
        SELECT n.ID_Notificacion, n.Mensaje, n.Leida, n.Fecha, s.Nombre AS NombreSucursal
        FROM Notificaciones n
        LEFT JOIN Sucursales s ON n.ID_Sucursal = s.ID_Sucursal
        WHERE n.ID_Sucursal = ?
        ORDER BY n.Fecha DESC
        LIMIT 10
    ");

    if (!$stmt) {
        throw new Exception('Error al preparar consulta de notificaciones: ' . $conn->error);
    }

    $stmt->bind_param('i', $idSucursal);
    if (!$stmt->execute()) {
        throw new Exception('Error al ejecutar consulta de notificaciones: ' . $stmt->error);
    }

    $result = $stmt->get_result();
    $notificaciones = [];
    while ($row = $result->fetch_assoc()) {
        $notificaciones[] = $row;
    }
    $stmt->close();

    // Contar notificaciones no leídas
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM Notificaciones WHERE ID_Sucursal = ? AND Leida = 0");
    if (!$stmt) {
        throw new Exception('Error al preparar consulta de conteo: ' . $conn->error);
    }

    $stmt->bind_param('i', $idSucursal);
    if (!$stmt->execute()) {
        throw new Exception('Error al ejecutar consulta de conteo: ' . $stmt->error);
    }

    $result = $stmt->get_result();
    $total = $result->fetch_assoc()['total'];
    $stmt->close();

    // Cerrar conexión
    $conn->close();

    // Devolver respuesta exitosa
    echo json_encode([
        'success' => true,
        'notificaciones' => $notificaciones,
        'total' => $total
    ]);

} catch (Exception $e) {
    // Asegurarse de que la conexión se cierre si existe
    if (isset($conn) && $conn instanceof mysqli) {
        $conn->close();
    }

    // Devolver error en formato JSON
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
} 