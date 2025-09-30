<?php
// Archivo de diagnóstico para identificar problemas con el inventario
header('Content-Type: application/json; charset=utf-8');

// Función para logging de debug
function debugLog($message, $data = []) {
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] DEBUG: $message";
    if (!empty($data)) {
        $logMessage .= " | Datos: " . json_encode($data, JSON_UNESCAPED_UNICODE);
    }
    error_log($logMessage);
}

try {
    debugLog("Iniciando diagnóstico del sistema de inventario");
    
    // 1. Verificar configuración de PHP
    $phpInfo = [
        'version' => PHP_VERSION,
        'memory_limit' => ini_get('memory_limit'),
        'max_execution_time' => ini_get('max_execution_time'),
        'max_input_vars' => ini_get('max_input_vars'),
        'post_max_size' => ini_get('post_max_size'),
        'upload_max_filesize' => ini_get('upload_max_filesize'),
        'error_reporting' => error_reporting(),
        'display_errors' => ini_get('display_errors')
    ];
    
    debugLog("Configuración de PHP", $phpInfo);
    
    // 2. Verificar conexión a la base de datos
    include_once 'db_connect.php';
    
    if (!$conn) {
        throw new Exception('No se pudo conectar a la base de datos');
    }
    
    $dbInfo = [
        'server_info' => mysqli_get_server_info($conn),
        'client_info' => mysqli_get_client_info($conn),
        'connection_status' => mysqli_stat($conn)
    ];
    
    debugLog("Información de la base de datos", $dbInfo);
    
    // 3. Verificar estructura de la tabla
    $tableCheck = mysqli_query($conn, "DESCRIBE InventariosStocks_Conteos");
    if (!$tableCheck) {
        throw new Exception('No se pudo acceder a la tabla InventariosStocks_Conteos: ' . mysqli_error($conn));
    }
    
    $columns = [];
    while ($row = mysqli_fetch_assoc($tableCheck)) {
        $columns[] = $row;
    }
    
    debugLog("Estructura de la tabla", $columns);
    
    // 4. Verificar datos POST recibidos
    $postInfo = [
        'method' => $_SERVER['REQUEST_METHOD'],
        'content_type' => $_SERVER['CONTENT_TYPE'] ?? 'no definido',
        'content_length' => $_SERVER['CONTENT_LENGTH'] ?? 'no definido',
        'post_keys' => array_keys($_POST),
        'post_count' => count($_POST)
    ];
    
    // Verificar si hay datos de inventario
    if (isset($_POST['IdBasedatos'])) {
        $postInfo['inventario_data'] = [
            'IdBasedatos_count' => is_array($_POST['IdBasedatos']) ? count($_POST['IdBasedatos']) : 'no es array',
            'sample_data' => array_slice($_POST['IdBasedatos'], 0, 3) // Primeros 3 elementos
        ];
    }
    
    debugLog("Datos POST recibidos", $postInfo);
    
    // 5. Probar una inserción simple
    $testQuery = "INSERT INTO InventariosStocks_Conteos (`ID_Prod_POS`, `Cod_Barra`, `Nombre_Prod`, `Fk_sucursal`, `Precio_Venta`, `Precio_C`, `Contabilizado`, `StockEnMomento`, `Diferencia`, `Sistema`, `AgregadoPor`, `ID_H_O_D`, `FechaInventario`, `Tipo_Ajuste`, `Anaquel`, `Repisa`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $testStmt = mysqli_prepare($conn, $testQuery);
    if (!$testStmt) {
        throw new Exception('Error al preparar consulta de prueba: ' . mysqli_error($conn));
    }
    
    // Datos de prueba
    $testData = [
        'TEST001', '123456789', 'Producto de Prueba', '1', 10.50, 8.00, 5, 5, 0, 'SISTEMA', 'DEBUG', '1', date('Y-m-d H:i:s'), 'AJUSTE', 'A1', 'R1'
    ];
    $testTypes = 'ssssddiiissssss';
    
    $bindResult = mysqli_stmt_bind_param($testStmt, $testTypes, ...$testData);
    if (!$bindResult) {
        throw new Exception('Error al enlazar parámetros de prueba: ' . mysqli_stmt_error($testStmt));
    }
    
    $executeResult = mysqli_stmt_execute($testStmt);
    if (!$executeResult) {
        throw new Exception('Error al ejecutar consulta de prueba: ' . mysqli_stmt_error($testStmt));
    }
    
    $affectedRows = mysqli_stmt_affected_rows($testStmt);
    mysqli_stmt_close($testStmt);
    
    debugLog("Prueba de inserción exitosa", ['filas_afectadas' => $affectedRows]);
    
    // 6. Limpiar datos de prueba
    mysqli_query($conn, "DELETE FROM InventariosStocks_Conteos WHERE ID_Prod_POS = 'TEST001'");
    
    $response = [
        'status' => 'success',
        'message' => 'Diagnóstico completado exitosamente',
        'data' => [
            'php_config' => $phpInfo,
            'database_info' => $dbInfo,
            'table_structure' => $columns,
            'post_data' => $postInfo,
            'test_insertion' => 'exitoso'
        ],
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
} catch (Exception $e) {
    debugLog("Error en diagnóstico", [
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
    
    $response = [
        'status' => 'error',
        'message' => 'Error en diagnóstico: ' . $e->getMessage(),
        'code' => 'DIAGNOSTIC_ERROR',
        'timestamp' => date('Y-m-d H:i:s')
    ];
} catch (Error $e) {
    debugLog("Error fatal en diagnóstico", [
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ]);
    
    $response = [
        'status' => 'error',
        'message' => 'Error fatal en diagnóstico: ' . $e->getMessage(),
        'code' => 'DIAGNOSTIC_FATAL_ERROR',
        'timestamp' => date('Y-m-d H:i:s')
    ];
} finally {
    if (isset($conn) && $conn) {
        mysqli_close($conn);
    }
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
?>
