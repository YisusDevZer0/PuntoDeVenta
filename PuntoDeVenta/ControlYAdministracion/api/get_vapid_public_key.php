<?php
// Habilitar visualización de errores para debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Verificar si existen las claves VAPID
$configFile = '../config/vapid_keys.json';

if (!file_exists($configFile)) {
    // Archivo no existe, intentamos ejecutar generar_vapid_keys.php
    try {
        // Cargar el script de generación de claves (esto generará las claves si no existen)
        include 'generar_vapid_keys.php';
        
        // Si llegamos aquí, las claves deberían existir ahora, verificamos de nuevo
        if (!file_exists($configFile)) {
            throw new Exception('No se pudieron generar automáticamente las claves VAPID');
        }
    } catch (Exception $e) {
        // Falló la generación automática
        echo json_encode([
            'success' => false,
            'message' => 'Error generando claves VAPID: ' . $e->getMessage(),
            'help' => 'Ejecute manualmente generar_vapid_keys.php'
        ]);
        exit;
    }
}

// Leer la clave pública
try {
    $keyData = json_decode(file_get_contents($configFile), true);
    
    if (!$keyData) {
        throw new Exception('Error decodificando el archivo JSON de claves VAPID: ' . json_last_error_msg());
    }
    
    if (!isset($keyData['publicKey'])) {
        throw new Exception('El archivo de configuración no contiene la clave pública');
    }
    
    // Verificar que la clave pública no esté vacía
    if (empty($keyData['publicKey'])) {
        throw new Exception('La clave pública está vacía');
    }
    
    echo json_encode([
        'success' => true,
        'publicKey' => $keyData['publicKey']
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al leer la clave pública: ' . $e->getMessage()
    ]);
}
?> 