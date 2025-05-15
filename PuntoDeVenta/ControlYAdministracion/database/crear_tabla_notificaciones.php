<?php
// Habilitar visualización de errores para debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

try {
    // Incluir conexión
    include "../dbconect.php";
    
    // Verificar conexión
    if (!isset($con) || $con === null) {
        throw new Exception("No se pudo establecer conexión con la base de datos");
    }
    
    // Verificar si la tabla ya existe
    $checkTable = $con->query("SHOW TABLES LIKE 'Notificaciones'");
    
    if ($checkTable->num_rows > 0) {
        echo json_encode([
            'status' => 'info',
            'message' => 'La tabla Notificaciones ya existe en la base de datos'
        ]);
        exit;
    }
    
    // Crear la tabla Notificaciones
    $sql = "CREATE TABLE IF NOT EXISTS Notificaciones (
        ID_Notificacion INT AUTO_INCREMENT PRIMARY KEY,
        Tipo VARCHAR(50) NOT NULL COMMENT 'Tipo de notificación (inventario, caducidad, caja, etc)',
        Mensaje TEXT NOT NULL COMMENT 'Contenido de la notificación',
        Fecha DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha y hora de la notificación',
        SucursalID INT NOT NULL COMMENT 'ID de la sucursal destinataria (0 para todas)',
        Leido TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Estado de lectura (0=no leída, 1=leída)'
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    if ($con->query($sql) === TRUE) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Tabla Notificaciones creada correctamente'
        ]);
        
        // Insertar una notificación de prueba
        $mensaje = "Sistema de notificaciones instalado correctamente. Esta es una notificación de prueba.";
        $insertSql = "INSERT INTO Notificaciones (Tipo, Mensaje, SucursalID) VALUES ('sistema', ?, 0)";
        $stmt = $con->prepare($insertSql);
        $stmt->bind_param("s", $mensaje);
        $stmt->execute();
        
    } else {
        throw new Exception("Error al crear la tabla: " . $con->error);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?> 