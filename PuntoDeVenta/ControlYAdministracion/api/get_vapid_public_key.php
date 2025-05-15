<?php
header('Content-Type: application/json');

// Verificar si existen las claves VAPID
$configFile = '../config/vapid_keys.json';

if (!file_exists($configFile)) {
    echo json_encode([
        'success' => false,
        'message' => 'Las claves VAPID no existen. Por favor, ejecute primero generar_vapid_keys.php'
    ]);
    exit;
}

// Leer la clave pública
try {
    $keyData = json_decode(file_get_contents($configFile), true);
    
    if (!isset($keyData['publicKey'])) {
        throw new Exception('El archivo de configuración no contiene la clave pública');
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