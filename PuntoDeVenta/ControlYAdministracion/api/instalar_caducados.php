<?php
header('Content-Type: application/json');
include_once "../dbconect.php";

try {
    // Leer el archivo SQL
    $sqlFile = "../database/caducados_tables.sql";
    
    if (!file_exists($sqlFile)) {
        throw new Exception("El archivo SQL no existe: " . $sqlFile);
    }
    
    $sql = file_get_contents($sqlFile);
    
    if (empty($sql)) {
        throw new Exception("El archivo SQL está vacío");
    }
    
    // Dividir el SQL en declaraciones individuales
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    $tablasCreadas = 0;
    $errores = [];
    
    foreach ($statements as $statement) {
        if (empty($statement) || strpos($statement, '--') === 0) {
            continue; // Saltar comentarios y líneas vacías
        }
        
        try {
            if ($con->query($statement)) {
                if (stripos($statement, 'CREATE TABLE') !== false) {
                    $tablasCreadas++;
                }
            } else {
                $errores[] = "Error en: " . substr($statement, 0, 50) . "... - " . $con->error;
            }
        } catch (Exception $e) {
            $errores[] = "Error en: " . substr($statement, 0, 50) . "... - " . $e->getMessage();
        }
    }
    
    if (count($errores) > 0) {
        echo json_encode([
            'success' => false,
            'error' => 'Se encontraron errores durante la instalación',
            'errores' => $errores,
            'tablas_creadas' => $tablasCreadas
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'message' => "Instalación completada exitosamente. Se crearon $tablasCreadas tablas.",
            'tablas_creadas' => $tablasCreadas
        ]);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} finally {
    if (isset($con)) {
        $con->close();
    }
}
?>
