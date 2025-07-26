<?php
// Script para limpiar e instalar el sistema de pedidos - Versión simple
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
        'message' => 'Limpiando e instalando sistema de pedidos (versión simple)...'
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
    
    // Leer el archivo SQL simple
    $sqlFile = __DIR__ . '/pedidos_schema_simple.sql';
    
    if (!file_exists($sqlFile)) {
        throw new Exception("No se encontró el archivo pedidos_schema_simple.sql");
    }
    
    $sqlContent = file_get_contents($sqlFile);
    
    // Dividir el contenido en consultas individuales
    $queries = explode(';', $sqlContent);
    
    $successCount = 0;
    $errorCount = 0;
    $errors = [];
    
    foreach ($queries as $query) {
        $query = trim($query);
        
        if (empty($query) || strpos($query, '--') === 0) {
            continue; // Saltar comentarios y líneas vacías
        }
        
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
            'message' => 'Sistema de pedidos instalado correctamente (versión simple)',
            'details' => [
                'queries_executed' => $successCount,
                'tables_created' => $tablesCreated,
                'errors' => $errors
            ]
        ]);
    } else {
        echo json_encode([
            'status' => 'warning',
            'message' => 'Instalación completada con algunos errores (versión simple)',
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