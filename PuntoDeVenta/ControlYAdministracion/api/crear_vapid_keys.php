<?php
// Habilitar visualización de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Método simple para crear claves VAPID - no usa OpenSSL directamente
function generateSimpleVAPIDKeys() {
    // Clave privada: 32 bytes aleatorios
    $privateBytes = random_bytes(32);
    
    // Crear clave pública (simulada para pruebas - en producción usar ECDH real)
    // Esto es solo para pruebas - no usar en producción
    $publicBytes = "\x04" . random_bytes(64);
    
    // Convertir a base64url
    $publicKey = rtrim(strtr(base64_encode($publicBytes), '+/', '-_'), '=');
    $privateKey = rtrim(strtr(base64_encode($privateBytes), '+/', '-_'), '=');
    
    return [
        'publicKey' => $publicKey,
        'privateKey' => $privateKey
    ];
}

// Crear el directorio si no existe
$configDir = '../config';
$configFile = $configDir . '/vapid_keys.json';

if (!file_exists($configDir)) {
    mkdir($configDir, 0755, true);
}

// Generar las claves
try {
    $keys = generateSimpleVAPIDKeys();
    
    // Guardar las claves
    $data = [
        'publicKey' => $keys['publicKey'],
        'privateKey' => $keys['privateKey'],
        'subject' => 'mailto:admin@doctorpez.mx',
        'created' => date('Y-m-d H:i:s'),
        'simulated' => true // Marcador para identificar claves simuladas
    ];
    
    file_put_contents($configFile, json_encode($data, JSON_PRETTY_PRINT));
    
    echo json_encode([
        'success' => true,
        'message' => 'Claves VAPID generadas correctamente (método alternativo)',
        'publicKey' => $keys['publicKey']
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al generar claves: ' . $e->getMessage()
    ]);
}
?> 