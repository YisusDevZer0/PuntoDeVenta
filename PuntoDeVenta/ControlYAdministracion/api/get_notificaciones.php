<?php
// Habilitar visualización de errores para debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Intentar incluir la conexión DB con manejo de errores
try {
    include "../dbconect.php";
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode([
        'error' => true,
        'message' => 'Error en la conexión a la base de datos: ' . $e->getMessage(),
        'notificaciones' => [],
        'total' => 0
    ]);
    exit;
}

// Iniciar sesión con manejo de errores
try {
    session_start();
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode([
        'error' => true,
        'message' => 'Error al iniciar sesión: ' . $e->getMessage(),
        'notificaciones' => [],
        'total' => 0
    ]);
    exit;
}

header('Content-Type: application/json');

// Verificar que la conexión existe
if (!isset($conn) || $conn === null) {
    echo json_encode([
        'error' => true,
        'message' => 'No se pudo establecer conexión con la base de datos',
        'notificaciones' => [],
        'total' => 0
    ]);
    exit;
}

// ID de sucursal del usuario actual
$sucursalID = isset($_SESSION["ID_Sucursal"]) ? $_SESSION["ID_Sucursal"] : 1;

try {
    // Verificar si la tabla Notificaciones existe
    $checkTable = $conn->query("SHOW TABLES LIKE 'Notificaciones'");
    if ($checkTable->num_rows === 0) {
        echo json_encode([
            'error' => true,
            'message' => 'La tabla Notificaciones no existe en la base de datos',
            'notificaciones' => [],
            'total' => 0
        ]);
        exit;
    }

    // Obtener estructura de la tabla para verificar campos
    $tableInfo = $conn->query("DESCRIBE Notificaciones");
    $fields = [];
    while ($field = $tableInfo->fetch_assoc()) {
        $fields[] = $field['Field'];
    }

    // Construir consulta basada en campos existentes
    if (in_array('ID_Notificacion', $fields) && in_array('Tipo', $fields) && 
        in_array('Mensaje', $fields) && in_array('Fecha', $fields) && 
        in_array('SucursalID', $fields) && in_array('Leido', $fields)) {
        
        // Obtener notificaciones no leídas para esta sucursal
        $query = "SELECT ID_Notificacion, Tipo, Mensaje, Fecha, SucursalID, 
                TIMESTAMPDIFF(MINUTE, Fecha, NOW()) as MinutosTranscurridos 
                FROM Notificaciones 
                WHERE Leido = 0 
                AND (SucursalID = ? OR SucursalID = 0)
                ORDER BY Fecha DESC 
                LIMIT 20";

        $stmt = $conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Error al preparar la consulta: " . $conn->error);
        }
        
        $stmt->bind_param("i", $sucursalID);
        $stmt->execute();
        $result = $stmt->get_result();

        $notificaciones = [];
        while ($row = $result->fetch_assoc()) {
            // Formatear tiempo transcurrido
            $tiempo = "";
            $minutos = $row['MinutosTranscurridos'];
            
            if ($minutos < 60) {
                $tiempo = $minutos . " minutos";
            } else if ($minutos < 1440) {
                $tiempo = floor($minutos / 60) . " horas";
            } else {
                $tiempo = floor($minutos / 1440) . " días";
            }
            
            $row['TiempoTranscurrido'] = $tiempo;
            $notificaciones[] = $row;
        }

        // Obtener también el total para el contador
        $queryTotal = "SELECT COUNT(*) as total FROM Notificaciones 
                    WHERE Leido = 0 
                    AND (SucursalID = ? OR SucursalID = 0)";
        $stmtTotal = $conn->prepare($queryTotal);
        $stmtTotal->bind_param("i", $sucursalID);
        $stmtTotal->execute();
        $total = $stmtTotal->get_result()->fetch_assoc()['total'];

        echo json_encode([
            'error' => false,
            'notificaciones' => $notificaciones,
            'total' => $total
        ]);
    } else {
        // La tabla existe pero no tiene la estructura esperada
        echo json_encode([
            'error' => true,
            'message' => 'La tabla Notificaciones no tiene la estructura esperada',
            'fields' => $fields,
            'notificaciones' => [],
            'total' => 0
        ]);
    }

} catch (Exception $e) {
    echo json_encode([
        'error' => true,
        'message' => 'Error en la consulta: ' . $e->getMessage(),
        'notificaciones' => [],
        'total' => 0
    ]);
}
?> 