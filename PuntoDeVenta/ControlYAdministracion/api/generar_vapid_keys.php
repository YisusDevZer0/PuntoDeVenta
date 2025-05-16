<?php
// Habilitar visualización de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Configurar tiempo de ejecución máximo
set_time_limit(60);

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

// Verificar si OpenSSL está disponible
if (!function_exists('openssl_pkey_new')) {
    echo json_encode([
        'success' => false,
        'message' => 'OpenSSL no está instalado o habilitado en este servidor'
    ]);
    exit;
}

// Verificar si ya existen claves
$configDir = '../config';
$configFile = $configDir . '/vapid_keys.json';

// Crear directorio si no existe
if (!file_exists($configDir)) {
    if (!mkdir($configDir, 0755, true)) {
        echo json_encode([
            'success' => false,
            'message' => 'No se pudo crear el directorio de configuración: ' . $configDir
        ]);
        exit;
    }
}

// Verificar permisos de escritura
if (!is_writable($configDir)) {
    echo json_encode([
        'success' => false,
        'message' => 'No se puede escribir en el directorio de configuración: ' . $configDir
    ]);
    exit;
}

// Generar claves VAPID (forzar regeneración si se solicita)
$forceRegenerate = isset($_GET['force']) && $_GET['force'] == '1';

if (!file_exists($configFile) || $forceRegenerate) {
    try {
        $vapidKeys = generateCorrectVAPIDKeys();
        
        // Guardar claves en archivo de configuración
        $keyData = [
            'publicKey' => $vapidKeys['publicKey'],
            'privateKey' => $vapidKeys['privateKey'],
            'subject' => 'mailto:admin@doctorpez.mx', // Cambiar por tu email
            'created' => date('Y-m-d H:i:s'),
            'format' => 'base64url_nopadding'
        ];
        
        $jsonData = json_encode($keyData, JSON_PRETTY_PRINT);
        
        if ($jsonData === false) {
            throw new Exception('Error al codificar JSON: ' . json_last_error_msg());
        }
        
        $bytesWritten = file_put_contents($configFile, $jsonData);
        
        if ($bytesWritten === false) {
            throw new Exception('Error al escribir en el archivo: ' . $configFile);
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Claves VAPID generadas correctamente',
            'keys' => [
                'publicKey' => $vapidKeys['publicKey']
            ],
            'bytes_written' => $bytesWritten
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error al generar claves VAPID: ' . $e->getMessage()
        ]);
    }
} else {
    // Las claves ya existen
    try {
        $fileContent = file_get_contents($configFile);
        
        if ($fileContent === false) {
            throw new Exception('No se pudo leer el archivo de claves');
        }
        
        $keyData = json_decode($fileContent, true);
        
        if ($keyData === null) {
            throw new Exception('Error al decodificar JSON de claves: ' . json_last_error_msg());
        }
        
        if (!isset($keyData['publicKey']) || !isset($keyData['privateKey'])) {
            throw new Exception('El archivo de claves no contiene las claves necesarias');
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Las claves VAPID ya existen',
            'keys' => [
                'publicKey' => $keyData['publicKey']
            ]
        ]);
    } catch (Exception $e) {
        // Si hay error al leer/validar claves existentes, intentar regenerar
        try {
            // Intentar eliminar el archivo corrupto
            if (file_exists($configFile)) {
                unlink($configFile);
            }
            
            $vapidKeys = generateCorrectVAPIDKeys();
            
            // Guardar nuevas claves
            $keyData = [
                'publicKey' => $vapidKeys['publicKey'],
                'privateKey' => $vapidKeys['privateKey'],
                'subject' => 'mailto:admin@doctorpez.mx',
                'created' => date('Y-m-d H:i:s'),
                'format' => 'base64url_nopadding',
                'regenerated' => true,
                'reason' => $e->getMessage()
            ];
            
            file_put_contents($configFile, json_encode($keyData, JSON_PRETTY_PRINT));
            
            echo json_encode([
                'success' => true,
                'message' => 'Se regeneraron las claves VAPID debido a problemas con las anteriores',
                'previous_error' => $e->getMessage(),
                'keys' => [
                    'publicKey' => $vapidKeys['publicKey']
                ]
            ]);
        } catch (Exception $e2) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al regenerar claves VAPID: ' . $e2->getMessage(),
                'previous_error' => $e->getMessage()
            ]);
        }
    }
}
?> 