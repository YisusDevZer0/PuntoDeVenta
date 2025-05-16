<?php
// Habilitar visualización de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Definir rutas
$configDir = '../config';
$configFile = $configDir . '/vapid_keys.json';

// Función para generar claves VAPID en formato correcto para Web Push
function generateCorrectVAPIDKeys() {
    // Generar clave privada ECC usando OpenSSL
    $privateKey = openssl_pkey_new([
        'curve_name' => 'prime256v1',
        'private_key_type' => OPENSSL_KEYTYPE_EC,
    ]);
    
    if (!$privateKey) {
        throw new Exception('Error al generar clave privada: ' . openssl_error_string());
    }
    
    // Extraer detalles de la clave
    $keyDetails = openssl_pkey_get_details($privateKey);
    
    // Verificar que tenemos los componentes necesarios
    if (!isset($keyDetails['ec']['x']) || !isset($keyDetails['ec']['y']) || !isset($keyDetails['ec']['d'])) {
        throw new Exception('No se pudieron extraer los componentes necesarios de la clave');
    }
    
    // Extraer las partes x e y de la clave pública y d de la privada
    $x = $keyDetails['ec']['x'];
    $y = $keyDetails['ec']['y'];
    $d = $keyDetails['ec']['d'];
    
    // La clave pública en formato no comprimido: 0x04 + x + y
    $publicKeyRaw = "\x04" . $x . $y;
    $privateKeyRaw = $d;
    
    // Convertir a base64url
    $publicKey = rtrim(strtr(base64_encode($publicKeyRaw), '+/', '-_'), '=');
    $privateKey = rtrim(strtr(base64_encode($privateKeyRaw), '+/', '-_'), '=');
    
    return [
        'publicKey' => $publicKey,
        'privateKey' => $privateKey
    ];
}

// Verificar claves existentes
if (file_exists($configFile)) {
    $keyData = json_decode(file_get_contents($configFile), true);
    
    echo json_encode([
        'success' => true,
        'message' => 'Información de claves VAPID existentes',
        'format' => 'Las claves VAPID deben estar en formato base64url sin padding',
        'keys' => [
            'publicKey' => $keyData['publicKey'],
            'publicKey_length' => strlen($keyData['publicKey']),
            'privateKey_length' => strlen($keyData['privateKey']),
            'subject' => $keyData['subject'] ?? 'No definido'
        ],
        'conversion_test' => [
            'description' => 'Prueba de conversión en PHP',
            'publicKey_raw' => base64_decode(strtr($keyData['publicKey'], '-_', '+/') . str_repeat('=', (4 - (strlen($keyData['publicKey']) % 4)) % 4)),
            'publicKey_raw_length' => strlen(base64_decode(strtr($keyData['publicKey'], '-_', '+/') . str_repeat('=', (4 - (strlen($keyData['publicKey']) % 4)) % 4))),
            'first_byte_should_be_4' => '0x' . bin2hex(substr(base64_decode(strtr($keyData['publicKey'], '-_', '+/') . str_repeat('=', (4 - (strlen($keyData['publicKey']) % 4)) % 4)), 0, 1))
        ]
    ]);
    
    exit;
}

// Si no existen claves, generar nuevas en formato correcto
try {
    $vapidKeys = generateCorrectVAPIDKeys();
    
    // Guardar claves en archivo de configuración
    $keyData = [
        'publicKey' => $vapidKeys['publicKey'],
        'privateKey' => $vapidKeys['privateKey'],
        'subject' => 'mailto:admin@doctorpez.mx',
        'created' => date('Y-m-d H:i:s'),
        'format' => 'base64url_nopadding'
    ];
    
    if (!file_exists($configDir)) {
        mkdir($configDir, 0755, true);
    }
    
    file_put_contents($configFile, json_encode($keyData, JSON_PRETTY_PRINT));
    
    echo json_encode([
        'success' => true,
        'message' => 'Nuevas claves VAPID generadas correctamente',
        'keys' => [
            'publicKey' => $vapidKeys['publicKey'],
            'publicKey_length' => strlen($vapidKeys['publicKey'])
        ]
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al generar claves VAPID: ' . $e->getMessage()
    ]);
}
?> 