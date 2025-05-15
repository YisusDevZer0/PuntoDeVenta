<?php
// Habilitar visualización de errores para debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

try {
    // Intentar incluir la conexión a la base de datos
    include "../database/db_connect.php";
    
    // Verificar si la conexión existe
    if (!isset($conn) || $conn === null) {
        throw new Exception('No se encontró la variable de conexión $conn');
    }
    
    // Verificar si la conexión está activa
    if ($conn->ping()) {
        // Verificar si la tabla Notificaciones existe
        $checkTable = $conn->query("SHOW TABLES LIKE 'Notificaciones'");
        $tableExists = $checkTable->num_rows > 0;
        
        if ($tableExists) {
            // Obtener información de la tabla
            $tableInfo = $conn->query("DESCRIBE Notificaciones");
            $fields = [];
            while ($field = $tableInfo->fetch_assoc()) {
                $fields[] = $field['Field'];
            }
            
            // Obtener total de registros
            $totalQuery = $conn->query("SELECT COUNT(*) as total FROM Notificaciones");
            $totalRecords = $totalQuery->fetch_assoc()['total'];
            
            echo json_encode([
                'status' => 'success',
                'message' => 'Conexión exitosa a la base de datos',
                'database_info' => [
                    'table_exists' => true,
                    'fields' => $fields,
                    'total_records' => $totalRecords
                ]
            ]);
        } else {
            echo json_encode([
                'status' => 'warning',
                'message' => 'Conexión exitosa, pero la tabla Notificaciones no existe',
                'database_info' => [
                    'table_exists' => false
                ]
            ]);
        }
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'No se pudo verificar la conexión (ping fallido)'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Error: ' . $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
}
?> 