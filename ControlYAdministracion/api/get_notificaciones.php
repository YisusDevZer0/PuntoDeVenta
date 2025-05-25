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
if (!isset($con) || $con === null) {
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
    $checkTable = $con->query("SHOW TABLES LIKE 'Notificaciones'");
    if ($checkTable->num_rows === 0) {
        echo json_encode([
            'error' => true,
            'message' => 'La tabla Notificaciones no existe en la base de datos',
            'notificaciones' => [],
            'total' => 0
        ]);
        exit;
    }

    // Obtener notificaciones no leídas para esta sucursal
    $query = "SELECT n.ID_Notificacion, n.Tipo, n.Mensaje, n.Fecha, n.SucursalID, n.Leido,
        COALESCE(s.Nombre_Sucursal, 'Todas las sucursales') AS Nombre_Sucursal
        FROM Notificaciones n
        LEFT JOIN Sucursales s ON n.SucursalID = s.ID_Sucursal
        WHERE n.Leido = 0 
        AND (n.SucursalID = ? OR n.SucursalID = 0)
        ORDER BY n.Fecha DESC 
        LIMIT 20";

    $stmt = $con->prepare($query);
    if (!$stmt) {
        throw new Exception("Error al preparar la consulta: " . $con->error);
    }
    
    $stmt->bind_param("i", $sucursalID);
    $stmt->execute();
    $result = $stmt->get_result();

    $notificaciones = [];
    while ($row = $result->fetch_assoc()) {
        // Formatear tiempo transcurrido
        $tiempo = "";
        $minutos = isset($row['MinutosTranscurridos']) ? $row['MinutosTranscurridos'] : 0;
        if ($minutos < 60) {
            $tiempo = $minutos . " minutos";
        } else if ($minutos < 1440) {
            $tiempo = floor($minutos / 60) . " horas";
        } else {
            $tiempo = floor($minutos / 1440) . " días";
        }

        // Reemplazo forzado del nombre de la sucursal en el mensaje (patrón más flexible)
        $mensaje = $row['Mensaje'];
        if ($row['SucursalID'] > 0 && $row['Nombre_Sucursal']) {
            // Reemplaza cualquier "en sucursal ..." por el nombre real de la sucursal
            $mensaje = preg_replace(
                '/en sucursal\s*[^\s)]+/i',
                'en sucursal ' . $row['Nombre_Sucursal'],
                $mensaje
            );
            // Si el mensaje no contiene el nombre de la sucursal, lo agrega al final
            if (stripos($mensaje, $row['Nombre_Sucursal']) === false) {
                $mensaje .= " (Sucursal: " . $row['Nombre_Sucursal'] . ")";
            }
        } elseif ($row['SucursalID'] == 0) {
            $mensaje = preg_replace(
                '/en sucursal\s*[^\s)]+/i',
                'en todas las sucursales',
                $mensaje
            );
        }

        $row['TiempoTranscurrido'] = $tiempo;
        $row['Mensaje'] = $mensaje;
        $row['Nombre_Sucursal'] = $row['Nombre_Sucursal'];
        unset($row['Nombre_Sucursal']);
        $notificaciones[] = $row;
    }

    // Obtener también el total para el contador
    $queryTotal = "SELECT COUNT(*) as total FROM Notificaciones 
                WHERE Leido = 0 
                AND (SucursalID = ? OR SucursalID = 0)";
    $stmtTotal = $con->prepare($queryTotal);
    $stmtTotal->bind_param("i", $sucursalID);
    $stmtTotal->execute();
    $total = $stmtTotal->get_result()->fetch_assoc()['total'];

    echo json_encode([
        'error' => false,
        'notificaciones' => $notificaciones,
        'total' => $total
    ]);

} catch (Exception $e) {
    echo json_encode([
        'error' => true,
        'message' => 'Error en la consulta: ' . $e->getMessage(),
        'notificaciones' => [],
        'total' => 0
    ]);
}
?> 