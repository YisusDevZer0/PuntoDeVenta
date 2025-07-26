<?php
// Script directo para instalar el sistema de pedidos
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

try {
    // Incluir conexión a la base de datos
    include "../Controladores/db_connect.php";
    
    // Verificar conexión
    if (!isset($conn) || $conn === null) {
        throw new Exception("No se pudo establecer conexión con la base de datos");
    }
    
    echo json_encode([
        'status' => 'info',
        'message' => 'Instalando sistema de pedidos (versión directa)...'
    ]);
    
    // Primero eliminar tablas existentes si existen
    $tablesToDrop = [
        'producto_proveedor',
        'proveedores_pedidos', 
        'pedido_historial',
        'pedido_detalles',
        'pedidos'
    ];
    
    foreach ($tablesToDrop as $table) {
        try {
            $conn->query("DROP TABLE IF EXISTS `$table`");
        } catch (Exception $e) {
            // Ignorar errores si la tabla no existe
        }
    }
    
    // Consultas SQL directas
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
            INDEX idx_fecha_creacion (fecha_creacion)
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        
        // Tabla de proveedores para pedidos
        "CREATE TABLE IF NOT EXISTS proveedores_pedidos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(255) NOT NULL,
            contacto VARCHAR(255),
            telefono VARCHAR(50),
            email VARCHAR(255),
            direccion TEXT,
            activo BOOLEAN DEFAULT TRUE,
            fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_activo (activo)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        
        // Tabla de asignación de proveedores a productos
        "CREATE TABLE IF NOT EXISTS producto_proveedor (
            id INT AUTO_INCREMENT PRIMARY KEY,
            producto_id INT NOT NULL,
            proveedor_id INT NOT NULL,
            codigo_proveedor VARCHAR(100),
            precio_proveedor DECIMAL(10,2),
            tiempo_entrega_dias INT DEFAULT 7,
            activo BOOLEAN DEFAULT TRUE,
            UNIQUE KEY unique_producto_proveedor (producto_id, proveedor_id),
            INDEX idx_producto (producto_id),
            INDEX idx_proveedor (proveedor_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    ];
    
    $successCount = 0;
    $errorCount = 0;
    $errors = [];
    
    foreach ($queries as $query) {
        try {
            // Ejecutar la consulta
            if ($conn->query($query)) {
                $successCount++;
            } else {
                $errorCount++;
                $errors[] = "Error en consulta: " . $conn->error;
            }
        } catch (Exception $e) {
            $errorCount++;
            $errors[] = "Error en consulta: " . $e->getMessage();
        }
    }
    
    // Verificar que las tablas se crearon correctamente
    $tablesToCheck = ['pedidos', 'pedido_detalles', 'pedido_historial', 'proveedores_pedidos', 'producto_proveedor'];
    $tablesCreated = [];
    
    foreach ($tablesToCheck as $table) {
        $result = $conn->query("SHOW TABLES LIKE '$table'");
        if ($result && $result->num_rows > 0) {
            $tablesCreated[] = $table;
        }
    }
    
    // Resultado final
    if ($errorCount === 0 && count($tablesCreated) === count($tablesToCheck)) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Sistema de pedidos instalado correctamente (versión directa)',
            'details' => [
                'queries_executed' => $successCount,
                'tables_created' => $tablesCreated,
                'errors' => $errors
            ]
        ]);
    } else {
        echo json_encode([
            'status' => 'warning',
            'message' => 'Instalación completada con algunos errores (versión directa)',
            'details' => [
                'queries_executed' => $successCount,
                'queries_failed' => $errorCount,
                'tables_created' => $tablesCreated,
                'tables_missing' => array_diff($tablesToCheck, $tablesCreated),
                'errors' => $errors
            ]
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Error durante la instalación: ' . $e->getMessage()
    ]);
}
?> 