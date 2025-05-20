<?php
require_once '../Controladores/db_connect.php';

header('Content-Type: application/json');

try {
    $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Verificar si la tabla existe
    $stmt = $db->query("SHOW TABLES LIKE 'notificaciones'");
    $tableExists = $stmt->rowCount() > 0;
    
    if (!$tableExists) {
        // Crear la tabla si no existe
        $db->exec("
            CREATE TABLE IF NOT EXISTS notificaciones (
                ID_Notificacion INT AUTO_INCREMENT PRIMARY KEY,
                ID_Usuario INT NOT NULL,
                Tipo VARCHAR(50) NOT NULL,
                Mensaje TEXT NOT NULL,
                Fecha_Creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
                ID_Sucursal INT,
                Leida TINYINT(1) DEFAULT 0,
                FOREIGN KEY (ID_Usuario) REFERENCES usuarios(ID_Usuario),
                FOREIGN KEY (ID_Sucursal) REFERENCES sucursales(ID_Sucursal)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
        
        echo json_encode([
            'success' => true,
            'message' => 'Tabla de notificaciones creada correctamente'
        ]);
    } else {
        // Verificar la estructura de la tabla
        $stmt = $db->query("DESCRIBE notificaciones");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'message' => 'Tabla de notificaciones existe',
            'structure' => $columns
        ]);
    }
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al verificar tabla de notificaciones: ' . $e->getMessage()
    ]);
} 