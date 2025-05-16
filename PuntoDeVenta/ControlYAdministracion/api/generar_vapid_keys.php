<?php
// Habilitar visualización de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Función para generar claves VAPID usando OpenSSL (recomendado)
function generateVAPIDKeys() {
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
    $privateKeyPEM = $keyDetails['key'];
    
    // Extraer la clave pública
    $publicKey = $keyDetails['ec']['x'] . $keyDetails['ec']['y'];
    
    // Convertir a formato URL-safe base64 según especificación VAPID
    $publicKeyBase64 = base64_encode($publicKey);
    $privateKeyBase64 = base64_encode($privateKeyPEM);
    
    // Convertir a URL-safe base64
    $publicKeyUrlSafe = str_replace(['+', '/'], ['-', '_'], $publicKeyBase64);
    $privateKeyUrlSafe = str_replace(['+', '/'], ['-', '_'], $privateKeyBase64);
    
    return [
        'publicKey' => $publicKeyUrlSafe,
        'privateKey' => $privateKeyUrlSafe
    ];
}

// Verificar si ya existen claves
$configFile = '../config/vapid_keys.json';
$configDir = dirname($configFile);

// Crear directorio si no existe
if (!file_exists($configDir)) {
    if (!mkdir($configDir, 0755, true)) {
        echo json_encode([
            'success' => false,
            'message' => 'No se pudo crear el directorio de configuración'
        ]);
        exit;
    }
}

// Generar claves VAPID si no existen
if (!file_exists($configFile)) {
    try {
        if (!function_exists('openssl_pkey_new')) {
            throw new Exception('OpenSSL no está instalado o habilitado en este servidor');
        }
        
        $vapidKeys = generateVAPIDKeys();
        
        // Guardar claves en archivo de configuración
        $keyData = [
            'publicKey' => $vapidKeys['publicKey'],
            'privateKey' => $vapidKeys['privateKey'],
            'subject' => 'mailto:admin@doctorpez.mx' // Cambiar por tu email
        ];
        
        file_put_contents($configFile, json_encode($keyData, JSON_PRETTY_PRINT));
        
        echo json_encode([
            'success' => true,
            'message' => 'Claves VAPID generadas correctamente',
            'keys' => [
                'publicKey' => $vapidKeys['publicKey']
            ]
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error al generar claves VAPID: ' . $e->getMessage()
        ]);
    }
} else {
    // Las claves ya existen
    $keyData = json_decode(file_get_contents($configFile), true);
    echo json_encode([
        'success' => true,
        'message' => 'Las claves VAPID ya existen',
        'keys' => [
            'publicKey' => $keyData['publicKey']
        ]
    ]);
}
?> 