<?php
header('Content-Type: application/json');
include_once "../Controladores/db_connect.php";
include_once "../Controladores/ControladorUsuario.php";

// Verificar sesión
session_start();
if(!isset($_SESSION['VentasPos'])){
    echo json_encode(['success' => false, 'message' => 'Sesión no válida']);
    exit();
}

try {
    // Verificar si las tablas existen
    $tables = ['pedidos', 'pedido_detalles', 'pedido_historial'];
    $existing_tables = [];
    
    foreach ($tables as $table) {
        $result = mysqli_query($conn, "SHOW TABLES LIKE '$table'");
        if (mysqli_num_rows($result) > 0) {
            $existing_tables[] = $table;
        }
    }
    
    if (count($existing_tables) === count($tables)) {
        echo json_encode([
            'success' => true, 
            'message' => 'Todas las tablas ya existen',
            'existing_tables' => $existing_tables
        ]);
        exit();
    }
    
    // Crear tablas que no existen
    $queries = [
        // Tabla principal de pedidos
        "CREATE TABLE IF NOT EXISTS pedidos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            folio VARCHAR(50) UNIQUE NOT NULL,
            sucursal_id INT NOT NULL,
            usuario_id INT NOT NULL,
            estado ENUM('pendiente', 'aprobado', 'rechazado', 'en_proceso', 'completado', 'cancelado') DEFAULT 'pendiente',
            prioridad ENUM('baja', 'normal', 'alta', 'urgente') DEFAULT 'normal',
            tipo_origen ENUM('admin', 'farmacia', 'cedis', 'sucursal') DEFAULT 'admin',
            observaciones TEXT,
            fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
            fecha_aprobacion DATETIME NULL,
            fecha_completado DATETIME NULL,
            aprobado_por INT NULL,
            total_estimado DECIMAL(10,2) DEFAULT 0.00,
            INDEX idx_sucursal (sucursal_id),
            INDEX idx_estado (estado),
            INDEX idx_fecha_creacion (fecha_creacion),
            INDEX idx_tipo_origen (tipo_origen)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        
        // Tabla de detalles de pedidos
        "CREATE TABLE IF NOT EXISTS pedido_detalles (
            id INT AUTO_INCREMENT PRIMARY KEY,
            pedido_id INT NOT NULL,
            producto_id INT NOT NULL,
            cantidad_solicitada DECIMAL(10,2) NOT NULL,
            cantidad_aprobada DECIMAL(10,2) NULL,
            cantidad_recibida DECIMAL(10,2) DEFAULT 0.00,
            precio_unitario DECIMAL(10,2) NULL,
            subtotal DECIMAL(10,2) DEFAULT 0.00,
            observaciones TEXT,
            estado ENUM('pendiente', 'aprobado', 'rechazado', 'recibido') DEFAULT 'pendiente',
            INDEX idx_pedido (pedido_id),
            INDEX idx_producto (producto_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        
        // Tabla de historial de cambios de estado
        "CREATE TABLE IF NOT EXISTS pedido_historial (
            id INT AUTO_INCREMENT PRIMARY KEY,
            pedido_id INT NOT NULL,
            usuario_id INT NOT NULL,
            estado_anterior VARCHAR(50),
            estado_nuevo VARCHAR(50),
            comentario TEXT,
            fecha_cambio DATETIME DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_pedido_fecha (pedido_id, fecha_cambio)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    ];
    
    $created_tables = [];
    $errors = [];
    
    foreach ($queries as $query) {
        if (mysqli_query($conn, $query)) {
            // Extraer nombre de tabla de la query
            preg_match('/CREATE TABLE IF NOT EXISTS (\w+)/', $query, $matches);
            if (isset($matches[1])) {
                $created_tables[] = $matches[1];
            }
        } else {
            $errors[] = mysqli_error($conn);
        }
    }
    
    if (empty($errors)) {
        echo json_encode([
            'success' => true,
            'message' => 'Tablas creadas exitosamente',
            'created_tables' => $created_tables,
            'existing_tables' => $existing_tables
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Error al crear algunas tablas',
            'errors' => $errors,
            'created_tables' => $created_tables
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
