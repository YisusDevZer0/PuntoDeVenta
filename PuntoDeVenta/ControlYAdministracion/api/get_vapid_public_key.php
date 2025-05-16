<?php
// Habilitar visualización de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Ruta del archivo de claves VAPID
$configFile = __DIR__ . '/../config/vapid_keys.json';

// Verificar si el archivo existe
if (!file_exists($configFile)) {
    echo json_encode([
        'success' => false,
        'message' => 'Las claves VAPID no existen. Por favor, ejecute primero el script de generación de claves.'
    ]);
    exit;
}

try {
    // Leer el archivo de claves
    $jsonData = file_get_contents($configFile);
    if (!$jsonData) {
        throw new Exception('No se pudieron leer las claves VAPID');
    }
    
    // Decodificar el JSON
    $keys = json_decode($jsonData, true);
    if (!$keys || !isset($keys['publicKey'])) {
        throw new Exception('Formato de claves inválido');
    }
    
    // Devolver la clave pública
    echo json_encode([
        'success' => true,
        'publicKey' => $keys['publicKey'],
        'format' => 'base64url'
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al leer las claves VAPID: ' . $e->getMessage()
    ]);
}
?> 