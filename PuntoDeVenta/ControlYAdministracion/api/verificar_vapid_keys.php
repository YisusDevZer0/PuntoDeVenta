<?php
// Habilitar visualización de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Definir rutas
$configDir = '../config';
$configFile = $configDir . '/vapid_keys.json';

// Función para generar claves VAPID usando la librería web-push si está disponible
function generateVAPIDKeysWithLibrary() {
    if (class_exists('Minishlink\WebPush\VAPID')) {
        try {
            return \Minishlink\WebPush\VAPID::createVapidKeys();
        } catch (Exception $e) {
            return null;
        }
    }
    return null;
}

// Función alternativa para generar claves VAPID usando OpenSSL
function generateVAPIDKeysWithOpenSSL() {
    try {
        // Generar clave privada ECC
        $privateKey = openssl_pkey_new([
            'curve_name' => 'prime256v1',
            'private_key_type' => OPENSSL_KEYTYPE_EC,
        ]);
        
        if (!$privateKey) {
            throw new Exception('Error al generar clave privada: ' . openssl_error_string());
        }
        
        // Extraer detalles de la clave
        $keyDetails = openssl_pkey_get_details($privateKey);
        
        if (!$keyDetails) {
            throw new Exception('Error al obtener detalles de la clave: ' . openssl_error_string());
        }
        
        $publicKey = $keyDetails['key'];
        
        // Convertir a formato URL-safe base64 según especificación VAPID
        $publicKeyBase64 = base64_encode($publicKey);
        $privateKeyBase64 = "";
        
        // Extraer la clave privada
        if (openssl_pkey_export($privateKey, $privateKeyPEM)) {
            $privateKeyBase64 = base64_encode($privateKeyPEM);
        } else {
            throw new Exception('Error al exportar clave privada: ' . openssl_error_string());
        }
        
        // Convertir a URL-safe base64
        $publicKeyUrlSafe = str_replace(['+', '/'], ['-', '_'], $publicKeyBase64);
        $privateKeyUrlSafe = str_replace(['+', '/'], ['-', '_'], $privateKeyBase64);
        
        return [
            'publicKey' => $publicKeyUrlSafe,
            'privateKey' => $privateKeyUrlSafe
        ];
    } catch (Exception $e) {
        return null;
    }
}

// Función para analizar y reparar el directorio config y las claves
function diagnoseAndFixVapidKeys() {
    global $configDir, $configFile;
    
    $result = [
        'success' => false,
        'diagnostics' => [],
        'actions_taken' => [],
    ];
    
    // 1. Verificar si el directorio config existe
    if (!file_exists($configDir)) {
        $result['diagnostics'][] = "El directorio config no existe";
        
        // Intentar crear el directorio
        if (mkdir($configDir, 0755, true)) {
            $result['actions_taken'][] = "Se creó el directorio config";
        } else {
            $result['diagnostics'][] = "No se pudo crear el directorio config";
            return $result;
        }
    } else {
        $result['diagnostics'][] = "El directorio config existe";
    }
    
    // 2. Verificar si el archivo de claves existe
    if (!file_exists($configFile)) {
        $result['diagnostics'][] = "El archivo de claves VAPID no existe";
        
        // Intentar generar nuevas claves
        $vapidKeys = generateVAPIDKeysWithLibrary();
        
        if (!$vapidKeys) {
            $vapidKeys = generateVAPIDKeysWithOpenSSL();
        }
        
        if ($vapidKeys) {
            // Guardar claves en archivo de configuración
            $keyData = [
                'publicKey' => $vapidKeys['publicKey'],
                'privateKey' => $vapidKeys['privateKey'],
                'subject' => 'mailto:admin@doctorpez.mx' // Cambiar por tu email
            ];
            
            if (file_put_contents($configFile, json_encode($keyData, JSON_PRETTY_PRINT))) {
                $result['actions_taken'][] = "Se generaron y guardaron nuevas claves VAPID";
                $result['success'] = true;
            } else {
                $result['diagnostics'][] = "No se pudieron guardar las claves VAPID";
            }
        } else {
            $result['diagnostics'][] = "No se pudieron generar nuevas claves VAPID";
        }
    } else {
        $result['diagnostics'][] = "El archivo de claves VAPID existe";
        
        // 3. Verificar si el archivo es válido
        $fileContent = file_get_contents($configFile);
        if (!$fileContent) {
            $result['diagnostics'][] = "No se pudo leer el archivo de claves VAPID";
            return $result;
        }
        
        $keyData = json_decode($fileContent, true);
        if (!$keyData) {
            $result['diagnostics'][] = "El archivo de claves VAPID no contiene JSON válido";
            
            // Intentar regenerar claves
            $result['actions_taken'][] = "Regenerando claves debido a JSON inválido";
            unlink($configFile);
            return diagnoseAndFixVapidKeys(); // Llamada recursiva después de eliminar
        }
        
        // 4. Verificar si contiene las claves necesarias
        $keysOk = true;
        if (!isset($keyData['publicKey']) || empty($keyData['publicKey'])) {
            $result['diagnostics'][] = "Falta la clave pública o está vacía";
            $keysOk = false;
        }
        
        if (!isset($keyData['privateKey']) || empty($keyData['privateKey'])) {
            $result['diagnostics'][] = "Falta la clave privada o está vacía";
            $keysOk = false;
        }
        
        if (!isset($keyData['subject']) || empty($keyData['subject'])) {
            $result['diagnostics'][] = "Falta el subject o está vacío";
            $keyData['subject'] = 'mailto:admin@doctorpez.mx';
            $result['actions_taken'][] = "Se agregó un subject predeterminado";
            $keysOk = false;
        }
        
        if (!$keysOk) {
            // Regenerar claves
            $result['actions_taken'][] = "Regenerando claves debido a claves incompletas";
            unlink($configFile);
            return diagnoseAndFixVapidKeys(); // Llamada recursiva después de eliminar
        } else {
            $result['diagnostics'][] = "Las claves VAPID parecen válidas";
            $result['success'] = true;
        }
    }
    
    return $result;
}

// Ejecutar diagnóstico y obtener resultados
$results = diagnoseAndFixVapidKeys();
echo json_encode($results, JSON_PRETTY_PRINT);
?> 