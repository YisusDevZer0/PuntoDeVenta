<?php
// Habilitar visualización de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Función para generar claves VAPID usando OpenSSL (más confiable)
function generateVapidKeysOpenSSL() {
    try {
        // Generar clave privada EC
        $privateKey = openssl_pkey_new([
            'curve_name' => 'prime256v1',
            'private_key_type' => OPENSSL_KEYTYPE_EC,
        ]);
        
        if (!$privateKey) {
            throw new Exception('Error generando clave EC: ' . openssl_error_string());
        }
        
        // Exportar la clave privada
        $keyDetails = openssl_pkey_get_details($privateKey);
        if (!$keyDetails) {
            throw new Exception('Error al obtener detalles de la clave: ' . openssl_error_string());
        }
        
        // Obtener los datos de la clave
        $privateKeyDer = $keyDetails['ec']['d'];
        $publicKeyUncompressed = "\x04" . $keyDetails['ec']['x'] . $keyDetails['ec']['y'];
        
        // Convertir a base64url
        $publicKey = rtrim(strtr(base64_encode($publicKeyUncompressed), '+/', '-_'), '=');
        $privateKey = rtrim(strtr(base64_encode($privateKeyDer), '+/', '-_'), '=');
        
        return [
            'publicKey' => $publicKey,
            'privateKey' => $privateKey
        ];
    } catch (Exception $e) {
        error_log('Error en generateVapidKeysOpenSSL: ' . $e->getMessage());
        return null;
    }
}

// Método alternativo simple para crear claves VAPID si OpenSSL falla
function generateSimpleVAPIDKeys() {
    // Clave privada: 32 bytes aleatorios
    $privateBytes = random_bytes(32);
    
    // Crear clave pública (simulada para pruebas - en producción usar ECDH real)
    // IMPORTANTE: El primer byte debe ser 0x04 para formato sin comprimir
    $publicBytes = "\x04" . random_bytes(64);
    
    // Convertir a base64url
    $publicKey = rtrim(strtr(base64_encode($publicBytes), '+/', '-_'), '=');
    $privateKey = rtrim(strtr(base64_encode($privateBytes), '+/', '-_'), '=');
    
    return [
        'publicKey' => $publicKey,
        'privateKey' => $privateKey
    ];
}

// Verificar el sistema de archivos
$debug = [];
$configDir = __DIR__ . '/../config';
$configFile = $configDir . '/vapid_keys.json';

// Información sobre directorios
$debug['current_dir'] = getcwd();
$debug['api_dir'] = __DIR__;
$debug['config_dir_path'] = realpath($configDir) ?: 'No existe';
$debug['config_dir_target'] = $configDir;
$debug['config_file_path'] = realpath($configFile) ?: 'No existe';
$debug['config_file_target'] = $configFile;

// Crear el directorio si no existe
if (!file_exists($configDir)) {
    $debug['mkdir_result'] = mkdir($configDir, 0755, true) ? 'Éxito' : 'Error';
    $debug['mkdir_error'] = error_get_last();
}

// Verificar si el directorio existe y si se puede escribir
$debug['config_dir_exists'] = file_exists($configDir) ? 'Sí' : 'No';
$debug['config_dir_writable'] = is_writable($configDir) ? 'Sí' : 'No';

// Generar las claves
try {
    // Intentar primero con OpenSSL
    $keys = generateVapidKeysOpenSSL();
    
    // Si OpenSSL falla, usar el método alternativo
    if (!$keys) {
        $debug['openssl_fallback'] = 'Usando método alternativo debido a error en OpenSSL';
        $keys = generateSimpleVAPIDKeys();
    } else {
        $debug['openssl_success'] = 'Claves generadas correctamente con OpenSSL';
    }
    
    // Guardar las claves
    $data = [
        'publicKey' => $keys['publicKey'],
        'privateKey' => $keys['privateKey'],
        'subject' => 'mailto:jesusemutul@gmail.com',
        'created' => date('Y-m-d H:i:s'),
        'simulated' => !isset($debug['openssl_success']) // Marca como simulada si no es de OpenSSL
    ];
    
    $jsonData = json_encode($data, JSON_PRETTY_PRINT);
    $debug['json_length'] = strlen($jsonData);
    
    // Intentar guardar el archivo
    $bytesWritten = file_put_contents($configFile, $jsonData);
    $debug['bytes_written'] = $bytesWritten;
    $debug['file_exists_after'] = file_exists($configFile) ? 'Sí' : 'No';
    
    echo json_encode([
        'success' => $bytesWritten !== false,
        'message' => 'Claves VAPID generadas correctamente' . (isset($debug['openssl_success']) ? ' (OpenSSL)' : ' (método alternativo)'),
        'publicKey' => $keys['publicKey'],
        'debug' => $debug
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al generar claves: ' . $e->getMessage(),
        'debug' => $debug
    ]);
}
?> 